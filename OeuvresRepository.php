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
   // Méthode pour compter le nombre total des réclamations
   public function countTotalOeuvres(): int
   {
       // Sélectionne le nombre total de réclamations en utilisant une requête de comptage SQL
       return $this->createQueryBuilder('r')
           ->select('COUNT(r.idoeuvre)')
           ->getQuery()
           ->getSingleScalarResult();
   }

    // Méthode pour compter le nombre de réclamation par rapport au type
    public function countOeuvresByType(string $categorie): int
    {
        // Sélectionne le nombre de réclamations pour un type spécifique en utilisant une requête de comptage SQL avec une clause WHERE
        return $this->createQueryBuilder('r')
            ->select('COUNT(r.idoeuvre)')
            ->andWhere('r.categorie = :categorie')
            ->setParameter('categorie', $categorie)
            ->getQuery()
            ->getSingleScalarResult();
    }

    // Méthode pour calculer la moyenne des réclamations par type
    public function averageOeuvreByType(string $categorie): float
    {
        // Obtient le nombre total de réclamations de ce type
        $totalOeuvreOfType = $this->countOeuvresByType($categorie);
        
        // Obtient le nombre total de réclamations
        $totalOeuvre = $this->countTotalOeuvres();
        
        // Vérifie si le nombre total de réclamations est différent de zéro pour éviter une division par zéro
        if ($totalOeuvre === 0) {
            return 0; // Retourne 0 pour éviter une division par zéro
        }

        // Calcule la moyenne des réclamations pour ce type par rapport à l'ensemble des réclamations
        return ($totalOeuvreOfType / $totalOeuvre) * 100; // Calcule le pourcentage
    }

}
