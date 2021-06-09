<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200520093500 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE cours CHANGE owner owner_id INT NOT NULL');
        $this->addSql('ALTER TABLE cours ADD CONSTRAINT FK_FDCA8C9C7E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_FDCA8C9C7E3C61F9 ON cours (owner_id)');
        $this->addSql('ALTER TABLE exercice CHANGE owner owner_id INT NOT NULL');
        $this->addSql('ALTER TABLE exercice ADD CONSTRAINT FK_E418C74D7E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_E418C74D7E3C61F9 ON exercice (owner_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE cours DROP FOREIGN KEY FK_FDCA8C9C7E3C61F9');
        $this->addSql('DROP INDEX IDX_FDCA8C9C7E3C61F9 ON cours');
        $this->addSql('ALTER TABLE cours CHANGE owner_id owner INT NOT NULL');
        $this->addSql('ALTER TABLE exercice DROP FOREIGN KEY FK_E418C74D7E3C61F9');
        $this->addSql('DROP INDEX IDX_E418C74D7E3C61F9 ON exercice');
        $this->addSql('ALTER TABLE exercice CHANGE owner_id owner INT NOT NULL');
    }
}
