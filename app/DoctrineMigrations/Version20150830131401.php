<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150830131401 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("ALTER TABLE sip_bid ADD object_id INT DEFAULT NULL");
        $this->addSql("ALTER TABLE sip_bid ADD CONSTRAINT FK_B018DE80232D562B FOREIGN KEY (object_id) REFERENCES content_object (id)");
        $this->addSql("CREATE INDEX IDX_B018DE80232D562B ON sip_bid (object_id)");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("ALTER TABLE sip_bid DROP FOREIGN KEY FK_B018DE80232D562B");
        $this->addSql("DROP INDEX IDX_B018DE80232D562B ON sip_bid");
        $this->addSql("ALTER TABLE sip_bid DROP object_id");
    }
}
