<?php

namespace Intex\OrgBundle\Entity\Repository;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * CompanyRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CompanyRepository extends \Doctrine\ORM\EntityRepository
{

    /**
     * @return array
     */
    public function getAllCompanies()
    {
        $qb = $this->createQueryBuilder('c')
            ->select('c')
            ->addOrderBy('c.name', 'ASC');
        return $qb->getQuery()
            ->getResult();
    }

     /**
     * Return companies from array $companies that exist in DB
     * @param ArrayCollection $companies Array organizations
     * @return array
     */
    public function getExistingCompanies(ArrayCollection $companies)
    {
        $companiesOgrns = $this->getOgrns($companies);

        $db = $this->createQueryBuilder('c')
            ->select('c')
            ->where('c.ogrn IN (:ogrns)')
            ->setParameter('ogrns', $companiesOgrns);

        return $db->getQuery()->getResult();
    }

    public function getOgrns($companies)
    {
        $ogrns = array();
        foreach ($companies as $organization){
            $ogrns[] = $organization->getOgrn();
        }
        return $ogrns;
    }
}
