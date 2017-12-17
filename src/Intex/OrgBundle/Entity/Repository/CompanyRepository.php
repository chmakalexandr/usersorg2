<?php

namespace Intex\OrgBundle\Entity\Repository;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * CompanyRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CompanyRepository extends \Doctrine\ORM\EntityRepository
{
    private $fieldSorting = array('name','ogrn','oktmo');

    /**
     * @return array
     */
    public function getAllCompanies($field = 'name',$order = 'ASC',$currentPage = 1, $limit = 5)
    {
        if (in_array($field, $this->fieldSorting)) {
            $field = 'c.' . $field;
            $qb = $this->createQueryBuilder('c')
                ->select('c')
                ->addOrderBy($field, $order);

            $paginator = new Paginator($qb);

            $paginator->getQuery()
                ->setFirstResult($limit * ($currentPage - 1))// Offset
                ->setMaxResults($limit); // Limit

            return $paginator;
        }
        return null;
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
