<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190306234457 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('CREATE TABLE address (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, street VARCHAR(255) NOT NULL, city VARCHAR(255) DEFAULT NULL, number VARCHAR(255) NOT NULL, zip VARCHAR(255) DEFAULT NULL)');
        $this->addSql('CREATE TABLE date_time_container (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, address_id INTEGER NOT NULL, date_time DATETIME NOT NULL)');
        $this->addSql('CREATE INDEX IDX_5AAF2086F5B7AF75 ON date_time_container (address_id)');
        $this->addSql('CREATE TABLE speed_metric (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, date_time_container_id INTEGER NOT NULL, min_speed INTEGER NOT NULL, max_speed INTEGER NOT NULL, amount_vehicles INTEGER NOT NULL)');
        $this->addSql('CREATE INDEX IDX_4031741850143883 ON speed_metric (date_time_container_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('DROP TABLE address');
        $this->addSql('DROP TABLE date_time_container');
        $this->addSql('DROP TABLE speed_metric');
    }
}
