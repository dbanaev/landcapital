<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150811150310 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("ALTER TABLE content_object ADD second_image_id INT DEFAULT NULL");
        $this->addSql("ALTER TABLE content_object ADD CONSTRAINT FK_C09BDB40EB17A08 FOREIGN KEY (second_image_id) REFERENCES sip_media_gallery_media (id)");
        $this->addSql("CREATE INDEX IDX_C09BDB40EB17A08 ON content_object (second_image_id)");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("ALTER TABLE content_object DROP FOREIGN KEY FK_C09BDB40EB17A08");
        $this->addSql("DROP INDEX IDX_C09BDB40EB17A08 ON content_object");
        $this->addSql("ALTER TABLE content_object DROP second_image_id");
    }
}
