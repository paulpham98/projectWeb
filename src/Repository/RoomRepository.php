<?php

namespace App\Repository;

use App\Entity\Room;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Room|null find($id, $lockMode = null, $lockVersion = null)
 * @method Room|null findOneBy(array $criteria, array $orderBy = null)
 * @method Room[]    findAll()
 * @method Room[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RoomRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Room::class);
    }
    /**
     * @return Room[]
     */
    public function sortIdAsc()
    {
        return $this->createQueryBuilder('room')
            ->orderBy('room.id', 'ASC')
            ->getQuery()
            ->getResult();
    }
    /**
     * @return Room[]
     */
    public function sortIdDesc()
    {
        return $this->createQueryBuilder('room')
            ->orderBy('room.id', 'DeSC')
            ->getQuery()
            ->getResult();
    }
    /**
     * @return Room[]
     */
    public function searchRoom($name)
    {
        return $this->createQueryBuilder('room')
            ->andWhere('room.name LIKE :name')
            ->setParameter('name', '%' . $name . '%')
            ->orderBy('room.name', 'asc')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult();
    }
}
