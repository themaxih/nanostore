<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240116085242 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE adresse (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, gender TINYINT(1) NOT NULL, first_name VARCHAR(32) NOT NULL, last_name VARCHAR(32) NOT NULL, phone_number VARCHAR(10) NOT NULL, address_line VARCHAR(255) NOT NULL, postal_code VARCHAR(5) NOT NULL, city VARCHAR(50) NOT NULL, INDEX IDX_C35F0816A76ED395 (user_id), UNIQUE INDEX unique_address (user_id, gender, first_name, last_name, phone_number, address_line, postal_code, city), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE categorie (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, href_name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE commande (order_id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, total_price DOUBLE PRECISION NOT NULL, gender TINYINT(1) NOT NULL, first_name VARCHAR(32) NOT NULL, last_name VARCHAR(32) NOT NULL, phone_number VARCHAR(10) NOT NULL, address_line VARCHAR(255) NOT NULL, postal_code VARCHAR(5) NOT NULL, city VARCHAR(50) NOT NULL, order_date DATE NOT NULL, INDEX IDX_6EEAA67DA76ED395 (user_id), PRIMARY KEY(order_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE commande_produit (order_id INT NOT NULL, product_id INT NOT NULL, quantity INT NOT NULL, INDEX IDX_DF1E9E878D9F6D38 (order_id), INDEX IDX_DF1E9E874584665A (product_id), PRIMARY KEY(order_id, product_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE panier_produit (user_id INT NOT NULL, product_id INT NOT NULL, quantity_chosen INT NOT NULL, INDEX IDX_D31F28A6A76ED395 (user_id), INDEX IDX_D31F28A64584665A (product_id), PRIMARY KEY(user_id, product_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE produit (id INT AUTO_INCREMENT NOT NULL, sub_category VARCHAR(255) NOT NULL, title VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, quantity_left INT NOT NULL, price DOUBLE PRECISION NOT NULL, nom_image VARCHAR(50) NOT NULL, INDEX IDX_29A5EC27BCE3F798 (sub_category), FULLTEXT INDEX title_fulltext (title, description), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sous_categorie (name VARCHAR(255) NOT NULL, category_id INT NOT NULL, href_name VARCHAR(255) NOT NULL, INDEX IDX_52743D7B12469DE2 (category_id), PRIMARY KEY(name)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE utilisateur (user_id INT AUTO_INCREMENT NOT NULL, default_address_id INT DEFAULT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, first_name VARCHAR(32) NOT NULL, last_name VARCHAR(32) NOT NULL, gender TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_1D1C63B3E7927C74 (email), UNIQUE INDEX UNIQ_1D1C63B3BD94FB16 (default_address_id), PRIMARY KEY(user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE utilisateur_newsletter (email VARCHAR(255) NOT NULL, PRIMARY KEY(email)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE adresse ADD CONSTRAINT FK_C35F0816A76ED395 FOREIGN KEY (user_id) REFERENCES utilisateur (user_id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE commande ADD CONSTRAINT FK_6EEAA67DA76ED395 FOREIGN KEY (user_id) REFERENCES utilisateur (user_id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE commande_produit ADD CONSTRAINT FK_DF1E9E878D9F6D38 FOREIGN KEY (order_id) REFERENCES commande (order_id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE commande_produit ADD CONSTRAINT FK_DF1E9E874584665A FOREIGN KEY (product_id) REFERENCES produit (id)');
        $this->addSql('ALTER TABLE panier_produit ADD CONSTRAINT FK_D31F28A6A76ED395 FOREIGN KEY (user_id) REFERENCES utilisateur (user_id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE panier_produit ADD CONSTRAINT FK_D31F28A64584665A FOREIGN KEY (product_id) REFERENCES produit (id)');
        $this->addSql('ALTER TABLE produit ADD CONSTRAINT FK_29A5EC27BCE3F798 FOREIGN KEY (sub_category) REFERENCES sous_categorie (name)');
        $this->addSql('ALTER TABLE sous_categorie ADD CONSTRAINT FK_52743D7B12469DE2 FOREIGN KEY (category_id) REFERENCES categorie (id)');
        $this->addSql('ALTER TABLE utilisateur ADD CONSTRAINT FK_1D1C63B3BD94FB16 FOREIGN KEY (default_address_id) REFERENCES adresse (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE adresse DROP FOREIGN KEY FK_C35F0816A76ED395');
        $this->addSql('ALTER TABLE commande DROP FOREIGN KEY FK_6EEAA67DA76ED395');
        $this->addSql('ALTER TABLE commande_produit DROP FOREIGN KEY FK_DF1E9E878D9F6D38');
        $this->addSql('ALTER TABLE commande_produit DROP FOREIGN KEY FK_DF1E9E874584665A');
        $this->addSql('ALTER TABLE panier_produit DROP FOREIGN KEY FK_D31F28A6A76ED395');
        $this->addSql('ALTER TABLE panier_produit DROP FOREIGN KEY FK_D31F28A64584665A');
        $this->addSql('ALTER TABLE produit DROP FOREIGN KEY FK_29A5EC27BCE3F798');
        $this->addSql('ALTER TABLE sous_categorie DROP FOREIGN KEY FK_52743D7B12469DE2');
        $this->addSql('ALTER TABLE utilisateur DROP FOREIGN KEY FK_1D1C63B3BD94FB16');
        $this->addSql('DROP TABLE adresse');
        $this->addSql('DROP TABLE categorie');
        $this->addSql('DROP TABLE commande');
        $this->addSql('DROP TABLE commande_produit');
        $this->addSql('DROP TABLE panier_produit');
        $this->addSql('DROP TABLE produit');
        $this->addSql('DROP TABLE sous_categorie');
        $this->addSql('DROP TABLE utilisateur');
        $this->addSql('DROP TABLE utilisateur_newsletter');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
