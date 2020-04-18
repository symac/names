<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200418201223 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE quizz ADD result_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE quizz ADD CONSTRAINT FK_7C77973D7A7B643 FOREIGN KEY (result_id) REFERENCES result (id)');
        $this->addSql('CREATE INDEX IDX_7C77973D7A7B643 ON quizz (result_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE quizz DROP FOREIGN KEY FK_7C77973D7A7B643');
        $this->addSql('DROP INDEX IDX_7C77973D7A7B643 ON quizz');
        $this->addSql('ALTER TABLE quizz DROP result_id');
    }
}
