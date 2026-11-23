<?php

namespace App\Entity;

use App\Repository\CombateRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CombateRepository::class)]
#[ORM\Table(name: 'combate')]
class Combate
{
    public const VICTORIA = 'victoria';
    public const DERROTA = 'derrota';
    public const EMPATE = 'empate';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_combate')]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: 'id_usuario', referencedColumnName: 'id_usuario', nullable: false, onDelete: 'CASCADE')]
    private ?Usuario $usuario = null;

    /** Una ronda origina como máximo un combate (relación 1 : 0..1). */
    #[ORM\OneToOne(inversedBy: 'combate')]
    #[ORM\JoinColumn(name: 'id_ronda', referencedColumnName: 'id_ronda', nullable: false, unique: true, onDelete: 'CASCADE')]
    private ?RondaEleccion $ronda = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: 'id_pokemon_jugador', referencedColumnName: 'id_pokemon', nullable: false, onDelete: 'CASCADE')]
    private ?Pokemon $pokemonJugador = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: 'id_pokemon_rival', referencedColumnName: 'id_pokemon', nullable: false, onDelete: 'CASCADE')]
    private ?Pokemon $pokemonRival = null;

    #[ORM\Column(length: 20)]
    private string $resultado = self::EMPATE;

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

    public function getRonda(): ?RondaEleccion
    {
        return $this->ronda;
    }

    public function setRonda(?RondaEleccion $ronda): static
    {
        $this->ronda = $ronda;

        return $this;
    }

    public function getPokemonJugador(): ?Pokemon
    {
        return $this->pokemonJugador;
    }

    public function setPokemonJugador(?Pokemon $pokemonJugador): static
    {
        $this->pokemonJugador = $pokemonJugador;

        return $this;
    }

    public function getPokemonRival(): ?Pokemon
    {
        return $this->pokemonRival;
    }

    public function setPokemonRival(?Pokemon $pokemonRival): static
    {
        $this->pokemonRival = $pokemonRival;

        return $this;
    }

    public function getResultado(): string
    {
        return $this->resultado;
    }

    public function setResultado(string $resultado): static
    {
        $this->resultado = $resultado;

        return $this;
    }

    public function getFecha(): \DateTimeImmutable
    {
        return $this->fecha;
    }
}
