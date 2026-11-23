<?php

namespace App\Entity;

use App\Repository\CartaOfrecidaRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * Las 3 cartas de una ronda. Clave primaria compuesta (id_ronda, id_pokemon).
 */
#[ORM\Entity(repositoryClass: CartaOfrecidaRepository::class)]
#[ORM\Table(name: 'carta_ofrecida')]
class CartaOfrecida
{
    #[ORM\Id]
    #[ORM\ManyToOne(inversedBy: 'cartas')]
    #[ORM\JoinColumn(name: 'id_ronda', referencedColumnName: 'id_ronda', nullable: false, onDelete: 'CASCADE')]
    private ?RondaEleccion $ronda = null;

    #[ORM\Id]
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: 'id_pokemon', referencedColumnName: 'id_pokemon', nullable: false, onDelete: 'CASCADE')]
    private ?Pokemon $pokemon = null;

    #[ORM\Column]
    private int $posicion = 1;

    #[ORM\Column]
    private bool $elegida = false;

    public function getRonda(): ?RondaEleccion
    {
        return $this->ronda;
    }

    public function setRonda(?RondaEleccion $ronda): static
    {
        $this->ronda = $ronda;

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

    public function getPosicion(): int
    {
        return $this->posicion;
    }

    public function setPosicion(int $posicion): static
    {
        $this->posicion = $posicion;

        return $this;
    }

    public function isElegida(): bool
    {
        return $this->elegida;
    }

    public function setElegida(bool $elegida): static
    {
        $this->elegida = $elegida;

        return $this;
    }
}
