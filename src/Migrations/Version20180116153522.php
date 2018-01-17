<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180116153522 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE mark ADD museum_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE mark ADD CONSTRAINT FK_6674F2714B52E5B5 FOREIGN KEY (museum_id) REFERENCES museum (id)');
        $this->addSql('CREATE INDEX IDX_6674F2714B52E5B5 ON mark (museum_id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE mark DROP FOREIGN KEY FK_6674F2714B52E5B5');
        $this->addSql('DROP INDEX IDX_6674F2714B52E5B5 ON mark');
        $this->addSql('ALTER TABLE mark DROP museum_id');
    }
}
