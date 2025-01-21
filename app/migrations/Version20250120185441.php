<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250120185441 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D64932707F12');
        $this->addSql('DROP INDEX IDX_8D93D64932707F12 ON user');
        $this->addSql('ALTER TABLE user CHANGE my_mood_id has_mood_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649DF78BBE5 FOREIGN KEY (has_mood_id) REFERENCES mood (id)');
        $this->addSql('CREATE INDEX IDX_8D93D649DF78BBE5 ON user (has_mood_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649DF78BBE5');
        $this->addSql('DROP INDEX IDX_8D93D649DF78BBE5 ON user');
        $this->addSql('ALTER TABLE user CHANGE has_mood_id my_mood_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D64932707F12 FOREIGN KEY (my_mood_id) REFERENCES mood (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_8D93D64932707F12 ON user (my_mood_id)');
    }
}
