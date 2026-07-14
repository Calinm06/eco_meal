<?php

namespace App\Repository;

use App\Entity\Business;
use App\Entity\Category;
use App\Entity\Order;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Business>
 */
class BusinessRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Business::class);
    }

    public function getNumberOfOrders(Business $business): int //numarul total de comenzi pentru un business
    {
        return $this->getEntityManager()->createQueryBuilder()
                    ->select('COUNT(o.consumer)')
                    ->from(Order::class,'o')
                    ->innerJoin('o.package_id','package')
                    ->where('package.business = :business')
                    ->setParameter('business', $business)
                    ->getQuery()
                    ->getSingleScalarResult();

    }

    public function getTotalSum(Business $business): int
    {
        return $this->getEntityManager()->createQueryBuilder()
                    ->select('SUM(package.price)')
                    ->from(Order::class,'o')
                    ->innerJoin('o.package_id','package')
                    ->where('package.business = :business')
                    ->setParameter('business', $business)
                    ->getQuery()
                    ->getSingleScalarResult();

    }

    public function getMostBoughtCategory(Business $business): array
    {
        return $this->getEntityManager()->createQueryBuilder()
                    ->select('o')
                    ->from(Order::class, 'o')
                    ->innerJoin('o.package_id','package')
                    ->innerJoin('package.category','category')
                    ->where('package.business = :business')
                    ->setParameter('business', $business)
                    ->groupBy('category.id')
                    ->orderBy('COUNT(o.id)', 'DESC')
                    ->setMaxResults(1)
                    ->getQuery()
                    ->getResult();
    }

    public function getLastDaysOrders(Business $business): array //afiseaza comenzile din ultimele 7 zile
    {
        $date = new \DateTimeImmutable('-7 days');
        return $this->getEntityManager()->createQueryBuilder()
                    ->select('o')
                    ->from(Order::class,'o')
                    ->innerJoin('o.package_id','package')
                    ->where('package.business = :business')
                    ->setParameter('business',$business)
                    ->andWhere('o.created_at > :date')
                    ->setParameter('date', $date)
                    ->getQuery()
                    ->getResult();

    }

    //    /**
    //     * @return Business[] Returns an array of Business objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('b')
    //            ->andWhere('b.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('b.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Business
    //    {
    //        return $this->createQueryBuilder('b')
    //            ->andWhere('b.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
