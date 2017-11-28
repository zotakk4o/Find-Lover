<?php

namespace FindLoverBundle\Repository;

use FindLoverBundle\Entity\Lover;

/**
 * LoverRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class LoverRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * @param array $friendsIds
     *
     * @return Lover[]|[]
     */
    public function findRecentlyAvailable($friendsIds)
    {
        return $this->getEntityManager()
                                 ->getRepository('FindLoverBundle:Lover')
                                 ->createQueryBuilder('l')
                                 ->select('l.id', 'l.firstName', 'l.lastName', 'l.nickname', 'l.profilePicture', 'l.lastOnline')
                                 ->where('l.id IN (:ids)')
                                 ->andWhere("l.lastOnline is NULL")
                                 ->orWhere('l.id IN (:ids)')
                                 ->andWhere('l.lastOnline >= :datePrevHour')
                                 ->setParameters(
                                     array(
                                         'ids' => $friendsIds,
                                         'datePrevHour' => date('Y-m-d H:i:s', strtotime('-1 hour'))
                                     )
                                 )
                                 ->getQuery()
                                 ->getResult();
    }

    /**
     * @var $params array
     *
     * @return Lover[]|[]
     *
     */
    public function extractRecentSearches($params)
    {
        return $this->getEntityManager()
                    ->getRepository('FindLoverBundle:Lover')
                    ->createQueryBuilder('l')
                    ->select('l.id', 'l.firstName', 'l.lastName', 'l.nickname', 'l.profilePicture')
                    ->where('l.id IN (:ids)')
                    ->setParameter('ids', $params['ids'])
                    ->setFirstResult($params['offset'])
                    ->setMaxResults(6)
                    ->getQuery()
                    ->getResult();

    }
}
