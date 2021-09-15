<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210915045116 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE forename (id INT AUTO_INCREMENT NOT NULL, wikidata VARCHAR(255) NOT NULL, label VARCHAR(255) NOT NULL, labels VARCHAR(255) NOT NULL, labels_length INT NOT NULL, a SMALLINT DEFAULT 0, b SMALLINT DEFAULT 0, c SMALLINT DEFAULT 0, d SMALLINT DEFAULT 0, e SMALLINT DEFAULT 0, f SMALLINT DEFAULT 0, g SMALLINT DEFAULT 0, h SMALLINT DEFAULT 0, i SMALLINT DEFAULT 0, j SMALLINT DEFAULT 0, k SMALLINT DEFAULT 0, l SMALLINT DEFAULT 0, m SMALLINT DEFAULT 0, n SMALLINT DEFAULT 0, o SMALLINT DEFAULT 0, p SMALLINT DEFAULT 0, q SMALLINT DEFAULT 0, r SMALLINT DEFAULT 0, s SMALLINT DEFAULT 0, t SMALLINT DEFAULT 0, u SMALLINT DEFAULT 0, v SMALLINT DEFAULT 0, w SMALLINT DEFAULT 0, x SMALLINT DEFAULT 0, y SMALLINT DEFAULT 0, z SMALLINT DEFAULT 0, gender SMALLINT DEFAULT NULL, INDEX idx_length (labels_length), INDEX idx_labels (labels), INDEX idx_label (label), INDEX idx_wikidata (wikidata), INDEX searchA (A), INDEX searchB (B), INDEX searchC (C), INDEX searchD (D), INDEX searchE (E), INDEX searchF (F), INDEX searchG (G), INDEX searchH (H), INDEX searchI (I), INDEX searchJ (J), INDEX searchK (K), INDEX searchL (L), INDEX searchM (M), INDEX searchN (N), INDEX searchO (O), INDEX searchP (P), INDEX searchQ (Q), INDEX searchR (R), INDEX searchS (S), INDEX searchT (T), INDEX searchU (U), INDEX searchV (V), INDEX searchW (W), INDEX searchX (X), INDEX searchY (Y), INDEX searchZ (Z), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE quizz (id INT AUTO_INCREMENT NOT NULL, result_id INT DEFAULT NULL, quizz_category_id INT DEFAULT NULL, wikidata VARCHAR(255) NOT NULL, answer VARCHAR(255) DEFAULT NULL, question LONGTEXT DEFAULT NULL, creation_date DATETIME NOT NULL, secret VARCHAR(10) NOT NULL, anagram VARCHAR(255) DEFAULT NULL, image VARCHAR(255) DEFAULT NULL, commons_filename VARCHAR(512) DEFAULT NULL, visible TINYINT(1) DEFAULT \'1\' NOT NULL, views INT DEFAULT 0 NOT NULL, INDEX IDX_7C77973D7A7B643 (result_id), INDEX IDX_7C77973D34535201 (quizz_category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE quizz_category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, wikidata_occupation VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE result (id INT AUTO_INCREMENT NOT NULL, slug VARCHAR(255) NOT NULL, status INT NOT NULL, search VARCHAR(255) NOT NULL, create_date DATE DEFAULT \'1970-01-01\' NOT NULL, view_date DATE DEFAULT \'1970-01-01\' NOT NULL, count_anagrams INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE result_step (id INT AUTO_INCREMENT NOT NULL, result_id INT NOT NULL, forename_length INT NOT NULL, anagrams LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', duration DOUBLE PRECISION DEFAULT NULL, INDEX IDX_D382E5727A7B643 (result_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE surname (id INT AUTO_INCREMENT NOT NULL, wikidata VARCHAR(32) NOT NULL, label VARCHAR(255) NOT NULL, labels VARCHAR(255) NOT NULL, labels_length INT NOT NULL, a SMALLINT DEFAULT 0, b SMALLINT DEFAULT 0, c SMALLINT DEFAULT 0, d SMALLINT DEFAULT 0, e SMALLINT DEFAULT 0, f SMALLINT DEFAULT 0, g SMALLINT DEFAULT 0, h SMALLINT DEFAULT 0, i SMALLINT DEFAULT 0, j SMALLINT DEFAULT 0, k SMALLINT DEFAULT 0, l SMALLINT DEFAULT 0, m SMALLINT DEFAULT 0, n SMALLINT DEFAULT 0, o SMALLINT DEFAULT 0, p SMALLINT DEFAULT 0, q SMALLINT DEFAULT 0, r SMALLINT DEFAULT 0, s SMALLINT DEFAULT 0, t SMALLINT DEFAULT 0, u SMALLINT DEFAULT 0, v SMALLINT DEFAULT 0, w SMALLINT DEFAULT 0, x SMALLINT DEFAULT 0, y SMALLINT DEFAULT 0, z SMALLINT DEFAULT 0, language VARCHAR(10) NOT NULL, INDEX idx_length (labels_length), INDEX idx_labels (labels), INDEX searchA (A), INDEX searchB (B), INDEX searchC (C), INDEX searchD (D), INDEX searchE (E), INDEX searchF (F), INDEX searchG (G), INDEX searchH (H), INDEX searchI (I), INDEX searchJ (J), INDEX searchK (K), INDEX searchL (L), INDEX searchM (M), INDEX searchN (N), INDEX searchO (O), INDEX searchP (P), INDEX searchQ (Q), INDEX searchR (R), INDEX searchS (S), INDEX searchT (T), INDEX searchU (U), INDEX searchV (V), INDEX searchW (W), INDEX searchX (X), INDEX searchY (Y), INDEX searchZ (Z), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE quizz ADD CONSTRAINT FK_7C77973D7A7B643 FOREIGN KEY (result_id) REFERENCES result (id)');
        $this->addSql('ALTER TABLE quizz ADD CONSTRAINT FK_7C77973D34535201 FOREIGN KEY (quizz_category_id) REFERENCES quizz_category (id)');
        $this->addSql('ALTER TABLE result_step ADD CONSTRAINT FK_D382E5727A7B643 FOREIGN KEY (result_id) REFERENCES result (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE quizz DROP FOREIGN KEY FK_7C77973D34535201');
        $this->addSql('ALTER TABLE quizz DROP FOREIGN KEY FK_7C77973D7A7B643');
        $this->addSql('ALTER TABLE result_step DROP FOREIGN KEY FK_D382E5727A7B643');
        $this->addSql('DROP TABLE forename');
        $this->addSql('DROP TABLE quizz');
        $this->addSql('DROP TABLE quizz_category');
        $this->addSql('DROP TABLE result');
        $this->addSql('DROP TABLE result_step');
        $this->addSql('DROP TABLE surname');
    }
}
