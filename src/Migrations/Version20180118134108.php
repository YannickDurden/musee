<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180118134108 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE answer (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, question_id INT DEFAULT NULL, value VARCHAR(50) NOT NULL, correct TINYINT(1) NOT NULL, INDEX IDX_DADD4A25A76ED395 (user_id), INDEX IDX_DADD4A251E27F6BF (question_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE description (id INT AUTO_INCREMENT NOT NULL, mark_id INT DEFAULT NULL, label LONGTEXT NOT NULL, category VARCHAR(20) NOT NULL, INDEX IDX_6DE440264290F12B (mark_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE mark (id INT AUTO_INCREMENT NOT NULL, museum_id INT DEFAULT NULL, name VARCHAR(100) NOT NULL, coordinate_x NUMERIC(6, 2) NOT NULL, coordinate_y NUMERIC(6, 2) NOT NULL, image VARCHAR(255) NOT NULL, INDEX IDX_6674F2714B52E5B5 (museum_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE media (id INT AUTO_INCREMENT NOT NULL, mark_id INT DEFAULT NULL, name VARCHAR(50) NOT NULL, file VARCHAR(255) NOT NULL, INDEX IDX_6A2CA10C4290F12B (mark_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE museum (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, website VARCHAR(50) NOT NULL, address VARCHAR(100) NOT NULL, phone_number VARCHAR(20) NOT NULL, facebook VARCHAR(50) NOT NULL, twitter VARCHAR(50) NOT NULL, instagram VARCHAR(50) NOT NULL, youtube VARCHAR(50) NOT NULL, map VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE question (id INT AUTO_INCREMENT NOT NULL, mark_id INT DEFAULT NULL, label VARCHAR(255) NOT NULL, category VARCHAR(50) NOT NULL, answers VARCHAR(255) NOT NULL, INDEX IDX_B6F7494E4290F12B (mark_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE route (id INT AUTO_INCREMENT NOT NULL, museum_id INT DEFAULT NULL, name VARCHAR(100) NOT NULL, duration TIME NOT NULL, INDEX IDX_2C420794B52E5B5 (museum_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE route_mark (route_id INT NOT NULL, mark_id INT NOT NULL, INDEX IDX_CB31F3F434ECB4E6 (route_id), INDEX IDX_CB31F3F44290F12B (mark_id), PRIMARY KEY(route_id, mark_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE score (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, route_id INT DEFAULT NULL, value INT NOT NULL, INDEX IDX_32993751A76ED395 (user_id), INDEX IDX_3299375134ECB4E6 (route_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, first_name VARCHAR(50) NOT NULL, last_name VARCHAR(50) NOT NULL, email VARCHAR(50) NOT NULL, newsletter TINYINT(1) NOT NULL, role VARCHAR(50) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE answer ADD CONSTRAINT FK_DADD4A25A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE answer ADD CONSTRAINT FK_DADD4A251E27F6BF FOREIGN KEY (question_id) REFERENCES question (id)');
        $this->addSql('ALTER TABLE description ADD CONSTRAINT FK_6DE440264290F12B FOREIGN KEY (mark_id) REFERENCES mark (id)');
        $this->addSql('ALTER TABLE mark ADD CONSTRAINT FK_6674F2714B52E5B5 FOREIGN KEY (museum_id) REFERENCES museum (id)');
        $this->addSql('ALTER TABLE media ADD CONSTRAINT FK_6A2CA10C4290F12B FOREIGN KEY (mark_id) REFERENCES mark (id)');
        $this->addSql('ALTER TABLE question ADD CONSTRAINT FK_B6F7494E4290F12B FOREIGN KEY (mark_id) REFERENCES mark (id)');
        $this->addSql('ALTER TABLE route ADD CONSTRAINT FK_2C420794B52E5B5 FOREIGN KEY (museum_id) REFERENCES museum (id)');
        $this->addSql('ALTER TABLE route_mark ADD CONSTRAINT FK_CB31F3F434ECB4E6 FOREIGN KEY (route_id) REFERENCES route (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE route_mark ADD CONSTRAINT FK_CB31F3F44290F12B FOREIGN KEY (mark_id) REFERENCES mark (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE score ADD CONSTRAINT FK_32993751A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE score ADD CONSTRAINT FK_3299375134ECB4E6 FOREIGN KEY (route_id) REFERENCES route (id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE description DROP FOREIGN KEY FK_6DE440264290F12B');
        $this->addSql('ALTER TABLE media DROP FOREIGN KEY FK_6A2CA10C4290F12B');
        $this->addSql('ALTER TABLE question DROP FOREIGN KEY FK_B6F7494E4290F12B');
        $this->addSql('ALTER TABLE route_mark DROP FOREIGN KEY FK_CB31F3F44290F12B');
        $this->addSql('ALTER TABLE mark DROP FOREIGN KEY FK_6674F2714B52E5B5');
        $this->addSql('ALTER TABLE route DROP FOREIGN KEY FK_2C420794B52E5B5');
        $this->addSql('ALTER TABLE answer DROP FOREIGN KEY FK_DADD4A251E27F6BF');
        $this->addSql('ALTER TABLE route_mark DROP FOREIGN KEY FK_CB31F3F434ECB4E6');
        $this->addSql('ALTER TABLE score DROP FOREIGN KEY FK_3299375134ECB4E6');
        $this->addSql('ALTER TABLE answer DROP FOREIGN KEY FK_DADD4A25A76ED395');
        $this->addSql('ALTER TABLE score DROP FOREIGN KEY FK_32993751A76ED395');
        $this->addSql('DROP TABLE answer');
        $this->addSql('DROP TABLE description');
        $this->addSql('DROP TABLE mark');
        $this->addSql('DROP TABLE media');
        $this->addSql('DROP TABLE museum');
        $this->addSql('DROP TABLE question');
        $this->addSql('DROP TABLE route');
        $this->addSql('DROP TABLE route_mark');
        $this->addSql('DROP TABLE score');
        $this->addSql('DROP TABLE user');
    }
}
