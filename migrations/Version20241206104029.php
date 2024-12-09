<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241206104029 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user ADD first_name VARCHAR(50) NOT NULL, ADD last_name VARCHAR(50) NOT NULL, DROP username, DROP bio, DROP profile_picture, DROP karma_points');
        $this->addSql('ALTER TABLE vote CHANGE vote_type vote_type ENUM(\'UPVOTE\', \'DOWNVOTE\')');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE vote CHANGE vote_type vote_type VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD username VARCHAR(255) NOT NULL, ADD bio LONGTEXT DEFAULT NULL, ADD profile_picture VARCHAR(255) DEFAULT NULL, ADD karma_points INT NOT NULL, DROP first_name, DROP last_name');
    }
}
