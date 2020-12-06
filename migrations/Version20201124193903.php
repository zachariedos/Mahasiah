<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201124193903 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE `order` (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE order_products (id INT AUTO_INCREMENT NOT NULL, products_id INT DEFAULT NULL, theorder_id INT DEFAULT NULL, quantity INT NOT NULL, INDEX IDX_5242B8EB6C8A81A9 (products_id), INDEX IDX_5242B8EB1905A2A3 (theorder_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE order_products ADD CONSTRAINT FK_5242B8EB6C8A81A9 FOREIGN KEY (products_id) REFERENCES products (id)');
        $this->addSql('ALTER TABLE order_products ADD CONSTRAINT FK_5242B8EB1905A2A3 FOREIGN KEY (theorder_id) REFERENCES `order` (id)');
        $this->addSql('ALTER TABLE cart DROP ordered');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE order_products DROP FOREIGN KEY FK_5242B8EB1905A2A3');
        $this->addSql('DROP TABLE `order`');
        $this->addSql('DROP TABLE order_products');
        $this->addSql('ALTER TABLE cart ADD ordered TINYINT(1) DEFAULT \'0\' NOT NULL');
    }
}
