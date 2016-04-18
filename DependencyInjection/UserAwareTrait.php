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
use LogicException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;


/**
 * UserProviderAwareTrait
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
trait UserAwareTrait {
    
    /**
     * Get user from the Security Context
     *
     * @throws LogicException  If SecurityBundle is not available
     * 
     * @return mixed
     */
    protected function getUser()
    {
        if (null === $this->getTokenStorage()) {
            throw new LogicException('The SecurityBundle is not registered in your application.');
        }

        if (null === $token = $this->getTokenStorage()->getToken()) {

            return null;
        }

        if (!is_object($user = $token->getUser())) {

            return null;
        }

        return $user;
    }
    
    /**
     * Get tokenStorage
     *
     * @return TokenStorageInterface
     */
    abstract protected function getTokenStorage();
}
