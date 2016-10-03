<?php

/*
 * This file is part of the BluemesaAclBundle.
 * 
 * Copyright (c) 2016 BlueMesa LabDB Contributors <labdb@bluemesa.eu>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bluemesa\Bundle\AclBundle\Repository;


use Bluemesa\Bundle\AclBundle\DependencyInjection\AclFilterAwareTrait;
use Bluemesa\Bundle\CoreBundle\Filter\ListFilterInterface;
use Bluemesa\Bundle\CoreBundle\Repository\EntityRepository as BaseEntityRepository;
use Bluemesa\Bundle\AclBundle\Filter\SecureFilterInterface;
use Bluemesa\Bundle\CoreBundle\Repository\FilteredRepositoryInterface;
use Bluemesa\Bundle\CoreBundle\Repository\FilteredRepositoryTrait;


/**
 * EntityRepository
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class EntityRepository extends BaseEntityRepository implements FilteredRepositoryInterface
{
    use AclFilterAwareTrait;
    use FilteredRepositoryTrait;

    /**
     *
     * @param  \Bluemesa\Bundle\CoreBundle\Filter\ListFilterInterface  $filter
     * @return \Doctrine\ORM\Query
     */
    public function getListQuery(ListFilterInterface $filter = null)
    {
        if ((null === $filter)&&($this->filter instanceof ListFilterInterface)) {
            $filter = $this->filter;
        }

        $qb = $this->getListQueryBuilder($filter);
        
        if ($filter instanceof SecureFilterInterface) {
            $permissions = $filter->getPermissions();
            $user = $filter->getUser();
        } else {
            $permissions = array();
            $user = null;
        }

        return (false === $permissions) ?
            parent::getListQuery($filter) :
            $this->getAclFilter()->apply($qb, $permissions, $user);
    }

    /**
     *
     * @param  \Bluemesa\Bundle\CoreBundle\Filter\ListFilterInterface  $filter
     * @return \Doctrine\ORM\Query
     */
    public function getCountQuery(ListFilterInterface $filter = null)
    {
        if ((null === $filter)&&($this->filter instanceof ListFilterInterface)) {
            $filter = $this->filter;
        }

        $qb = $this->getCountQueryBuilder($filter);
        
        if ($filter instanceof SecureFilterInterface) {
            $permissions = $filter->getPermissions();
            $user = $filter->getUser();
        } else {
            $permissions = array();
            $user = null;
        }
        
        return (false === $permissions) ?
            parent::getCountQuery($filter) :
            $this->getAclFilter()->apply($qb, $permissions, $user);
    }
}
