<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211215223711 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE `group` (id INT AUTO_INCREMENT NOT NULL, internal_name VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, priority INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE group_group (group_source INT NOT NULL, group_target INT NOT NULL, INDEX IDX_3DEC6FA28C0CA7A3 (group_source), INDEX IDX_3DEC6FA295E9F72C (group_target), PRIMARY KEY(group_source, group_target)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_group (user_id INT NOT NULL, group_id INT NOT NULL, INDEX IDX_8F02BF9DA76ED395 (user_id), INDEX IDX_8F02BF9DFE54D947 (group_id), PRIMARY KEY(user_id, group_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE group_group ADD CONSTRAINT FK_3DEC6FA28C0CA7A3 FOREIGN KEY (group_source) REFERENCES `group` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE group_group ADD CONSTRAINT FK_3DEC6FA295E9F72C FOREIGN KEY (group_target) REFERENCES `group` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_group ADD CONSTRAINT FK_8F02BF9DA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_group ADD CONSTRAINT FK_8F02BF9DFE54D947 FOREIGN KEY (group_id) REFERENCES `group` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user DROP permission');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE group_group DROP FOREIGN KEY FK_3DEC6FA28C0CA7A3');
        $this->addSql('ALTER TABLE group_group DROP FOREIGN KEY FK_3DEC6FA295E9F72C');
        $this->addSql('ALTER TABLE user_group DROP FOREIGN KEY FK_8F02BF9DFE54D947');
        $this->addSql('DROP TABLE `group`');
        $this->addSql('DROP TABLE group_group');
        $this->addSql('DROP TABLE user_group');
        $this->addSql('ALTER TABLE `user` ADD permission INT NOT NULL');
    }
}
