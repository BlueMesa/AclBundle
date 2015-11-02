<?php

/*
 * This file is part of the BluemesaAclBundle.
 * 
 * Copyright (c) 2016 BlueMesa LabDB Contributors <labdb@bluemesa.eu>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bluemesa\Bundle\AclBundle\DependencyInjection;

use JMS\DiExtraBundle\Annotation as DI;
use Bluemesa\Bundle\AclBundle\Bridge\Doctrine\AclFilter;

/**
 * AclFilterAwareTrait
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
trait AclFilterAwareTrait {
    
    /**
     * @var VIB\SecurityBundle\Bridge\Doctrine\AclFilter $aclFilter
     */
    protected $aclFilter;
    
    
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
}
