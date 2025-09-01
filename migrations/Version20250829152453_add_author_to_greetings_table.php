<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250829152453_add_author_to_greetings_table extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE greetings ADD author_email VARCHAR(180) DEFAULT NULL');

        // Initialiser les enregistrements existants si nécessaire
        $this->addSql("UPDATE greetings SET author_email = 'null@example.com' WHERE status IS NULL");

        // Configure à Null par défaut
        $this->addSql('ALTER TABLE greetings ALTER author_email SET NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE greetings DROP author_email');
    }
}
