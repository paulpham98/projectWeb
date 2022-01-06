<?php

namespace App\Repository;

use App\Entity\CourseDetail;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CourseDetail|null find($id, $lockMode = null, $lockVersion = null)
 * @method CourseDetail|null findOneBy(array $criteria, array $orderBy = null)
 * @method CourseDetail[]    findAll()
 * @method CourseDetail[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CourseDetailRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CourseDetail::class);
    }

    // /**
    //  * @return CourseDetail[] Returns an array of CourseDetail objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?CourseDetail
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

     /**
     * @return CourseDetail[]
     */
    public function searchCourseDetail($name) 
    {
        return $this->createQueryBuilder('course')
                    ->andWhere('course.name LIKE :name')
                    ->setParameter('name', '%' . $name . '%')
                    ->orderBy('course.name', 'asc')
                    ->setMaxResults(4)
                    ->getQuery()
                    ->getResult()
        ;
    }
}
