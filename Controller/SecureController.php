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

use Bluemesa\Bundle\AclBundle\Bridge\Doctrine\AclFilter;
use LogicException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;


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
     * @deprecated Use getUser method from Controller directly
     *
     * @return TokenStorageInterface
     * @throws LogicException
     */
    protected function getTokenStorage()
    {
        if (!$this instanceof Controller) {
            throw new LogicException('This method can only be used in controllers.');
        }
        if (!$this->container->has('security.token_storage')) {
            throw new LogicException('The SecurityBundle is not registered in your application.');
        }

        return $this->container->get('security.token_storage');
    }

    /**
     * Get authorization checker
     *
     * @deprecated Use isGranted method from Controller directly
     *
     * @return AuthorizationCheckerInterface
     */
    protected function getAuthorizationChecker()
    {
        if (!$this instanceof Controller) {
            throw new LogicException('This method can only be used in controllers.');
        }
        if (!$this->container->has('security.authorization_checker')) {
            throw new LogicException('The SecurityBundle is not registered in your application.');
        }

        return $this->container->get('security.authorization_checker');
    }

    /**
     * Get ACL filter
     *
     * @return AclFilter
     */
    protected function getAclFilter()
    {
        if (!$this instanceof Controller) {
            throw new LogicException('This method can only be used in controllers.');
        }

        return $this->container->get('bluemesa.acl.filter');
    }
}
