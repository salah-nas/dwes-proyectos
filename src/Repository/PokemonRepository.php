<?php

namespace App\Repository;

use App\Entity\Pokemon;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Pokemon>
 */
class PokemonRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Pokemon::class);
    }

    /** @return list<int> */
    private function todosLosIds(): array
    {
        $filas = $this->createQueryBuilder('p')->select('p.id')->getQuery()->getScalarResult();

        return array_map('intval', array_column($filas, 'id'));
    }

    /**
     * Devuelve $cantidad Pokémon distintos, elegidos al azar de todo el catálogo.
     *
     * @return list<Pokemon>
     */
    public function aleatorios(int $cantidad): array
    {
        $ids = $this->todosLosIds();
        if (count($ids) < $cantidad) {
            return [];
        }

        shuffle($ids);
        $pokemons = $this->findBy(['id' => array_slice($ids, 0, $cantidad)]);
        shuffle($pokemons);

        return array_values($pokemons);
    }

    /** Un Pokémon al azar de cualquier tipo (sin filtrar). */
    public function unoAleatorio(): ?Pokemon
    {
        $ids = $this->todosLosIds();
        if ([] === $ids) {
            return null;
        }

        return $this->find($ids[array_rand($ids)]);
    }
}
