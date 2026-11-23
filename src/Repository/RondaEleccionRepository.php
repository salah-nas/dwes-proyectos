<?php

namespace App\Repository;

use App\Entity\RondaEleccion;
use App\Entity\Usuario;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<RondaEleccion>
 */
class RondaEleccionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RondaEleccion::class);
    }

    /** Ronda del usuario que todavía no ha terminado en combate. */
    public function findPendiente(Usuario $usuario): ?RondaEleccion
    {
        return $this->createQueryBuilder('r')
            ->leftJoin('r.combate', 'c')
            ->andWhere('r.usuario = :usuario')
            ->andWhere('c.id IS NULL')
            ->setParameter('usuario', $usuario)
            ->orderBy('r.id', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
