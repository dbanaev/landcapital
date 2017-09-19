<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150806194057 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("ALTER TABLE sip_bid CHANGE created created DATETIME DEFAULT NULL, CHANGE text text LONGTEXT DEFAULT NULL, CHANGE email email VARCHAR(255) DEFAULT NULL, CHANGE phone phone VARCHAR(255) DEFAULT NULL, CHANGE name name VARCHAR(255) DEFAULT NULL");
        $this->addSql("DROP INDEX UNIQ_7577BF095E237E06 ON sip_list_currency");
        $this->addSql("DROP INDEX UNIQ_7577BF0977153098 ON sip_list_currency");
        $this->addSql("ALTER TABLE sip_list_village ADD distance INT DEFAULT NULL");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("ALTER TABLE sip_bid CHANGE created created DATETIME NOT NULL, CHANGE text text LONGTEXT NOT NULL, CHANGE email email VARCHAR(255) NOT NULL, CHANGE phone phone VARCHAR(255) NOT NULL, CHANGE name name VARCHAR(255) NOT NULL");
        $this->addSql("CREATE UNIQUE INDEX UNIQ_7577BF095E237E06 ON sip_list_currency (name)");
        $this->addSql("CREATE UNIQUE INDEX UNIQ_7577BF0977153098 ON sip_list_currency (code)");
        $this->addSql("ALTER TABLE sip_list_village DROP distance");
    }
}
