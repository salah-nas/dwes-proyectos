<?php

namespace App\Repository;

use App\Entity\Captura;
use App\Entity\Pokemon;
use App\Entity\Usuario;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Captura>
 */
class CapturaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Captura::class);
    }

    /**
     * Capturas del usuario indexadas por id de Pokémon.
     *
     * @return array<int, Captura>
     */
    public function porPokemon(Usuario $usuario): array
    {
        $mapa = [];
        foreach ($this->findBy(['usuario' => $usuario]) as $captura) {
            $mapa[(int) $captura->getPokemon()?->getId()] = $captura;
        }

        return $mapa;
    }

    public function existe(Usuario $usuario, Pokemon $pokemon): bool
    {
        return null !== $this->findOneBy(['usuario' => $usuario, 'pokemon' => $pokemon]);
    }
}
