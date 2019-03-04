<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190301162126 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('CREATE TABLE speed_category (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, data_set_id INTEGER NOT NULL, range_from INTEGER NOT NULL, range_to INTEGER NOT NULL, amount_vehicles INTEGER NOT NULL)');
        $this->addSql('CREATE INDEX IDX_7ABA5EBA70053C01 ON speed_category (data_set_id)');
        $this->addSql('CREATE TABLE data_set (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, address_id INTEGER NOT NULL, date_time DATETIME NOT NULL)');
        $this->addSql('CREATE INDEX IDX_A298C469F5B7AF75 ON data_set (address_id)');
        $this->addSql('CREATE TABLE address (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, street VARCHAR(255) NOT NULL, city VARCHAR(255) DEFAULT NULL, number VARCHAR(255) NOT NULL, zip VARCHAR(255) DEFAULT NULL)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('DROP TABLE speed_category');
        $this->addSql('DROP TABLE data_set');
        $this->addSql('DROP TABLE address');
    }
}
