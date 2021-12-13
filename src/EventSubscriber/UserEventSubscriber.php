<?php
namespace App\EventSubscriber;

use App\Entity\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserEventSubscriber implements EventSubscriberInterface
{

    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public static function getSubscribedEvents()
    {
        return [
            BeforeEntityUpdatedEvent::class => ['beforeEntityUpdatedEvent'],
            BeforeEntityPersistedEvent::class => ['beforeEntityPersistedEvent']
        ];
    }

    public function beforeEntityUpdatedEvent(BeforeEntityUpdatedEvent $event)
    {
        $entity = $event->getEntityInstance();
        if (!($entity instanceof User))
        {
            return;
        }
        $this->_encodePassword($entity);
    }

    public function beforeEntityPersistedEvent(BeforeEntityPersistedEvent $event)
    {
        $entity = $event->getEntityInstance();
        if (!($entity instanceof User))
        {
            return;
        }
        $this->_encodePassword($entity);
    }

    private function _encodePassword(User &$entity)
    {
        if ( strlen($entity->getPlainPassword()) > 0 )
        {
            // encode the plain password
            $entity->setPassword(
                $this->passwordHasher->hashPassword(
                    $entity,
                    $entity->getPlainPassword()
                )
            );

            // peace-of-mind "" vs "NULL"
            $entity->setPlainPassword(null);
        }
    }
}