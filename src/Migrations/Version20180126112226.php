<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180126112226 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE museum ADD admin_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE museum ADD CONSTRAINT FK_62474477642B8210 FOREIGN KEY (admin_id) REFERENCES user (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_62474477642B8210 ON museum (admin_id)');
        $this->addSql('ALTER TABLE user ADD username VARCHAR(25) NOT NULL, ADD password VARCHAR(64) DEFAULT NULL, ADD is_active TINYINT(1) NOT NULL, CHANGE first_name first_name VARCHAR(50) DEFAULT NULL, CHANGE last_name last_name VARCHAR(50) DEFAULT NULL, CHANGE role role LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\'');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649F85E0677 ON user (username)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON user (email)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE museum DROP FOREIGN KEY FK_62474477642B8210');
        $this->addSql('DROP INDEX UNIQ_62474477642B8210 ON museum');
        $this->addSql('ALTER TABLE museum DROP admin_id');
        $this->addSql('DROP INDEX UNIQ_8D93D649F85E0677 ON user');
        $this->addSql('DROP INDEX UNIQ_8D93D649E7927C74 ON user');
        $this->addSql('ALTER TABLE user DROP username, DROP password, DROP is_active, CHANGE first_name first_name VARCHAR(50) NOT NULL COLLATE utf8_unicode_ci, CHANGE last_name last_name VARCHAR(50) NOT NULL COLLATE utf8_unicode_ci, CHANGE role role VARCHAR(50) NOT NULL COLLATE utf8_unicode_ci');
    }
}
