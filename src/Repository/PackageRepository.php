<?php

namespace App\Repository;

use App\Dto\PackageSearchFilter;
use App\Entity\Package;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Package>
 */
class PackageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Package::class);
    }

    public function findByFilter(PackageSearchFilter $filter): array
    {
        $qb = $this->createQueryBuilder('p')
                    ->leftJoin('p.category','c')
                    ->addSelect('c')
                    ->leftJoin('p.business','b')
                    ->addSelect('b')
                    ->leftJoin('b.business_type','bt')
                    ->addSelect('bt');

        if($filter->getName()){
            $qb->andWhere('p.name LIKE :name')
                ->setParameter('name','%'.$filter->getName().'%');
        }

        if($filter->getMinPrice()){
            $qb->andWhere('p.price >= :minPrice')
                ->setParameter('minPrice', $filter->getMinPrice());
        }

        if($filter->getMaxPrice()){
            $qb->andWhere('p.price <= :maxPrice')
            ->setParameter('maxPrice',$filter->getMaxPrice());
        }

        if($filter->getCategory()){
            $qb->andWhere('p.category = :category')
                ->setParameter('category', $filter->getCategory());
        }

        if($filter->getBusiness()){
            $qb->andWhere('p.business = :business')
                ->setParameter('business', $filter->getBusiness());
        }

        if($filter->getBusinessType()){
            $qb->andWhere('b.businessType = :businessType')
                ->setParameter('businessType', $filter->getBusinessType());
        }

        if($filter->getCity()){
            $qb->andWhere('p.city LIKE :city')
                ->setParameter('city', '%'.$filter->getCity().'%');
        }

        return $qb->getQuery()->getResult();
    }

    //    /**
    //     * @return Package[] Returns an array of Package objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Package
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
