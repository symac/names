<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231018041611 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE forename (id INT AUTO_INCREMENT NOT NULL, wikidata VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, `label` VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, labels VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, labels_length INT NOT NULL, a SMALLINT DEFAULT 0, b SMALLINT DEFAULT 0, c SMALLINT DEFAULT 0, d SMALLINT DEFAULT 0, e SMALLINT DEFAULT 0, f SMALLINT DEFAULT 0, g SMALLINT DEFAULT 0, h SMALLINT DEFAULT 0, i SMALLINT DEFAULT 0, j SMALLINT DEFAULT 0, k SMALLINT DEFAULT 0, l SMALLINT DEFAULT 0, m SMALLINT DEFAULT 0, n SMALLINT DEFAULT 0, o SMALLINT DEFAULT 0, p SMALLINT DEFAULT 0, q SMALLINT DEFAULT 0, r SMALLINT DEFAULT 0, s SMALLINT DEFAULT 0, t SMALLINT DEFAULT 0, u SMALLINT DEFAULT 0, v SMALLINT DEFAULT 0, w SMALLINT DEFAULT 0, x SMALLINT DEFAULT 0, y SMALLINT DEFAULT 0, z SMALLINT DEFAULT 0, gender SMALLINT DEFAULT NULL, letters_index VARCHAR(26) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci`, INDEX searchH (h), INDEX searchY (y), INDEX searchA (a), INDEX searchU (u), INDEX searchC (c), INDEX searchB (b), INDEX searchE (e), INDEX searchD (d), INDEX searchG (g), INDEX searchF (f), INDEX searchI (i), INDEX searchL (l), INDEX searchZ (z), INDEX searchV (v), INDEX idx_wikidata (wikidata(191)), INDEX searchK (k), INDEX searchJ (j), INDEX searchS (s), INDEX searchM (m), INDEX searchW (w), INDEX searchO (o), INDEX searchN (n), INDEX searchQ (q), INDEX searchP (p), INDEX idx_length (labels_length), INDEX searchR (r), INDEX searchX (x), INDEX searchT (t), INDEX idx_labels (labels(191)), INDEX idx_label (`label`(191)), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE quizz (id INT AUTO_INCREMENT NOT NULL, result_id INT DEFAULT NULL, quizz_category_id INT DEFAULT NULL, wikidata VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, answer VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, question LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, creation_date DATETIME NOT NULL, secret VARCHAR(10) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, anagram VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, image VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, commons_filename VARCHAR(512) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, visible TINYINT(1) DEFAULT \'1\' NOT NULL, views INT DEFAULT 0 NOT NULL, INDEX IDX_7C77973D7A7B643 (result_id), INDEX IDX_7C77973D34535201 (quizz_category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE quizz_category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, wikidata_occupation VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE result (id INT AUTO_INCREMENT NOT NULL, slug VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, status INT NOT NULL, search VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, create_date DATE DEFAULT \'1970-01-01\' NOT NULL, view_date DATE DEFAULT \'1970-01-01\' NOT NULL, count_anagrams INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE result_step (id INT AUTO_INCREMENT NOT NULL, result_id INT NOT NULL, forename_length INT NOT NULL, anagrams LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:array)\', duration DOUBLE PRECISION DEFAULT NULL, INDEX IDX_D382E5727A7B643 (result_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE surname (id INT AUTO_INCREMENT NOT NULL, wikidata VARCHAR(32) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, `label` VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, labels VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, labels_length INT NOT NULL, a SMALLINT DEFAULT 0, b SMALLINT DEFAULT 0, c SMALLINT DEFAULT 0, d SMALLINT DEFAULT 0, e SMALLINT DEFAULT 0, f SMALLINT DEFAULT 0, g SMALLINT DEFAULT 0, h SMALLINT DEFAULT 0, i SMALLINT DEFAULT 0, j SMALLINT DEFAULT 0, k SMALLINT DEFAULT 0, l SMALLINT DEFAULT 0, m SMALLINT DEFAULT 0, n SMALLINT DEFAULT 0, o SMALLINT DEFAULT 0, p SMALLINT DEFAULT 0, q SMALLINT DEFAULT 0, r SMALLINT DEFAULT 0, s SMALLINT DEFAULT 0, t SMALLINT DEFAULT 0, u SMALLINT DEFAULT 0, v SMALLINT DEFAULT 0, w SMALLINT DEFAULT 0, x SMALLINT DEFAULT 0, y SMALLINT DEFAULT 0, z SMALLINT DEFAULT 0, language VARCHAR(10) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, letters_index VARCHAR(26) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, INDEX searchJ (j), INDEX searchM (m), INDEX searchC (c), INDEX searchW (w), INDEX searchE (e), INDEX searchD (d), INDEX searchG (g), INDEX searchF (f), INDEX searchI (i), INDEX searchH (h), INDEX searchK (k), INDEX searchB (b), INDEX searchZ (z), INDEX searchX (x), INDEX searchA (a), INDEX searchL (l), INDEX searchU (u), INDEX searchO (o), INDEX searchY (y), INDEX searchQ (q), INDEX searchP (p), INDEX searchS (s), INDEX searchR (r), INDEX idx_length (labels_length), INDEX searchT (t), INDEX searchN (n), INDEX searchV (v), INDEX idx_labels (labels(191)), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE forename');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE quizz');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE quizz_category');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE result');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE result_step');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE surname');
    }
}
