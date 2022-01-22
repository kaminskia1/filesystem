<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class ArcherCreateUserCommand
 *
 * @package App\Command
 */
class CreateUserCommand extends Command
{
    /**
     * @var string Command name
     */
    protected static $defaultName = 'make:fs-user';

    /**
     * @var string Log name
     */
    public $logName = 'CreateUser';

    /**
     * @var EntityManagerInterface Entity Manager instance
     */
    private $entityManager;

    /**
     * @var UserPasswordEncoderInterface Password Encoder instance
     */
    private $passwordEncoder;

    /**
     * Command constructor.
     *
     * @param EntityManagerInterface       $entityManager
     * @param UserPasswordEncoderInterface $passwordEncoder
     */
    public function __construct(EntityManagerInterface $entityManager, UserPasswordEncoderInterface $passwordEncoder)
    {
        // Import EntityManager and PasswordEncoder
        $this->entityManager = $entityManager;
        $this->passwordEncoder = $passwordEncoder;

        // Call the parent command constructor
        parent::__construct();
    }

    /**
     * Command configuration
     */
    protected function configure()
    {
        $this
            ->setDescription('Create a new user')
            ->addArgument('email', InputArgument::OPTIONAL, 'An Email to use')
            ->addArgument('password', InputArgument::OPTIONAL, 'The password to encode');
    }

    /**
     * Command execution
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // Create SymfonyStyle IO instance
        $io = new SymfonyStyle($input, $output);

        // Prompt for password, nickname if applicable, and custom role to bind if applicable
        $email = $input->getArgument('email') ?? $io->ask("Enter an email");
        $password = $input->getArgument('password') ?? $io->ask("Enter a password");

        // Instantiate new User instance
        $user = new User();
        $user->setEmail($email);

        // Encode with the server's standard encoding
        $user->setPassword(
            $this->passwordEncoder->encodePassword($user, $password)
        );


        // Save user to database
        $this->entityManager->persist($user);

        // Flush the Entity Manger
        $this->entityManager->flush();

        // Output success with the user's data, as creation is complete
        $io->success("User [" . $user->getEmail() . "] has been created!");

        // Return successful execution
        return Command::SUCCESS;
    }
}
