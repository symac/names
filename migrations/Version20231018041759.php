<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231018041759 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX idx_labels ON forename');
        $this->addSql('DROP INDEX idx_label ON forename');
        $this->addSql('DROP INDEX idx_wikidata ON forename');
        $this->addSql('CREATE INDEX searchLettersIndex ON forename (letters_index)');
        $this->addSql('CREATE INDEX idx_labels ON forename (labels)');
        $this->addSql('CREATE INDEX idx_label ON forename (`label`)');
        $this->addSql('CREATE INDEX idx_wikidata ON forename (wikidata)');
        $this->addSql('DROP INDEX idx_labels ON surname');
        $this->addSql('CREATE INDEX searchLettersIndex ON surname (letters_index)');
        $this->addSql('CREATE INDEX idx_labels ON surname (labels)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX searchLettersIndex ON forename');
        $this->addSql('DROP INDEX idx_labels ON forename');
        $this->addSql('DROP INDEX idx_label ON forename');
        $this->addSql('DROP INDEX idx_wikidata ON forename');
        $this->addSql('CREATE INDEX idx_labels ON forename (labels(191))');
        $this->addSql('CREATE INDEX idx_label ON forename (`label`(191))');
        $this->addSql('CREATE INDEX idx_wikidata ON forename (wikidata(191))');
        $this->addSql('DROP INDEX searchLettersIndex ON surname');
        $this->addSql('DROP INDEX idx_labels ON surname');
        $this->addSql('CREATE INDEX idx_labels ON surname (labels(191))');
    }
}
