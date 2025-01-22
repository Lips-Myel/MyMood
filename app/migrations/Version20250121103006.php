<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250121103006 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE cohortes (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, start_date DATETIME DEFAULT NULL, end_date DATETIME DEFAULT NULL, temporary TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cohortes_user (cohortes_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_989312B03631EA3B (cohortes_id), INDEX IDX_989312B0A76ED395 (user_id), PRIMARY KEY(cohortes_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE cohortes_user ADD CONSTRAINT FK_989312B03631EA3B FOREIGN KEY (cohortes_id) REFERENCES cohortes (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE cohortes_user ADD CONSTRAINT FK_989312B0A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user ADD has_role_id INT NOT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649B1F24A77 FOREIGN KEY (has_role_id) REFERENCES roles (id)');
        $this->addSql('CREATE INDEX IDX_8D93D649B1F24A77 ON user (has_role_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cohortes_user DROP FOREIGN KEY FK_989312B03631EA3B');
        $this->addSql('ALTER TABLE cohortes_user DROP FOREIGN KEY FK_989312B0A76ED395');
        $this->addSql('DROP TABLE cohortes');
        $this->addSql('DROP TABLE cohortes_user');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649B1F24A77');
        $this->addSql('DROP INDEX IDX_8D93D649B1F24A77 ON user');
        $this->addSql('ALTER TABLE user DROP has_role_id');
    }
}
