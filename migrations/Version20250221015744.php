<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250221015744 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE answer (id INT AUTO_INCREMENT NOT NULL, response_id INT DEFAULT NULL, question_id INT DEFAULT NULL, answer_text LONGTEXT NOT NULL, INDEX IDX_DADD4A25FBF32840 (response_id), INDEX IDX_DADD4A251E27F6BF (question_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE question (id INT AUTO_INCREMENT NOT NULL, template_id INT DEFAULT NULL, type VARCHAR(255) NOT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, display_in_table TINYINT(1) NOT NULL, position INT NOT NULL, INDEX IDX_B6F7494E5DA0FB8 (template_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE response (id INT AUTO_INCREMENT NOT NULL, template_id INT DEFAULT NULL, submitted_at DATETIME NOT NULL, INDEX IDX_3E7B0BFB5DA0FB8 (template_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tag (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(150) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tag_template (tag_id INT NOT NULL, template_id INT NOT NULL, INDEX IDX_A1516458BAD26311 (tag_id), INDEX IDX_A15164585DA0FB8 (template_id), PRIMARY KEY(tag_id, template_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE template_tags (template_id INT NOT NULL, tag_id INT NOT NULL, INDEX IDX_BA72BE4B5DA0FB8 (template_id), INDEX IDX_BA72BE4BBAD26311 (tag_id), PRIMARY KEY(template_id, tag_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE answer ADD CONSTRAINT FK_DADD4A25FBF32840 FOREIGN KEY (response_id) REFERENCES response (id)');
        $this->addSql('ALTER TABLE answer ADD CONSTRAINT FK_DADD4A251E27F6BF FOREIGN KEY (question_id) REFERENCES question (id)');
        $this->addSql('ALTER TABLE question ADD CONSTRAINT FK_B6F7494E5DA0FB8 FOREIGN KEY (template_id) REFERENCES template (id)');
        $this->addSql('ALTER TABLE response ADD CONSTRAINT FK_3E7B0BFB5DA0FB8 FOREIGN KEY (template_id) REFERENCES template (id)');
        $this->addSql('ALTER TABLE tag_template ADD CONSTRAINT FK_A1516458BAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tag_template ADD CONSTRAINT FK_A15164585DA0FB8 FOREIGN KEY (template_id) REFERENCES template (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE template_tags ADD CONSTRAINT FK_BA72BE4B5DA0FB8 FOREIGN KEY (template_id) REFERENCES template (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE template_tags ADD CONSTRAINT FK_BA72BE4BBAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE template ADD topic_id INT DEFAULT NULL, CHANGE image_url image_url VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE template ADD CONSTRAINT FK_97601F831F55203D FOREIGN KEY (topic_id) REFERENCES topic (id)');
        $this->addSql('CREATE INDEX IDX_97601F831F55203D ON template (topic_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE answer DROP FOREIGN KEY FK_DADD4A25FBF32840');
        $this->addSql('ALTER TABLE answer DROP FOREIGN KEY FK_DADD4A251E27F6BF');
        $this->addSql('ALTER TABLE question DROP FOREIGN KEY FK_B6F7494E5DA0FB8');
        $this->addSql('ALTER TABLE response DROP FOREIGN KEY FK_3E7B0BFB5DA0FB8');
        $this->addSql('ALTER TABLE tag_template DROP FOREIGN KEY FK_A1516458BAD26311');
        $this->addSql('ALTER TABLE tag_template DROP FOREIGN KEY FK_A15164585DA0FB8');
        $this->addSql('ALTER TABLE template_tags DROP FOREIGN KEY FK_BA72BE4B5DA0FB8');
        $this->addSql('ALTER TABLE template_tags DROP FOREIGN KEY FK_BA72BE4BBAD26311');
        $this->addSql('DROP TABLE answer');
        $this->addSql('DROP TABLE question');
        $this->addSql('DROP TABLE response');
        $this->addSql('DROP TABLE tag');
        $this->addSql('DROP TABLE tag_template');
        $this->addSql('DROP TABLE template_tags');
        $this->addSql('ALTER TABLE template DROP FOREIGN KEY FK_97601F831F55203D');
        $this->addSql('DROP INDEX IDX_97601F831F55203D ON template');
        $this->addSql('ALTER TABLE template DROP topic_id, CHANGE image_url image_url VARCHAR(255) NOT NULL');
    }
}
