<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180202102606 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE mark CHANGE image image VARCHAR(255) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_6674F2715E237E06 ON mark (name)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_6A2CA10C5E237E06 ON media (name)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_624744775E237E06 ON museum (name)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2C420795E237E06 ON route (name)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX UNIQ_6674F2715E237E06 ON mark');
        $this->addSql('ALTER TABLE mark CHANGE image image VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci');
        $this->addSql('DROP INDEX UNIQ_6A2CA10C5E237E06 ON media');
        $this->addSql('DROP INDEX UNIQ_624744775E237E06 ON museum');
        $this->addSql('DROP INDEX UNIQ_2C420795E237E06 ON route');
    }
}
