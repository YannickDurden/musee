<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180124005527 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user CHANGE username username VARCHAR(25) NOT NULL, CHANGE is_active is_active TINYINT(1) NOT NULL, CHANGE email email VARCHAR(50) NOT NULL, CHANGE newsletter newsletter TINYINT(1) NOT NULL, CHANGE role role LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\'');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user CHANGE username username VARCHAR(25) DEFAULT NULL COLLATE utf8_unicode_ci, CHANGE is_active is_active TINYINT(1) DEFAULT NULL, CHANGE email email VARCHAR(50) DEFAULT NULL COLLATE utf8_unicode_ci, CHANGE newsletter newsletter TINYINT(1) DEFAULT NULL, CHANGE role role LONGTEXT DEFAULT NULL COLLATE utf8_unicode_ci COMMENT \'(DC2Type:array)\'');
    }
}
