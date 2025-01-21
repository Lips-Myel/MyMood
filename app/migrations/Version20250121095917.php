<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250121095917 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE call_alerts (id INT AUTO_INCREMENT NOT NULL, alert_between_id INT DEFAULT NULL, call_statut TINYINT(1) NOT NULL, call_date DATETIME NOT NULL, UNIQUE INDEX UNIQ_32A21149EBA49B09 (alert_between_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE roles (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE call_alerts ADD CONSTRAINT FK_32A21149EBA49B09 FOREIGN KEY (alert_between_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE call_alerts DROP FOREIGN KEY FK_32A21149EBA49B09');
        $this->addSql('DROP TABLE call_alerts');
        $this->addSql('DROP TABLE roles');
    }
}
