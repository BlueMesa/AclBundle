<?php

/*
 * This file is part of the ACL Bundle.
 * 
 * Copyright (c) 2016 BlueMesa LabDB Contributors <labdb@bluemesa.eu>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Bluemesa\Bundle\AclBundle\Controller;


use Bluemesa\Bundle\AclBundle\Request\AclHandler;
use Symfony\Component\DependencyInjection\ContainerInterface;

trait AclControllerTrait
{
    /**
     * @return AclHandler
     */
    public function getAclHandler()
    {
        if (! $this->container instanceof ContainerInterface) {
            throw new \LogicException("Calling class must have container property set to ContainerInterface instance");
        }

        return $this->container->get('bluemesa.acl.handler');
    }
}
