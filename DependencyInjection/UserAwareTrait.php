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


/**
 * UserProviderAwareTrait
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
trait UserAwareTrait {
    
    /**
     * Get user from the Security Context
     *
     * @throws \LogicException If SecurityBundle is not available
     * 
     * @return mixed
     */
    protected function getUser()
    {
        if (null === $this->getSecurityContext()) {
            throw new \LogicException('The SecurityBundle is not registered in your application.');
        }

        if (null === $token = $this->getSecurityContext()->getToken()) {
            return;
        }

        if (!is_object($user = $token->getUser())) {
            return;
        }

        return $user;
    }
    
    /**
     * Get security context
     *
     * @return Symfony\Component\Security\Core\SecurityContextInterface
     */
    abstract protected function getSecurityContext();
}
