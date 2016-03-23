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
     * Get token storage
     *
     * @return \Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface
     */
    protected function getTokenStorage()
    {
        return $this->get('security.token_storage');
    }

    /**
     * Get authorization checker
     *
     * @return \Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface
     */
    protected function getAuthorizationChecker()
    {
        return $this->get('security.authorization_checker');
    }

    /**
     * Get ACL filter
     *
     * @return \Bluemesa\Bundle\AclBundle\Bridge\Doctrine\AclFilter
     */
    protected function getAclFilter()
    {
        return $this->get('bluemesa.acl.filter');
    }
}
