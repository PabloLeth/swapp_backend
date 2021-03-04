<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210302113118 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE chat (id INT AUTO_INCREMENT NOT NULL, chat_type_id INT NOT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_659DF2AA894240FA (chat_type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE chat_type (id INT AUTO_INCREMENT NOT NULL, type VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE chat_user (id INT AUTO_INCREMENT NOT NULL, worker_id INT NOT NULL, chat_id INT NOT NULL, INDEX IDX_2B0F4B086B20BA36 (worker_id), INDEX IDX_2B0F4B081A9A7125 (chat_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE message (id INT AUTO_INCREMENT NOT NULL, chat_id INT NOT NULL, worker_id INT NOT NULL, text VARCHAR(512) NOT NULL, date DATETIME NOT NULL, INDEX IDX_B6BD307F1A9A7125 (chat_id), INDEX IDX_B6BD307F6B20BA36 (worker_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE chat ADD CONSTRAINT FK_659DF2AA894240FA FOREIGN KEY (chat_type_id) REFERENCES chat_type (id)');
        $this->addSql('ALTER TABLE chat_user ADD CONSTRAINT FK_2B0F4B086B20BA36 FOREIGN KEY (worker_id) REFERENCES Workers (id)');
        $this->addSql('ALTER TABLE chat_user ADD CONSTRAINT FK_2B0F4B081A9A7125 FOREIGN KEY (chat_id) REFERENCES chat (id)');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307F1A9A7125 FOREIGN KEY (chat_id) REFERENCES chat (id)');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307F6B20BA36 FOREIGN KEY (worker_id) REFERENCES Workers (id)');
        $this->addSql('ALTER TABLE Branch DROP FOREIGN KEY Branch_ibfk_1');
        $this->addSql('ALTER TABLE Branch CHANGE company_id company_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE Branch ADD CONSTRAINT FK_BC2A1E29979B1AD6 FOREIGN KEY (company_id) REFERENCES Company (id)');
        $this->addSql('ALTER TABLE Rating CHANGE from_user from_user INT DEFAULT NULL, CHANGE to_user to_user INT DEFAULT NULL');
        $this->addSql('ALTER TABLE Shift DROP FOREIGN KEY Shift_ibfk_1');
        $this->addSql('ALTER TABLE Shift DROP FOREIGN KEY Shift_ibfk_5');
        $this->addSql('ALTER TABLE Shift CHANGE branch_id branch_id INT DEFAULT NULL, CHANGE worker_id worker_id INT DEFAULT NULL, CHANGE shift_type_id shift_type_id INT DEFAULT NULL, CHANGE swapping swapping TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE Shift ADD CONSTRAINT FK_64CA1441DCD6CC49 FOREIGN KEY (branch_id) REFERENCES Branch (id)');
        $this->addSql('ALTER TABLE Shift ADD CONSTRAINT FK_64CA14416B20BA36 FOREIGN KEY (worker_id) REFERENCES Workers (id)');
        $this->addSql('ALTER TABLE Workers DROP FOREIGN KEY Workers_ibfk_1');
        $this->addSql('ALTER TABLE Workers CHANGE branch_id branch_id INT DEFAULT NULL, CHANGE role_id role_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE Workers ADD CONSTRAINT FK_7790445CDCD6CC49 FOREIGN KEY (branch_id) REFERENCES Branch (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE chat_user DROP FOREIGN KEY FK_2B0F4B081A9A7125');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307F1A9A7125');
        $this->addSql('ALTER TABLE chat DROP FOREIGN KEY FK_659DF2AA894240FA');
        $this->addSql('DROP TABLE chat');
        $this->addSql('DROP TABLE chat_type');
        $this->addSql('DROP TABLE chat_user');
        $this->addSql('DROP TABLE message');
        $this->addSql('ALTER TABLE Branch DROP FOREIGN KEY FK_BC2A1E29979B1AD6');
        $this->addSql('ALTER TABLE Branch CHANGE company_id company_id INT NOT NULL');
        $this->addSql('ALTER TABLE Branch ADD CONSTRAINT Branch_ibfk_1 FOREIGN KEY (company_id) REFERENCES Company (id) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE Rating CHANGE from_user from_user INT NOT NULL, CHANGE to_user to_user INT NOT NULL');
        $this->addSql('ALTER TABLE Shift DROP FOREIGN KEY FK_64CA1441DCD6CC49');
        $this->addSql('ALTER TABLE Shift DROP FOREIGN KEY FK_64CA14416B20BA36');
        $this->addSql('ALTER TABLE Shift CHANGE branch_id branch_id INT NOT NULL, CHANGE shift_type_id shift_type_id INT NOT NULL, CHANGE worker_id worker_id INT NOT NULL, CHANGE swapping swapping TINYINT(1) DEFAULT \'0\' NOT NULL');
        $this->addSql('ALTER TABLE Shift ADD CONSTRAINT Shift_ibfk_1 FOREIGN KEY (branch_id) REFERENCES Branch (id) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE Shift ADD CONSTRAINT Shift_ibfk_5 FOREIGN KEY (worker_id) REFERENCES Workers (id) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE Workers DROP FOREIGN KEY FK_7790445CDCD6CC49');
        $this->addSql('ALTER TABLE Workers CHANGE branch_id branch_id INT NOT NULL, CHANGE role_id role_id INT NOT NULL');
        $this->addSql('ALTER TABLE Workers ADD CONSTRAINT Workers_ibfk_1 FOREIGN KEY (branch_id) REFERENCES Branch (id) ON UPDATE CASCADE ON DELETE CASCADE');
    }
}
