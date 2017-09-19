<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150806175040 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("CREATE TABLE sip_bid (id INT AUTO_INCREMENT NOT NULL, created DATETIME NOT NULL, text LONGTEXT NOT NULL, email VARCHAR(255) NOT NULL, phone VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE sip_list_currency (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, code VARCHAR(255) NOT NULL, ratio DOUBLE PRECISION NOT NULL, UNIQUE INDEX UNIQ_7577BF095E237E06 (name), UNIQUE INDEX UNIQ_7577BF0977153098 (code), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE sip_list_locality (id INT AUTO_INCREMENT NOT NULL, road_id INT NOT NULL, name VARCHAR(255) NOT NULL, alias VARCHAR(255) DEFAULT NULL, text LONGTEXT DEFAULT NULL, coordinates VARCHAR(255) DEFAULT NULL, distance INT DEFAULT NULL, title VARCHAR(255) DEFAULT NULL, description LONGTEXT DEFAULT NULL, keywords LONGTEXT DEFAULT NULL, INDEX IDX_FDF78FD0962F8178 (road_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE sip_list_road (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, alias VARCHAR(255) DEFAULT NULL, text LONGTEXT DEFAULT NULL, title VARCHAR(255) DEFAULT NULL, description LONGTEXT DEFAULT NULL, keywords LONGTEXT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE sip_list_selection (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE sip_list_village (id INT AUTO_INCREMENT NOT NULL, locality_id INT NOT NULL, name VARCHAR(255) NOT NULL, alias VARCHAR(255) DEFAULT NULL, text LONGTEXT DEFAULT NULL, coordinates VARCHAR(255) DEFAULT NULL, title VARCHAR(255) DEFAULT NULL, description LONGTEXT DEFAULT NULL, keywords LONGTEXT DEFAULT NULL, INDEX IDX_33A2BFB88823A92 (locality_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE sip_list_realty_selection (object_id INT NOT NULL, selection_id INT NOT NULL, INDEX IDX_9D4955CA232D562B (object_id), INDEX IDX_9D4955CAE48EFE78 (selection_id), PRIMARY KEY(object_id, selection_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("ALTER TABLE sip_list_locality ADD CONSTRAINT FK_FDF78FD0962F8178 FOREIGN KEY (road_id) REFERENCES sip_list_road (id)");
        $this->addSql("ALTER TABLE sip_list_village ADD CONSTRAINT FK_33A2BFB88823A92 FOREIGN KEY (locality_id) REFERENCES sip_list_locality (id)");
        $this->addSql("ALTER TABLE sip_list_realty_selection ADD CONSTRAINT FK_9D4955CA232D562B FOREIGN KEY (object_id) REFERENCES content_object (id)");
        $this->addSql("ALTER TABLE sip_list_realty_selection ADD CONSTRAINT FK_9D4955CAE48EFE78 FOREIGN KEY (selection_id) REFERENCES sip_list_selection (id)");
        $this->addSql("ALTER TABLE content_object ADD village_id INT DEFAULT NULL, ADD currency_id INT NOT NULL, ADD coordinates VARCHAR(255) DEFAULT NULL, ADD distance INT DEFAULT NULL, ADD house INT DEFAULT NULL, ADD area INT DEFAULT NULL, ADD add_info LONGTEXT DEFAULT NULL, ADD land_info LONGTEXT DEFAULT NULL, ADD layout LONGTEXT DEFAULT NULL, ADD communication LONGTEXT DEFAULT NULL, ADD print_info LONGTEXT DEFAULT NULL, ADD title VARCHAR(255) DEFAULT NULL, ADD description LONGTEXT DEFAULT NULL, ADD keywords LONGTEXT DEFAULT NULL, CHANGE currency locality_id INT NOT NULL");
        $this->addSql("ALTER TABLE content_object ADD CONSTRAINT FK_C09BDB4088823A92 FOREIGN KEY (locality_id) REFERENCES sip_list_locality (id)");
        $this->addSql("ALTER TABLE content_object ADD CONSTRAINT FK_C09BDB405E0D5582 FOREIGN KEY (village_id) REFERENCES sip_list_village (id)");
        $this->addSql("ALTER TABLE content_object ADD CONSTRAINT FK_C09BDB4038248176 FOREIGN KEY (currency_id) REFERENCES sip_list_currency (id)");
        $this->addSql("CREATE INDEX IDX_C09BDB4088823A92 ON content_object (locality_id)");
        $this->addSql("CREATE INDEX IDX_C09BDB405E0D5582 ON content_object (village_id)");
        $this->addSql("CREATE INDEX IDX_C09BDB4038248176 ON content_object (currency_id)");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("ALTER TABLE content_object DROP FOREIGN KEY FK_C09BDB4038248176");
        $this->addSql("ALTER TABLE sip_list_village DROP FOREIGN KEY FK_33A2BFB88823A92");
        $this->addSql("ALTER TABLE content_object DROP FOREIGN KEY FK_C09BDB4088823A92");
        $this->addSql("ALTER TABLE sip_list_locality DROP FOREIGN KEY FK_FDF78FD0962F8178");
        $this->addSql("ALTER TABLE sip_list_realty_selection DROP FOREIGN KEY FK_9D4955CAE48EFE78");
        $this->addSql("ALTER TABLE content_object DROP FOREIGN KEY FK_C09BDB405E0D5582");
        $this->addSql("DROP TABLE sip_bid");
        $this->addSql("DROP TABLE sip_list_currency");
        $this->addSql("DROP TABLE sip_list_locality");
        $this->addSql("DROP TABLE sip_list_road");
        $this->addSql("DROP TABLE sip_list_selection");
        $this->addSql("DROP TABLE sip_list_village");
        $this->addSql("DROP TABLE sip_list_realty_selection");
        $this->addSql("DROP INDEX IDX_C09BDB4088823A92 ON content_object");
        $this->addSql("DROP INDEX IDX_C09BDB405E0D5582 ON content_object");
        $this->addSql("DROP INDEX IDX_C09BDB4038248176 ON content_object");
        $this->addSql("ALTER TABLE content_object ADD currency INT NOT NULL, DROP locality_id, DROP village_id, DROP currency_id, DROP coordinates, DROP distance, DROP house, DROP area, DROP add_info, DROP land_info, DROP layout, DROP communication, DROP print_info, DROP title, DROP description, DROP keywords");
    }
}
