<?php

namespace App\Repository;

use App\Entity\Oeuvres;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Oeuvres>
 *
 * @method Oeuvres|null find($id, $lockMode = null, $lockVersion = null)
 * @method Oeuvres|null findOneBy(array $criteria, array $orderBy = null)
 * @method Oeuvres[]    findAll()
 * @method Oeuvres[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OeuvresRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Oeuvres::class);
    }

//    /**
//     * @return Oeuvres[] Returns an array of Oeuvres objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('o')
//            ->andWhere('o.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('o.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Oeuvres
//    {
//        return $this->createQueryBuilder('o')
//            ->andWhere('o.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
    /**
     * @param string|null $categorie
     * @param string|null $titre
     * @return Oeuvres[]
     */
    public function findByFilters(?string $categorie, ?string $titre): array
    {
        $queryBuilder = $this->createQueryBuilder('o');

        if ($categorie !== null && $categorie !== 'All') {
            $queryBuilder
                ->andWhere('o.categorie = :categorie')
                ->setParameter('categorie', $categorie);
        }

        if ($titre !== null) {
            $queryBuilder
                ->andWhere('o.titre LIKE :titre')
                ->setParameter('titre', '%'.$titre.'%');
        }

        return $queryBuilder->getQuery()->getResult();
    }
}
