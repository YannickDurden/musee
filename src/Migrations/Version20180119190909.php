<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180119190909 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE description (id INT AUTO_INCREMENT NOT NULL, mark_id INT DEFAULT NULL, label LONGTEXT NOT NULL, category VARCHAR(20) NOT NULL, INDEX IDX_6DE440264290F12B (mark_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE description ADD CONSTRAINT FK_6DE440264290F12B FOREIGN KEY (mark_id) REFERENCES mark (id)');
        $this->addSql('ALTER TABLE mark ADD museum_id INT DEFAULT NULL, DROP description');
        $this->addSql('ALTER TABLE mark ADD CONSTRAINT FK_6674F2714B52E5B5 FOREIGN KEY (museum_id) REFERENCES museum (id)');
        $this->addSql('CREATE INDEX IDX_6674F2714B52E5B5 ON mark (museum_id)');
        $this->addSql('ALTER TABLE museum ADD map VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE question ADD answers VARCHAR(255) NOT NULL, DROP responses');
        $this->addSql('ALTER TABLE route DROP category');
        $this->addSql('ALTER TABLE user ADD username VARCHAR(25) NOT NULL, ADD password VARCHAR(64) NOT NULL, ADD is_active TINYINT(1) NOT NULL, CHANGE role role LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\'');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649F85E0677 ON user (username)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON user (email)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE description');
        $this->addSql('ALTER TABLE mark DROP FOREIGN KEY FK_6674F2714B52E5B5');
        $this->addSql('DROP INDEX IDX_6674F2714B52E5B5 ON mark');
        $this->addSql('ALTER TABLE mark ADD description LONGTEXT NOT NULL COLLATE utf8_unicode_ci, DROP museum_id');
        $this->addSql('ALTER TABLE museum DROP map');
        $this->addSql('ALTER TABLE question ADD responses LONGTEXT NOT NULL COLLATE utf8_unicode_ci, DROP answers');
        $this->addSql('ALTER TABLE route ADD category VARCHAR(50) NOT NULL COLLATE utf8_unicode_ci');
        $this->addSql('DROP INDEX UNIQ_8D93D649F85E0677 ON user');
        $this->addSql('DROP INDEX UNIQ_8D93D649E7927C74 ON user');
        $this->addSql('ALTER TABLE user DROP username, DROP password, DROP is_active, CHANGE role role VARCHAR(50) NOT NULL COLLATE utf8_unicode_ci');
    }
}
