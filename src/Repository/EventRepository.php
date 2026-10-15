<?php

namespace App\Repository;

use App\Entity\Event;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class EventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Event::class);
    }

    public function findUpcomingValidated(?string $categorie = null): array
    {
        $qb = $this->createQueryBuilder('e')
            ->andWhere('e.isValidated = :validated')
            ->andWhere('e.date >= :now')
            ->setParameter('validated', true)
            ->setParameter('now', new \DateTimeImmutable())
            ->orderBy('e.date', 'ASC');

        if ($categorie !== null && in_array($categorie, [
            'concert',
            'conference',
            'atelier',
            'sport'
        ], true)) {
            $qb->andWhere('e.category = :categorie')
               ->setParameter('categorie', $categorie);
        }

        return $qb->getQuery()->getResult();
    }
}
