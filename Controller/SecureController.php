<?php

/*
 * This file is part of the BluemesaAclBundle.
 * 
 * Copyright (c) 2016 BlueMesa LabDB Contributors <labdb@bluemesa.eu>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bluemesa\Bundle\AclBundle\Controller;

/**
 * Methods for secure controllers
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
trait SecureController
{
    /**
     * Get security context
     *
     * @return Symfony\Component\Security\Core\SecurityContextInterface
     */
    protected function getSecurityContext()
    {
        return $this->get('security.context');
    }

    /**
     * Get ACL filter
     *
     * @return VIB\SecurityBundle\Bridge\Doctrine\AclFilter
     */
    protected function getAclFilter()
    {
        return $this->get('bluemesa.acl.filter');
    }
}
