<?php

/*
 * This file is part of the BluemesaCoreBundle.
 * 
 * Copyright (c) 2016 BlueMesa LabDB Contributors <labdb@bluemesa.eu>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bluemesa\Bundle\CoreBundle\Repository;

use JMS\DiExtraBundle\Annotation as DI;

use Bluemesa\Bundle\CoreBundle\Doctrine\SecureObjectManager;
use VIB\SecurityBundle\Bridge\Doctrine\AclFilter;

use Bluemesa\Bundle\CoreBundle\Filter\ListFilterInterface;
use Bluemesa\Bundle\CoreBundle\Filter\EntityFilterInterface;
use Bluemesa\Bundle\CoreBundle\Filter\SecureFilterInterface;

use Doctrine\ORM\EntityRepository as BaseEntityRepository;

/**
 * EntityRepository
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class EntityRepository extends BaseEntityRepository
{
    /**
     * @var VIB\SecurityBundle\Bridge\Doctrine\AclFilter $aclFilter
     */
    protected $aclFilter;

    /**
     * @var Bluemesa\Bundle\CoreBundle\Doctrine\SecureObjectManager $objectManager
     */
    protected $objectManager;
    
    
    /**
     * Set the ACL filter service
     *
     * @DI\InjectParams({ "aclFilter" = @DI\Inject("vib.security.filter.acl") })
     * 
     * @param VIB\SecurityBundle\Bridge\Doctrine\AclFilter $aclFilter
     */
    public function setAclFilter(AclFilter $aclFilter)
    {
        $this->aclFilter = $aclFilter;
    }
    
    /**
     * Return the ACL filter service
     * 
     * @return VIB\SecurityBundle\Bridge\Doctrine\AclFilter
     */
    protected function getAclFilter()
    {
        return $this->aclFilter;
    }
    
    /**
     * Set the Object manager service
     * 
     * @DI\InjectParams({ "objectManager" = @DI\Inject("bluemesa.core.doctrine.manager") })
     * 
     * @param Bluemesa\Bundle\CoreBundle\Doctrine\SecureObjectManager
     */
    public function setObjectManager(SecureObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }
    
    /**
     * Get the Object manager service
     * 
     * @return type Bluemesa\Bundle\CoreBundle\Doctrine\ObjectManager
     */
    protected function getObjectManager()
    {
        return $this->objectManager;
    }
    
    /**
     *
     * @param  Bluemesa\Bundle\CoreBundle\Filter\ListFilterInterface  $filter
     * @return Doctrine\Common\Collections\Collection
     */
    public function getList(ListFilterInterface $filter = null)
    {
        return $this->getListQuery($filter)->getResult();
    }

    /**
     *
     * @param  Bluemesa\Bundle\CoreBundle\Filter\ListFilterInterface  $filter
     * @return Doctrine\ORM\Query
     */
    public function getListQuery(ListFilterInterface $filter = null)
    {
        $qb = $this->getListQueryBuilder($filter);
        
        if ($filter instanceof SecureFilterInterface) {
            $permissions = $filter->getPermissions();
            $user = $filter->getUser();
        } else {
            $permissions = array();
            $user = null;
        }

        return (false === $permissions) ? $qb->getQuery() : $this->getAclFilter()->apply($qb, $permissions, $user);
    }

    /**
     *
     * @param  Bluemesa\Bundle\CoreBundle\Filter\ListFilterInterface  $filter
     * @return Doctrine\ORM\QueryBuilder
     */
    protected function getListQueryBuilder(ListFilterInterface $filter = null)
    {
        return $this->createQueryBuilder('e');
    }

    /**
     *
     * @param  Bluemesa\Bundle\CoreBundle\Filter\ListFilterInterface  $filter
     * @return integer
     */
    public function getListCount(ListFilterInterface $filter = null)
    {
        return $this->getCountQuery($filter)->getSingleScalarResult();
    }

    /**
     *
     * @param  Bluemesa\Bundle\CoreBundle\Filter\ListFilterInterface  $filter
     * @return Doctrine\ORM\Query
     */
    public function getCountQuery(ListFilterInterface $filter = null)
    {
        $qb = $this->getCountQueryBuilder($filter);
        
        if ($filter instanceof SecureFilterInterface) {
            $permissions = $filter->getPermissions();
            $user = $filter->getUser();
        } else {
            $permissions = array();
            $user = null;
        }
        
        return (false === $permissions) ? $qb->getQuery() : $this->getAclFilter()->apply($qb, $permissions, $user);
    }

    /**
     *
     * @param  Bluemesa\Bundle\CoreBundle\Filter\ListFilterInterface  $filter
     * @return Doctrine\ORM\QueryBuilder
     */
    protected function getCountQueryBuilder(ListFilterInterface $filter = null)
    {
        return $this->createQueryBuilder('e')
                ->select('count(e.id)');
    }

    /**
     * Get a single Entity by its id
     * 
     * @param  Bluemesa\Bundle\CoreBundle\Filter\EntityFilterInterface  $filter
     * @return Doctrine\ORM\QueryBuilder
     */
    public function getEntity($id, EntityFilterInterface $filter = null)
    {
        return $this->getEntityQueryBuilder($id, $filter)->getQuery()->getSingleResult();
    }

    /**
     * Get Entity Query Builder
     * 
     * @param  Bluemesa\Bundle\CoreBundle\Filter\EntityFilterInterface  $filter
     * @return Doctrine\ORM\QueryBuilder
     */
    protected function getEntityQueryBuilder($id, EntityFilterInterface $filter = null)
    {
        return $this->createQueryBuilder('e')
                ->where('e.id = :id')
                ->setParameter('id', $id);
    }
}
