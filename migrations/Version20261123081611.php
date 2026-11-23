<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261123081611 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE captura (id_captura INT AUTO_INCREMENT NOT NULL, fecha DATETIME NOT NULL, id_usuario INT NOT NULL, id_pokemon INT NOT NULL, id_combate INT DEFAULT NULL, UNIQUE INDEX UNIQ_8C9362FCCF1E99BF (id_combate), UNIQUE INDEX uniq_captura_usuario_pokemon (id_usuario, id_pokemon), INDEX IDX_8C9362FCFCF8192D (id_usuario), INDEX IDX_8C9362FCBC413983 (id_pokemon), PRIMARY KEY (id_captura)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE captura ADD CONSTRAINT FK_8C9362FCFCF8192D FOREIGN KEY (id_usuario) REFERENCES usuario (id_usuario) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE captura ADD CONSTRAINT FK_8C9362FCBC413983 FOREIGN KEY (id_pokemon) REFERENCES pokemon (id_pokemon) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE captura ADD CONSTRAINT FK_8C9362FCCF1E99BF FOREIGN KEY (id_combate) REFERENCES combate (id_combate) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE pokemon ADD numero_pokedex INT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE captura DROP FOREIGN KEY FK_8C9362FCFCF8192D');
        $this->addSql('ALTER TABLE captura DROP FOREIGN KEY FK_8C9362FCBC413983');
        $this->addSql('ALTER TABLE captura DROP FOREIGN KEY FK_8C9362FCCF1E99BF');
        $this->addSql('DROP TABLE captura');
        $this->addSql('ALTER TABLE pokemon DROP numero_pokedex');
    }
}
