<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190410143601 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE hive_data (id INT AUTO_INCREMENT NOT NULL, hive_id INT NOT NULL, weight INT DEFAULT NULL, temperature DOUBLE PRECISION DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, deleted_at DATETIME DEFAULT NULL, INDEX IDX_2F63831FE9A48D12 (hive_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB ROW_FORMAT = DYNAMIC');
        $this->addSql('CREATE TABLE hive (id INT AUTO_INCREMENT NOT NULL, master_node_id INT NOT NULL, code VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, deleted_at DATETIME DEFAULT NULL, latitude NUMERIC(10, 8) DEFAULT NULL, longitude NUMERIC(11, 8) DEFAULT NULL, UNIQUE INDEX UNIQ_DC6DBBF877153098 (code), INDEX IDX_DC6DBBF89A2A72BF (master_node_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB ROW_FORMAT = DYNAMIC');
        $this->addSql('CREATE TABLE push_notification_token (id INT AUTO_INCREMENT NOT NULL, token VARCHAR(255) NOT NULL, active TINYINT(1) DEFAULT \'1\' NOT NULL, error_count INT DEFAULT 0 NOT NULL, last_response LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json_array)\', enabled TINYINT(1) DEFAULT \'1\' NOT NULL, filters LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json_array)\', created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, deleted_at DATETIME DEFAULT NULL, INDEX token_idx (token), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB ROW_FORMAT = DYNAMIC');
        $this->addSql('CREATE TABLE master_node (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, code VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, deleted_at DATETIME DEFAULT NULL, latitude NUMERIC(10, 8) DEFAULT NULL, longitude NUMERIC(11, 8) DEFAULT NULL, UNIQUE INDEX UNIQ_AECAC52C77153098 (code), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB ROW_FORMAT = DYNAMIC');
        $this->addSql('ALTER TABLE hive_data ADD CONSTRAINT FK_2F63831FE9A48D12 FOREIGN KEY (hive_id) REFERENCES hive (id)');
        $this->addSql('ALTER TABLE hive ADD CONSTRAINT FK_DC6DBBF89A2A72BF FOREIGN KEY (master_node_id) REFERENCES master_node (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE hive_data DROP FOREIGN KEY FK_2F63831FE9A48D12');
        $this->addSql('ALTER TABLE hive DROP FOREIGN KEY FK_DC6DBBF89A2A72BF');
        $this->addSql('DROP TABLE hive_data');
        $this->addSql('DROP TABLE hive');
        $this->addSql('DROP TABLE push_notification_token');
        $this->addSql('DROP TABLE master_node');
    }
}
