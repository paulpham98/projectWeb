<?php

namespace App\Repository;

use App\Entity\Teacher;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Teacher|null find($id, $lockMode = null, $lockVersion = null)
 * @method Teacher|null findOneBy(array $criteria, array $orderBy = null)
 * @method Teacher[]    findAll()
 * @method Teacher[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TeacherRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Teacher::class);
    }

    /**
     * @return Teacher[]
     */
    public function sortIdAsc()
    {
        return $this->createQueryBuilder('teacher')
            ->orderBy('teacher.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Teacher[]
     */
    public function sortIdDesc()
    {
        return $this->createQueryBuilder('teacher')
            ->orderBy('teacher.id', 'Desc')
            ->getQuery()
            ->getResult();
    }
    /**
     * @return Teacher[]
     */
    public function searchTeacher($name)
    {
        return $this->createQueryBuilder('teacher')
            ->andWhere('teacher.name LIKE :name')
            ->setParameter('name', '%' . $name . '%')
            ->orderBy('teacher.name', 'asc')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult();
    }

    
}
