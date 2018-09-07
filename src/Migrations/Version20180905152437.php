<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180905152437 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, slack_id VARCHAR(100) NOT NULL, slack_name VARCHAR(100) NOT NULL, slack_real_name VARCHAR(100) NOT NULL, email VARCHAR(150) DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D64963F6D2C9 (slack_id), UNIQUE INDEX UNIQ_8D93D64945ABD499 (slack_name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users_usergroups (user_id INT NOT NULL, user_group_id INT NOT NULL, INDEX IDX_4FFC8120A76ED395 (user_id), INDEX IDX_4FFC81201ED93D47 (user_group_id), PRIMARY KEY(user_id, user_group_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_group (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, handle VARCHAR(100) NOT NULL, slack_id VARCHAR(30) NOT NULL, UNIQUE INDEX UNIQ_8F02BF9D63F6D2C9 (slack_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE users_usergroups ADD CONSTRAINT FK_4FFC8120A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE users_usergroups ADD CONSTRAINT FK_4FFC81201ED93D47 FOREIGN KEY (user_group_id) REFERENCES user_group (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE users_usergroups DROP FOREIGN KEY FK_4FFC8120A76ED395');
        $this->addSql('ALTER TABLE users_usergroups DROP FOREIGN KEY FK_4FFC81201ED93D47');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE users_usergroups');
        $this->addSql('DROP TABLE user_group');
    }
}
