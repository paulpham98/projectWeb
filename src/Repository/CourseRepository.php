<?php

namespace App\Repository;

use App\Entity\Course;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Mapping\OrderBy;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Course|null find($id, $lockMode = null, $lockVersion = null)
 * @method Course|null findOneBy(array $criteria, array $orderBy = null)
 * @method Course[]    findAll()
 * @method Course[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CourseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Course::class);
    }

    /**
     * @return Course[] 
     */
    public function sortDESC($a)
    {
        return $this->createQueryBuilder('course')
            ->orderBy('course' . '.' . $a, 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Course[] 
     */
    public function sortASC($a)
    {
        return $this->createQueryBuilder('course')
            ->orderBy('course' . '.' . $a, 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Course[] 
     */
    public function searchCourse($value)
    {
        return $this->createQueryBuilder('entity')
            ->andWhere('entity.name LIKE :value')
            ->setParameter('value', '%' . $value . '%')
            ->getQuery()
            ->getResult();
    }



    /*
    public function findOneBySomeField($value): ?Course
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
