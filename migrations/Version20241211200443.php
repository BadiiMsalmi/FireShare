<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241211200443 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE vote CHANGE user_id user_id INT DEFAULT NULL, CHANGE post_id post_id INT DEFAULT NULL, CHANGE vote_type vote_type ENUM(\'UPVOTE\', \'DOWNVOTE\')');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE vote CHANGE user_id user_id INT NOT NULL, CHANGE post_id post_id INT NOT NULL, CHANGE vote_type vote_type VARCHAR(255) DEFAULT NULL');
    }
}
