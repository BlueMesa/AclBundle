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
use Symfony\Component\Security\Core\User\UserProviderInterface;


/**
 * UserProviderAwareTrait
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
trait UserProviderAwareTrait {
    
    /**
     * @var Symfony\Component\Security\Core\User\UserProviderInterface
     */
    protected $userProvider;
    
    /**
     * Set userProvider
     *
     * @DI\InjectParams({"userProvider" = @DI\Inject("user_provider")})
     * 
     * @param Symfony\Component\Security\Core\User\UserProviderInterface  $userProvider
     */
    public function setUserProvider(UserProviderInterface $userProvider)
    {
        $this->userProvider = $userProvider;
    }
}
