<?php

namespace App\Entity;

use App\Repository\RondaEleccionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Cada vez que al cliente se le ofrecen 3 cartas ocultas.
 */
#[ORM\Entity(repositoryClass: RondaEleccionRepository::class)]
#[ORM\Table(name: 'ronda_eleccion')]
class RondaEleccion
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_ronda')]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: 'id_usuario', referencedColumnName: 'id_usuario', nullable: false, onDelete: 'CASCADE')]
    private ?Usuario $usuario = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: 'id_pokemon_elegido', referencedColumnName: 'id_pokemon', nullable: true, onDelete: 'SET NULL')]
    private ?Pokemon $pokemonElegido = null;

    #[ORM\Column]
    private \DateTimeImmutable $fecha;

    /** @var Collection<int, CartaOfrecida> */
    #[ORM\OneToMany(targetEntity: CartaOfrecida::class, mappedBy: 'ronda', cascade: ['persist', 'remove'])]
    #[ORM\OrderBy(['posicion' => 'ASC'])]
    private Collection $cartas;

    #[ORM\OneToOne(targetEntity: Combate::class, mappedBy: 'ronda')]
    private ?Combate $combate = null;

    public function __construct()
    {
        $this->fecha = new \DateTimeImmutable();
        $this->cartas = new ArrayCollection();
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

    public function getPokemonElegido(): ?Pokemon
    {
        return $this->pokemonElegido;
    }

    public function setPokemonElegido(?Pokemon $pokemonElegido): static
    {
        $this->pokemonElegido = $pokemonElegido;

        return $this;
    }

    public function getFecha(): \DateTimeImmutable
    {
        return $this->fecha;
    }

    /** @return Collection<int, CartaOfrecida> */
    public function getCartas(): Collection
    {
        return $this->cartas;
    }

    public function addCarta(CartaOfrecida $carta): static
    {
        if (!$this->cartas->contains($carta)) {
            $this->cartas->add($carta);
        }

        return $this;
    }

    public function getCartaEnPosicion(int $posicion): ?CartaOfrecida
    {
        foreach ($this->cartas as $carta) {
            if ($carta->getPosicion() === $posicion) {
                return $carta;
            }
        }

        return null;
    }

    public function getCombate(): ?Combate
    {
        return $this->combate;
    }
}
