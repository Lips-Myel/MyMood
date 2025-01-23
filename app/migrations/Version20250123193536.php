<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250123193536 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE call_alerts (id INT AUTO_INCREMENT NOT NULL, alert_between_id INT DEFAULT NULL, call_statut TINYINT(1) NOT NULL, call_date DATETIME NOT NULL, UNIQUE INDEX UNIQ_32A21149EBA49B09 (alert_between_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cohortes (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, start_date DATETIME DEFAULT NULL, end_date DATETIME DEFAULT NULL, temporary TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cohortes_user (cohortes_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_989312B03631EA3B (cohortes_id), INDEX IDX_989312B0A76ED395 (user_id), PRIMARY KEY(cohortes_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE mood (id INT AUTO_INCREMENT NOT NULL, score INT DEFAULT NULL, date_mood DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE roles (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, has_mood_id INT DEFAULT NULL, has_role_id INT NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, last_connection DATETIME DEFAULT NULL, black_list TINYINT(1) DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), INDEX IDX_8D93D649DF78BBE5 (has_mood_id), INDEX IDX_8D93D649B1F24A77 (has_role_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE historical_mood (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, mood_id INT NOT NULL, date DATETIME NOT NULL, score INT NOT NULL, INDEX IDX_D605657A76ED395 (user_id), INDEX IDX_D605657B889D33E (mood_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE call_alerts ADD CONSTRAINT FK_32A21149EBA49B09 FOREIGN KEY (alert_between_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE cohortes_user ADD CONSTRAINT FK_989312B03631EA3B FOREIGN KEY (cohortes_id) REFERENCES cohortes (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE cohortes_user ADD CONSTRAINT FK_989312B0A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649DF78BBE5 FOREIGN KEY (has_mood_id) REFERENCES mood (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649B1F24A77 FOREIGN KEY (has_role_id) REFERENCES roles (id)');
        $this->addSql('ALTER TABLE historical_mood ADD CONSTRAINT FK_D605657A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE historical_mood ADD CONSTRAINT FK_D605657B889D33E FOREIGN KEY (mood_id) REFERENCES mood (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE historical_mood DROP FOREIGN KEY FK_D605657B889D33E');
        $this->addSql('ALTER TABLE historical_mood DROP FOREIGN KEY FK_D605657A76ED395');
        $this->addSql('ALTER TABLE call_alerts DROP FOREIGN KEY FK_32A21149EBA49B09');
        $this->addSql('ALTER TABLE cohortes_user DROP FOREIGN KEY FK_989312B03631EA3B');
        $this->addSql('ALTER TABLE cohortes_user DROP FOREIGN KEY FK_989312B0A76ED395');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649DF78BBE5');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649B1F24A77');
        $this->addSql('DROP TABLE call_alerts');
        $this->addSql('DROP TABLE cohortes');
        $this->addSql('DROP TABLE cohortes_user');
        $this->addSql('DROP TABLE mood');
        $this->addSql('DROP TABLE roles');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE historical_mood');
    }
}