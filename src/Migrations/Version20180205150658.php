<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180205150658 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE UNIQUE INDEX UNIQ_2C420795E237E06 ON route (name)');
        $this->addSql('ALTER TABLE mark ADD media_id INT DEFAULT NULL, CHANGE coordinate_x coordinate_x NUMERIC(6, 4) NOT NULL, CHANGE coordinate_y coordinate_y NUMERIC(6, 4) NOT NULL');
        $this->addSql('ALTER TABLE mark ADD CONSTRAINT FK_6674F271EA9FDD75 FOREIGN KEY (media_id) REFERENCES media (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_6674F2715E237E06 ON mark (name)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_6674F271EA9FDD75 ON mark (media_id)');
        $this->addSql('ALTER TABLE mark RENAME INDEX museum_id TO IDX_6674F2714B52E5B5');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_624744775E237E06 ON museum (name)');
        $this->addSql('ALTER TABLE media DROP FOREIGN KEY FK_6A2CA10C4290F12B');
        $this->addSql('DROP INDEX IDX_6A2CA10C4290F12B ON media');
        $this->addSql('ALTER TABLE media DROP mark_id');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_6A2CA10C5E237E06 ON media (name)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE mark DROP FOREIGN KEY FK_6674F271EA9FDD75');
        $this->addSql('DROP INDEX UNIQ_6674F2715E237E06 ON mark');
        $this->addSql('DROP INDEX UNIQ_6674F271EA9FDD75 ON mark');
        $this->addSql('ALTER TABLE mark DROP media_id, CHANGE coordinate_x coordinate_x NUMERIC(6, 2) NOT NULL, CHANGE coordinate_y coordinate_y NUMERIC(6, 2) NOT NULL');
        $this->addSql('ALTER TABLE mark RENAME INDEX idx_6674f2714b52e5b5 TO museum_id');
        $this->addSql('DROP INDEX UNIQ_6A2CA10C5E237E06 ON media');
        $this->addSql('ALTER TABLE media ADD mark_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE media ADD CONSTRAINT FK_6A2CA10C4290F12B FOREIGN KEY (mark_id) REFERENCES mark (id)');
        $this->addSql('CREATE INDEX IDX_6A2CA10C4290F12B ON media (mark_id)');
        $this->addSql('DROP INDEX UNIQ_624744775E237E06 ON museum');
        $this->addSql('DROP INDEX UNIQ_2C420795E237E06 ON route');
    }
}
