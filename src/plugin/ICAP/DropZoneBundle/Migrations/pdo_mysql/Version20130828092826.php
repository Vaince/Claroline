<?php

namespace ICAP\DropZoneBundle\Migrations\pdo_mysql;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated migration based on mapping information: modify it with caution
 *
 * Generation date: 2013/08/28 09:28:31
 */
class Version20130828092826 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("
            CREATE TABLE icap__dropzonebundle_correction (
                id INT AUTO_INCREMENT NOT NULL, 
                user_id INT NOT NULL, 
                total_grade SMALLINT DEFAULT NULL, 
                comment LONGTEXT DEFAULT NULL, 
                valid TINYINT(1) NOT NULL, 
                start_date DATETIME NOT NULL, 
                end_date DATETIME DEFAULT NULL, 
                finished TINYINT(1) NOT NULL, 
                INDEX IDX_CDA81F40A76ED395 (user_id), 
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB
        ");
        $this->addSql("
            CREATE TABLE icap__dropzonebundle_criterion (
                id INT AUTO_INCREMENT NOT NULL, 
                drop_zone_id INT NOT NULL, 
                instruction LONGTEXT NOT NULL, 
                INDEX IDX_F94B3BA7A8C6E7BD (drop_zone_id), 
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB
        ");
        $this->addSql("
            CREATE TABLE icap__dropzonebundle_document (
                id INT AUTO_INCREMENT NOT NULL, 
                resource_node_id INT DEFAULT NULL, 
                drop_id INT NOT NULL, 
                url VARCHAR(255) DEFAULT NULL, 
                path VARCHAR(255) DEFAULT NULL, 
                INDEX IDX_744084241BAD783F (resource_node_id), 
                INDEX IDX_744084244D224760 (drop_id), 
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB
        ");
        $this->addSql("
            CREATE TABLE icap__dropzonebundle_drop (
                id INT AUTO_INCREMENT NOT NULL, 
                drop_zone_id INT NOT NULL, 
                user_id INT NOT NULL, 
                drop_date DATETIME NOT NULL, 
                reported TINYINT(1) NOT NULL, 
                valid TINYINT(1) NOT NULL, 
                INDEX IDX_3AD19BA6A8C6E7BD (drop_zone_id), 
                INDEX IDX_3AD19BA6A76ED395 (user_id), 
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB
        ");
        $this->addSql("
            CREATE TABLE icap__dropzonebundle_dropzone (
                id INT AUTO_INCREMENT NOT NULL, 
                instruction LONGTEXT DEFAULT NULL, 
                allow_workspace_resource TINYINT(1) NOT NULL, 
                allow_upload TINYINT(1) NOT NULL, 
                allow_url TINYINT(1) NOT NULL, 
                peer_review TINYINT(1) NOT NULL, 
                expected_total_correction SMALLINT NOT NULL, 
                allow_drop_in_review TINYINT(1) NOT NULL, 
                display_notation_to_learners TINYINT(1) NOT NULL, 
                display_notation_message_to_learners TINYINT(1) NOT NULL, 
                minimum_score_to_pass SMALLINT NOT NULL, 
                manual_planning TINYINT(1) NOT NULL, 
                manual_state VARCHAR(255) NOT NULL, 
                start_allow_drop DATETIME DEFAULT NULL, 
                end_allow_drop DATETIME DEFAULT NULL, 
                end_review DATETIME DEFAULT NULL, 
                allow_comment_in_correction TINYINT(1) NOT NULL, 
                total_criteria_column SMALLINT NOT NULL, 
                resourceNode_id INT DEFAULT NULL, 
                UNIQUE INDEX UNIQ_6782FC23B87FAB32 (resourceNode_id), 
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB
        ");
        $this->addSql("
            CREATE TABLE icap__dropzonebundle_grade (
                id INT AUTO_INCREMENT NOT NULL, 
                criterion_id INT NOT NULL, 
                correction_id INT NOT NULL, 
                value SMALLINT NOT NULL, 
                INDEX IDX_B3C52D9397766307 (criterion_id), 
                INDEX IDX_B3C52D9394AE086B (correction_id), 
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB
        ");
        $this->addSql("
            ALTER TABLE icap__dropzonebundle_correction 
            ADD CONSTRAINT FK_CDA81F40A76ED395 FOREIGN KEY (user_id) 
            REFERENCES claro_user (id)
        ");
        $this->addSql("
            ALTER TABLE icap__dropzonebundle_criterion 
            ADD CONSTRAINT FK_F94B3BA7A8C6E7BD FOREIGN KEY (drop_zone_id) 
            REFERENCES icap__dropzonebundle_dropzone (id)
        ");
        $this->addSql("
            ALTER TABLE icap__dropzonebundle_document 
            ADD CONSTRAINT FK_744084241BAD783F FOREIGN KEY (resource_node_id) 
            REFERENCES claro_resource_node (id)
        ");
        $this->addSql("
            ALTER TABLE icap__dropzonebundle_document 
            ADD CONSTRAINT FK_744084244D224760 FOREIGN KEY (drop_id) 
            REFERENCES icap__dropzonebundle_drop (id)
        ");
        $this->addSql("
            ALTER TABLE icap__dropzonebundle_drop 
            ADD CONSTRAINT FK_3AD19BA6A8C6E7BD FOREIGN KEY (drop_zone_id) 
            REFERENCES icap__dropzonebundle_dropzone (id)
        ");
        $this->addSql("
            ALTER TABLE icap__dropzonebundle_drop 
            ADD CONSTRAINT FK_3AD19BA6A76ED395 FOREIGN KEY (user_id) 
            REFERENCES claro_user (id)
        ");
        $this->addSql("
            ALTER TABLE icap__dropzonebundle_dropzone 
            ADD CONSTRAINT FK_6782FC23B87FAB32 FOREIGN KEY (resourceNode_id) 
            REFERENCES claro_resource_node (id) 
            ON DELETE CASCADE
        ");
        $this->addSql("
            ALTER TABLE icap__dropzonebundle_grade 
            ADD CONSTRAINT FK_B3C52D9397766307 FOREIGN KEY (criterion_id) 
            REFERENCES icap__dropzonebundle_criterion (id)
        ");
        $this->addSql("
            ALTER TABLE icap__dropzonebundle_grade 
            ADD CONSTRAINT FK_B3C52D9394AE086B FOREIGN KEY (correction_id) 
            REFERENCES icap__dropzonebundle_correction (id)
        ");
    }

    public function down(Schema $schema)
    {
        $this->addSql("
            ALTER TABLE icap__dropzonebundle_grade 
            DROP FOREIGN KEY FK_B3C52D9394AE086B
        ");
        $this->addSql("
            ALTER TABLE icap__dropzonebundle_grade 
            DROP FOREIGN KEY FK_B3C52D9397766307
        ");
        $this->addSql("
            ALTER TABLE icap__dropzonebundle_document 
            DROP FOREIGN KEY FK_744084244D224760
        ");
        $this->addSql("
            ALTER TABLE icap__dropzonebundle_criterion 
            DROP FOREIGN KEY FK_F94B3BA7A8C6E7BD
        ");
        $this->addSql("
            ALTER TABLE icap__dropzonebundle_drop 
            DROP FOREIGN KEY FK_3AD19BA6A8C6E7BD
        ");
        $this->addSql("
            DROP TABLE icap__dropzonebundle_correction
        ");
        $this->addSql("
            DROP TABLE icap__dropzonebundle_criterion
        ");
        $this->addSql("
            DROP TABLE icap__dropzonebundle_document
        ");
        $this->addSql("
            DROP TABLE icap__dropzonebundle_drop
        ");
        $this->addSql("
            DROP TABLE icap__dropzonebundle_dropzone
        ");
        $this->addSql("
            DROP TABLE icap__dropzonebundle_grade
        ");
    }
}