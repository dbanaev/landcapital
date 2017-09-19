<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150723220339 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("CREATE TABLE sip_object_has_media (id INT AUTO_INCREMENT NOT NULL, object_id INT DEFAULT NULL, image_id INT DEFAULT NULL, position INT NOT NULL, INDEX IDX_A12D14EA232D562B (object_id), INDEX IDX_A12D14EA3DA5256D (image_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE content_object (id INT AUTO_INCREMENT NOT NULL, image_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, price_from INT NOT NULL, price_to INT NOT NULL, currency INT NOT NULL, INDEX IDX_C09BDB403DA5256D (image_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("ALTER TABLE sip_object_has_media ADD CONSTRAINT FK_A12D14EA232D562B FOREIGN KEY (object_id) REFERENCES content_object (id)");
        $this->addSql("ALTER TABLE sip_object_has_media ADD CONSTRAINT FK_A12D14EA3DA5256D FOREIGN KEY (image_id) REFERENCES sip_media_gallery_media (id)");
        $this->addSql("ALTER TABLE content_object ADD CONSTRAINT FK_C09BDB403DA5256D FOREIGN KEY (image_id) REFERENCES sip_media_gallery_media (id)");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("ALTER TABLE sip_object_has_media DROP FOREIGN KEY FK_A12D14EA232D562B");
        $this->addSql("DROP TABLE sip_object_has_media");
        $this->addSql("DROP TABLE content_object");
    }
}
