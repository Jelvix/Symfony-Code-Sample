<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190122100927 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE user_role (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_role_user_role (user_role_source INT NOT NULL, user_role_target INT NOT NULL, INDEX IDX_1799CA265DB75757 (user_role_source), INDEX IDX_1799CA26445207D8 (user_role_target), PRIMARY KEY(user_role_source, user_role_target)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_role_user_role ADD CONSTRAINT FK_1799CA265DB75757 FOREIGN KEY (user_role_source) REFERENCES user_role (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_role_user_role ADD CONSTRAINT FK_1799CA26445207D8 FOREIGN KEY (user_role_target) REFERENCES user_role (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user_role_user_role DROP FOREIGN KEY FK_1799CA265DB75757');
        $this->addSql('ALTER TABLE user_role_user_role DROP FOREIGN KEY FK_1799CA26445207D8');
        $this->addSql('DROP TABLE user_role');
        $this->addSql('DROP TABLE user_role_user_role');
    }
}
