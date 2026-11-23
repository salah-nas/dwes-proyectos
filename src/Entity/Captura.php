<?php

namespace App\Entity;

use App\Repository\CapturaRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * Pokémon capturados por un usuario (su Pokédex).
 * Un usuario solo puede capturar cada Pokémon una vez.
 */
#[ORM\Entity(repositoryClass: CapturaRepository::class)]
#[ORM\Table(name: 'captura')]
#[ORM\UniqueConstraint(name: 'uniq_captura_usuario_pokemon', columns: ['id_usuario', 'id_pokemon'])]
class Captura
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_captura')]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: 'id_usuario', referencedColumnName: 'id_usuario', nullable: false, onDelete: 'CASCADE')]
    private ?Usuario $usuario = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: 'id_pokemon', referencedColumnName: 'id_pokemon', nullable: false, onDelete: 'CASCADE')]
    private ?Pokemon $pokemon = null;

    /** Combate ganado en el que se capturó. */
    #[ORM\OneToOne]
    #[ORM\JoinColumn(name: 'id_combate', referencedColumnName: 'id_combate', nullable: true, onDelete: 'SET NULL')]
    private ?Combate $combate = null;

    #[ORM\Column]
    private \DateTimeImmutable $fecha;

    public function __construct()
    {
        $this->fecha = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsuario(): ?Usuario
    {
        return $this->usuario;
    }

    public function setUsuario(?Usuario $usuario): static
    {
        $this->usuario = $usuario;

        return $this;
    }

    public function getPokemon(): ?Pokemon
    {
        return $this->pokemon;
    }

    public function setPokemon(?Pokemon $pokemon): static
    {
        $this->pokemon = $pokemon;

        return $this;
    }

    public function getCombate(): ?Combate
    {
        return $this->combate;
    }

    public function setCombate(?Combate $combate): static
    {
        $this->combate = $combate;

        return $this;
    }

    public function getFecha(): \DateTimeImmutable
    {
        return $this->fecha;
    }
}
