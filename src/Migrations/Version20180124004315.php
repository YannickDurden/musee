<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180124004315 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE museum DROP FOREIGN KEY FK_62474477642B8210');
        $this->addSql('DROP INDEX UNIQ_62474477642B8210 ON museum');
        $this->addSql('ALTER TABLE museum DROP admin_id');
        $this->addSql('ALTER TABLE user ADD museum_id INT DEFAULT NULL, CHANGE username username VARCHAR(25) NOT NULL, CHANGE is_active is_active TINYINT(1) NOT NULL, CHANGE email email VARCHAR(50) NOT NULL, CHANGE newsletter newsletter TINYINT(1) NOT NULL, CHANGE role role LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\'');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6494B52E5B5 FOREIGN KEY (museum_id) REFERENCES museum (id)');
        $this->addSql('CREATE INDEX IDX_8D93D6494B52E5B5 ON user (museum_id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE museum ADD admin_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE museum ADD CONSTRAINT FK_62474477642B8210 FOREIGN KEY (admin_id) REFERENCES user (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_62474477642B8210 ON museum (admin_id)');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6494B52E5B5');
        $this->addSql('DROP INDEX IDX_8D93D6494B52E5B5 ON user');
        $this->addSql('ALTER TABLE user DROP museum_id, CHANGE username username VARCHAR(25) DEFAULT NULL COLLATE utf8_unicode_ci, CHANGE is_active is_active TINYINT(1) DEFAULT NULL, CHANGE email email VARCHAR(50) DEFAULT NULL COLLATE utf8_unicode_ci, CHANGE newsletter newsletter TINYINT(1) DEFAULT NULL, CHANGE role role LONGTEXT DEFAULT NULL COLLATE utf8_unicode_ci COMMENT \'(DC2Type:array)\'');
    }
}
