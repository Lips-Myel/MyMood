<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250123150022 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE historical_mood (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, mood_id INT NOT NULL, date DATETIME NOT NULL, score INT NOT NULL, INDEX IDX_D605657A76ED395 (user_id), INDEX IDX_D605657B889D33E (mood_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE historical_mood ADD CONSTRAINT FK_D605657A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE historical_mood ADD CONSTRAINT FK_D605657B889D33E FOREIGN KEY (mood_id) REFERENCES mood (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE historical_mood DROP FOREIGN KEY FK_D605657A76ED395');
        $this->addSql('ALTER TABLE historical_mood DROP FOREIGN KEY FK_D605657B889D33E');
        $this->addSql('DROP TABLE historical_mood');
    }
}
