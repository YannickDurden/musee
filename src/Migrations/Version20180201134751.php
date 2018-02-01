<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180201134751 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE mark ADD media_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE mark ADD CONSTRAINT FK_6674F271EA9FDD75 FOREIGN KEY (media_id) REFERENCES media (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_6674F271EA9FDD75 ON mark (media_id)');
        $this->addSql('ALTER TABLE media DROP FOREIGN KEY FK_6A2CA10C4290F12B');
        $this->addSql('DROP INDEX IDX_6A2CA10C4290F12B ON media');
        $this->addSql('ALTER TABLE media DROP mark_id');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE mark DROP FOREIGN KEY FK_6674F271EA9FDD75');
        $this->addSql('DROP INDEX UNIQ_6674F271EA9FDD75 ON mark');
        $this->addSql('ALTER TABLE mark DROP media_id');
        $this->addSql('ALTER TABLE media ADD mark_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE media ADD CONSTRAINT FK_6A2CA10C4290F12B FOREIGN KEY (mark_id) REFERENCES mark (id)');
        $this->addSql('CREATE INDEX IDX_6A2CA10C4290F12B ON media (mark_id)');
    }
}
