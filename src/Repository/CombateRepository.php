<?php

namespace App\Repository;

use App\Entity\Combate;
use App\Entity\Usuario;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Combate>
 */
class CombateRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Combate::class);
    }

    /** @return list<Combate> */
    public function historialDe(Usuario $usuario): array
    {
        return $this->findBy(['usuario' => $usuario], ['id' => 'DESC']);
    }
}
