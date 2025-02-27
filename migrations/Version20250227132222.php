<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250227132222 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tag_template DROP FOREIGN KEY FK_A15164585DA0FB8');
        $this->addSql('ALTER TABLE tag_template DROP FOREIGN KEY FK_A1516458BAD26311');
        $this->addSql('DROP TABLE tag_template');
        $this->addSql('ALTER TABLE question ADD title VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE tag_template (tag_id INT NOT NULL, template_id INT NOT NULL, INDEX IDX_A1516458BAD26311 (tag_id), INDEX IDX_A15164585DA0FB8 (template_id), PRIMARY KEY(tag_id, template_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE tag_template ADD CONSTRAINT FK_A15164585DA0FB8 FOREIGN KEY (template_id) REFERENCES template (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tag_template ADD CONSTRAINT FK_A1516458BAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE question DROP title');
    }
}
