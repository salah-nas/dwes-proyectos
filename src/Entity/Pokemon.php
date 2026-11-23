<?php

namespace App\Entity;

use App\Repository\PokemonRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PokemonRepository::class)]
#[ORM\Table(name: 'pokemon')]
class Pokemon
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_pokemon')]
    private ?int $id = null;

    #[ORM\Column(length: 60, unique: true)]
    private ?string $nombre = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $imagenUrl = null;

    #[ORM\Column]
    private int $hp = 0;

    #[ORM\Column]
    private int $ataque = 0;

    #[ORM\Column]
    private int $defensa = 0;

    #[ORM\Column]
    private int $velocidad = 0;

    /**
     * Tabla intermedia POKEMON_TIPO (relación N:M).
     *
     * @var Collection<int, Tipo>
     */
    #[ORM\ManyToMany(targetEntity: Tipo::class)]
    #[ORM\JoinTable(name: 'pokemon_tipo')]
    #[ORM\JoinColumn(name: 'id_pokemon', referencedColumnName: 'id_pokemon', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'id_tipo', referencedColumnName: 'id_tipo', onDelete: 'CASCADE')]
    private Collection $tipos;

    public function __construct()
    {
        $this->tipos = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): static
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function getImagenUrl(): ?string
    {
        return $this->imagenUrl;
    }

    public function setImagenUrl(?string $imagenUrl): static
    {
        $this->imagenUrl = $imagenUrl;

        return $this;
    }

    public function getHp(): int
    {
        return $this->hp;
    }

    public function setHp(int $hp): static
    {
        $this->hp = $hp;

        return $this;
    }

    public function getAtaque(): int
    {
        return $this->ataque;
    }

    public function setAtaque(int $ataque): static
    {
        $this->ataque = $ataque;

        return $this;
    }

    public function getDefensa(): int
    {
        return $this->defensa;
    }

    public function setDefensa(int $defensa): static
    {
        $this->defensa = $defensa;

        return $this;
    }

    public function getVelocidad(): int
    {
        return $this->velocidad;
    }

    public function setVelocidad(int $velocidad): static
    {
        $this->velocidad = $velocidad;

        return $this;
    }

    /** @return Collection<int, Tipo> */
    public function getTipos(): Collection
    {
        return $this->tipos;
    }

    public function addTipo(Tipo $tipo): static
    {
        if (!$this->tipos->contains($tipo)) {
            $this->tipos->add($tipo);
        }

        return $this;
    }

    public function removeTipo(Tipo $tipo): static
    {
        $this->tipos->removeElement($tipo);

        return $this;
    }
}
