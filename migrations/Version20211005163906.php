<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211005163906 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE authors (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE books (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, paruted_at DATE NOT NULL, is_free TINYINT(1) NOT NULL, cover VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE books_categories (books_id INT NOT NULL, categories_id INT NOT NULL, INDEX IDX_16746F157DD8AC20 (books_id), INDEX IDX_16746F15A21214B7 (categories_id), PRIMARY KEY(books_id, categories_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE books_authors (books_id INT NOT NULL, authors_id INT NOT NULL, INDEX IDX_877EACC27DD8AC20 (books_id), INDEX IDX_877EACC26DE2013A (authors_id), PRIMARY KEY(books_id, authors_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE books_reservations (id INT AUTO_INCREMENT NOT NULL, user_id CHAR(36) NOT NULL, books_id INT NOT NULL, reserved_at DATETIME NOT NULL, is_collected TINYINT(1) NOT NULL, collected_at DATETIME DEFAULT NULL, INDEX IDX_D86E1169A76ED395 (user_id), UNIQUE INDEX UNIQ_D86E11697DD8AC20 (books_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE categories (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users (id CHAR(36) NOT NULL DEFAULT UUID(), email VARCHAR(255) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, lastname VARCHAR(255) NOT NULL, firstname VARCHAR(255) NOT NULL, address VARCHAR(255) NOT NULL, birthdate DATE NOT NULL, is_validate TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_1483A5E9E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE books_categories ADD CONSTRAINT FK_16746F157DD8AC20 FOREIGN KEY (books_id) REFERENCES books (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE books_categories ADD CONSTRAINT FK_16746F15A21214B7 FOREIGN KEY (categories_id) REFERENCES categories (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE books_authors ADD CONSTRAINT FK_877EACC27DD8AC20 FOREIGN KEY (books_id) REFERENCES books (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE books_authors ADD CONSTRAINT FK_877EACC26DE2013A FOREIGN KEY (authors_id) REFERENCES authors (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE books_reservations ADD CONSTRAINT FK_D86E1169A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE books_reservations ADD CONSTRAINT FK_D86E11697DD8AC20 FOREIGN KEY (books_id) REFERENCES books (id)');
    }


    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE books_authors DROP FOREIGN KEY FK_877EACC26DE2013A');
        $this->addSql('ALTER TABLE books_categories DROP FOREIGN KEY FK_16746F157DD8AC20');
        $this->addSql('ALTER TABLE books_authors DROP FOREIGN KEY FK_877EACC27DD8AC20');
        $this->addSql('ALTER TABLE books_reservations DROP FOREIGN KEY FK_D86E11697DD8AC20');
        $this->addSql('ALTER TABLE books_categories DROP FOREIGN KEY FK_16746F15A21214B7');
        $this->addSql('ALTER TABLE books_reservations DROP FOREIGN KEY FK_D86E1169A76ED395');
        $this->addSql('DROP TABLE authors');
        $this->addSql('DROP TABLE books');
        $this->addSql('DROP TABLE books_categories');
        $this->addSql('DROP TABLE books_authors');
        $this->addSql('DROP TABLE books_reservations');
        $this->addSql('DROP TABLE categories');
        $this->addSql('DROP TABLE users');
    }
}
