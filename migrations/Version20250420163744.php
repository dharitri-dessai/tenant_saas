<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250420163744 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SEQUENCE audit_log_id_seq INCREMENT BY 1 MINVALUE 1 START 1
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE audit_log (id INT NOT NULL, user_id INT DEFAULT NULL, entity_type VARCHAR(255) NOT NULL, entity_id INT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, action VARCHAR(255) NOT NULL, request_route VARCHAR(255) DEFAULT NULL, event_data JSON NOT NULL, ip_address VARCHAR(255) NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_F6E1C0F5A76ED395 ON audit_log (user_id)
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN audit_log.created_at IS '(DC2Type:datetime_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE audit_log ADD CONSTRAINT FK_F6E1C0F5A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SCHEMA public
        SQL);
        $this->addSql(<<<'SQL'
            DROP SEQUENCE audit_log_id_seq CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE audit_log DROP CONSTRAINT FK_F6E1C0F5A76ED395
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE audit_log
        SQL);
    }
}
