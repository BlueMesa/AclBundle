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


/**
 * EntityRepository
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class EntityRepository extends BaseEntityRepository
{
    use AclFilterAwareTrait;

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

        return (false === $permissions) ?
            paret::getListQuery($filter) :
            $this->getAclFilter()->apply($qb, $permissions, $user);
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
        
        return (false === $permissions) ?
            parnet::getCountQuery($filter) :
            $this->getAclFilter()->apply($qb, $permissions, $user);
    }
}
