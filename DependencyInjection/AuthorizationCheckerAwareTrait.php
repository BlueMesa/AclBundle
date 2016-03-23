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
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;


/**
 * AuthorizationCheckerAwareTrait
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
trait AuthorizationCheckerAwareTrait {
    
    /**
     * @var \Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface
     */
    protected $authorizationChecker;
    
    /**
     * Set securityContext
     *
     * @DI\InjectParams({"authorizationChecker" = @DI\Inject("security.authorization_checker", required=false)})
     * 
     * @param \Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface  $authorizationChecker
     */
    public function setAuthorizationChecker(AuthorizationCheckerInterface $authorizationChecker = null)
    {
        $this->authorizationChecker = $authorizationChecker;
    }
    
    /**
     * Get security context
     *
     * @return \Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface
     */
    protected function getAuthorizationChecker()
    {
        return $this->authorizationChecker;
    }
}
