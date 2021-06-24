<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version1 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, image VARCHAR(255) DEFAULT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_64C19C15E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE delegation (id INT AUTO_INCREMENT NOT NULL, user_from_id INT NOT NULL, user_to_id INT NOT NULL, workshop_id INT DEFAULT NULL, theme_id INT DEFAULT NULL, deepness INT DEFAULT NULL, INDEX IDX_292F436D20C3C701 (user_from_id), INDEX IDX_292F436DD2F7B13D (user_to_id), INDEX IDX_292F436D1FDCE57C (workshop_id), INDEX IDX_292F436D59027487 (theme_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE document (id INT AUTO_INCREMENT NOT NULL, workshop_id INT NOT NULL, name VARCHAR(255) NOT NULL, path VARCHAR(255) NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_D8698A761FDCE57C (workshop_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE forum (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, proposal_id INT NOT NULL, parent_forum_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, INDEX IDX_852BBECDA76ED395 (user_id), INDEX IDX_852BBECDF4792058 (proposal_id), INDEX IDX_852BBECDB6011601 (parent_forum_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE keyword (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE notification (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, request_id INT DEFAULT NULL, date DATE NOT NULL, subject MEDIUMTEXT NOT NULL, is_read TINYINT(1) NOT NULL, INDEX IDX_BF5476CAA76ED395 (user_id), INDEX IDX_BF5476CA427EB8A5 (request_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE proposal (id INT AUTO_INCREMENT NOT NULL, workshop_id INT NOT NULL, user_id INT NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, INDEX IDX_BFE594721FDCE57C (workshop_id), INDEX IDX_BFE59472A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE report (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, forum_id INT NOT NULL, date DATETIME NOT NULL, INDEX IDX_C42F7784A76ED395 (user_id), INDEX IDX_C42F778429CCBAD0 (forum_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE request (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, category_id INT NOT NULL, is_done TINYINT(1) NOT NULL, INDEX IDX_3B978F9FA76ED395 (user_id), INDEX IDX_3B978F9F12469DE2 (category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reset_password_request (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, selector VARCHAR(20) NOT NULL, hashed_token VARCHAR(100) NOT NULL, requested_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', expires_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_7CE748AA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE theme (id INT AUTO_INCREMENT NOT NULL, category_id INT DEFAULT NULL, name VARCHAR(40) NOT NULL, image VARCHAR(255) DEFAULT NULL, updated_at DATETIME DEFAULT NULL, description MEDIUMTEXT NOT NULL, is_public TINYINT(1) NOT NULL, delegation_deepness INT DEFAULT NULL, vote_type VARCHAR(255) NOT NULL, INDEX IDX_9775E70812469DE2 (category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(40) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, is_allowed_emails TINYINT(1) NOT NULL, image VARCHAR(255) DEFAULT NULL, updated_at DATETIME DEFAULT NULL, first_name VARCHAR(40) NOT NULL, last_name VARCHAR(40) NOT NULL, UNIQUE INDEX UNIQ_8D93D649F85E0677 (username), UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_category (user_id INT NOT NULL, category_id INT NOT NULL, INDEX IDX_E6C1FDC1A76ED395 (user_id), INDEX IDX_E6C1FDC112469DE2 (category_id), PRIMARY KEY(user_id, category_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE vote (id INT AUTO_INCREMENT NOT NULL, proposal_id INT NOT NULL, user_id INT NOT NULL, creation_date DATE NOT NULL, voted_for TINYINT(1) DEFAULT \'0\' NOT NULL, voted_against TINYINT(1) DEFAULT \'0\' NOT NULL, voted_blank TINYINT(1) DEFAULT \'0\' NOT NULL, INDEX IDX_5A108564F4792058 (proposal_id), INDEX IDX_5A108564A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE website (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, version VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, registration_message VARCHAR(255) DEFAULT NULL, login_message VARCHAR(255) DEFAULT NULL, background_color VARCHAR(255) DEFAULT NULL, image VARCHAR(255) DEFAULT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE workshop (id INT AUTO_INCREMENT NOT NULL, theme_id INT NOT NULL, user_id INT NOT NULL, name VARCHAR(50) NOT NULL, description LONGTEXT NOT NULL, date_begin DATE NOT NULL, date_end DATE NOT NULL, rights_see_workshop VARCHAR(1024) NOT NULL, rights_vote_proposals VARCHAR(1024) NOT NULL, rights_write_proposals VARCHAR(1024) NOT NULL, quorum_required INT DEFAULT NULL, rights_delegation TINYINT(1) NOT NULL, image VARCHAR(255) DEFAULT NULL, updated_at DATETIME DEFAULT NULL, date_vote_begin DATE NOT NULL, date_vote_end DATE NOT NULL, keytext VARCHAR(255) DEFAULT NULL, delegation_deepness INT DEFAULT NULL, INDEX IDX_9B6F02C459027487 (theme_id), INDEX IDX_9B6F02C4A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE workshop_keyword (workshop_id INT NOT NULL, keyword_id INT NOT NULL, INDEX IDX_18DC632C1FDCE57C (workshop_id), INDEX IDX_18DC632C115D4552 (keyword_id), PRIMARY KEY(workshop_id, keyword_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE delegation ADD CONSTRAINT FK_292F436D20C3C701 FOREIGN KEY (user_from_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE delegation ADD CONSTRAINT FK_292F436DD2F7B13D FOREIGN KEY (user_to_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE delegation ADD CONSTRAINT FK_292F436D1FDCE57C FOREIGN KEY (workshop_id) REFERENCES workshop (id)');
        $this->addSql('ALTER TABLE delegation ADD CONSTRAINT FK_292F436D59027487 FOREIGN KEY (theme_id) REFERENCES theme (id)');
        $this->addSql('ALTER TABLE document ADD CONSTRAINT FK_D8698A761FDCE57C FOREIGN KEY (workshop_id) REFERENCES workshop (id)');
        $this->addSql('ALTER TABLE forum ADD CONSTRAINT FK_852BBECDA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE forum ADD CONSTRAINT FK_852BBECDF4792058 FOREIGN KEY (proposal_id) REFERENCES proposal (id)');
        $this->addSql('ALTER TABLE forum ADD CONSTRAINT FK_852BBECDB6011601 FOREIGN KEY (parent_forum_id) REFERENCES forum (id)');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CAA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CA427EB8A5 FOREIGN KEY (request_id) REFERENCES request (id)');
        $this->addSql('ALTER TABLE proposal ADD CONSTRAINT FK_BFE594721FDCE57C FOREIGN KEY (workshop_id) REFERENCES workshop (id)');
        $this->addSql('ALTER TABLE proposal ADD CONSTRAINT FK_BFE59472A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE report ADD CONSTRAINT FK_C42F7784A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE report ADD CONSTRAINT FK_C42F778429CCBAD0 FOREIGN KEY (forum_id) REFERENCES forum (id)');
        $this->addSql('ALTER TABLE request ADD CONSTRAINT FK_3B978F9FA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE request ADD CONSTRAINT FK_3B978F9F12469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE reset_password_request ADD CONSTRAINT FK_7CE748AA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE theme ADD CONSTRAINT FK_9775E70812469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_category ADD CONSTRAINT FK_E6C1FDC1A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_category ADD CONSTRAINT FK_E6C1FDC112469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE vote ADD CONSTRAINT FK_5A108564F4792058 FOREIGN KEY (proposal_id) REFERENCES proposal (id)');
        $this->addSql('ALTER TABLE vote ADD CONSTRAINT FK_5A108564A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE workshop ADD CONSTRAINT FK_9B6F02C459027487 FOREIGN KEY (theme_id) REFERENCES theme (id)');
        $this->addSql('ALTER TABLE workshop ADD CONSTRAINT FK_9B6F02C4A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE workshop_keyword ADD CONSTRAINT FK_18DC632C1FDCE57C FOREIGN KEY (workshop_id) REFERENCES workshop (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE workshop_keyword ADD CONSTRAINT FK_18DC632C115D4552 FOREIGN KEY (keyword_id) REFERENCES keyword (id) ON DELETE CASCADE');
        # On crée une entité Website qui sera ensuite modifiable
        $this->addSql("INSERT INTO `website` (`id`, `title`, `version`, `name`, `email`) VALUES ('1', 'AGORA Ex Machina', 'v0.9.2', 'CRLBazin', 'crlbazin@gmail.com')");
        # Creation de la categorie par defaut
        $this->addSql("INSERT INTO `category` (`name`) VALUES ('Defaut')");
        # Creation de l'administrateur
        $this->addSql("INSERT INTO `user` (`id`, `username`, `roles`, `password`, `email`, `is_allowed_emails`, `image`, `updated_at`, `first_name`, `last_name`) VALUES ('999', 'Administrateur', '[\"ROLE_ADMIN\"]', '\$argon2id\$v=19\$m=65536,t=4,p=1\$Q1BLTEFVM29kZHovLkFFTQ\$R2Kva8FJKipcwcaTYzPU6w+VSjty/X9aVjVqpvZdvLc', 'admin@mail.com', '0', NULL, NULL, 'Admin', 'Nistrateur'); ");
        # Ajout de l'admin a la categorie defaut
        $this->addSql("INSERT INTO `user_category` (`user_id`, `category_id`) VALUES ('999', '1'); ");
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE request DROP FOREIGN KEY FK_3B978F9F12469DE2');
        $this->addSql('ALTER TABLE theme DROP FOREIGN KEY FK_9775E70812469DE2');
        $this->addSql('ALTER TABLE user_category DROP FOREIGN KEY FK_E6C1FDC112469DE2');
        $this->addSql('ALTER TABLE forum DROP FOREIGN KEY FK_852BBECDB6011601');
        $this->addSql('ALTER TABLE report DROP FOREIGN KEY FK_C42F778429CCBAD0');
        $this->addSql('ALTER TABLE workshop_keyword DROP FOREIGN KEY FK_18DC632C115D4552');
        $this->addSql('ALTER TABLE forum DROP FOREIGN KEY FK_852BBECDF4792058');
        $this->addSql('ALTER TABLE vote DROP FOREIGN KEY FK_5A108564F4792058');
        $this->addSql('ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CA427EB8A5');
        $this->addSql('ALTER TABLE delegation DROP FOREIGN KEY FK_292F436D59027487');
        $this->addSql('ALTER TABLE workshop DROP FOREIGN KEY FK_9B6F02C459027487');
        $this->addSql('ALTER TABLE delegation DROP FOREIGN KEY FK_292F436D20C3C701');
        $this->addSql('ALTER TABLE delegation DROP FOREIGN KEY FK_292F436DD2F7B13D');
        $this->addSql('ALTER TABLE forum DROP FOREIGN KEY FK_852BBECDA76ED395');
        $this->addSql('ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CAA76ED395');
        $this->addSql('ALTER TABLE proposal DROP FOREIGN KEY FK_BFE59472A76ED395');
        $this->addSql('ALTER TABLE report DROP FOREIGN KEY FK_C42F7784A76ED395');
        $this->addSql('ALTER TABLE request DROP FOREIGN KEY FK_3B978F9FA76ED395');
        $this->addSql('ALTER TABLE reset_password_request DROP FOREIGN KEY FK_7CE748AA76ED395');
        $this->addSql('ALTER TABLE user_category DROP FOREIGN KEY FK_E6C1FDC1A76ED395');
        $this->addSql('ALTER TABLE vote DROP FOREIGN KEY FK_5A108564A76ED395');
        $this->addSql('ALTER TABLE workshop DROP FOREIGN KEY FK_9B6F02C4A76ED395');
        $this->addSql('ALTER TABLE delegation DROP FOREIGN KEY FK_292F436D1FDCE57C');
        $this->addSql('ALTER TABLE document DROP FOREIGN KEY FK_D8698A761FDCE57C');
        $this->addSql('ALTER TABLE proposal DROP FOREIGN KEY FK_BFE594721FDCE57C');
        $this->addSql('ALTER TABLE workshop_keyword DROP FOREIGN KEY FK_18DC632C1FDCE57C');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE delegation');
        $this->addSql('DROP TABLE document');
        $this->addSql('DROP TABLE forum');
        $this->addSql('DROP TABLE keyword');
        $this->addSql('DROP TABLE notification');
        $this->addSql('DROP TABLE proposal');
        $this->addSql('DROP TABLE report');
        $this->addSql('DROP TABLE request');
        $this->addSql('DROP TABLE reset_password_request');
        $this->addSql('DROP TABLE theme');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE user_category');
        $this->addSql('DROP TABLE vote');
        $this->addSql('DROP TABLE website');
        $this->addSql('DROP TABLE workshop');
        $this->addSql('DROP TABLE workshop_keyword');
    }
}
