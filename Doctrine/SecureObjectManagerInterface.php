<?php

/*
 * This file is part of the BluemesaAclBundle.
 * 
 * Copyright (c) 2016 BlueMesa LabDB Contributors <labdb@bluemesa.eu>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bluemesa\Bundle\AclBundle\Doctrine;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * ACL aware ObjectManagerInterface
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
interface SecureObjectManagerInterface {
    
    /**
     * Create ACL for object(s)
     *
     * @param object $objects
     * @param array  $acl_param
     */
    public function createACL($objects, $acl_param = null);

    /**
     * Delete ACL for object(s)
     *
     * @param object $objects
     */
    public function removeACL($objects);
    
    /**
     * Update ACL for object(s)
     *
     * @param object $objects
     */
    public function updateACL($objects, array $acl_array);
    
    /**
     * Get ACL for object
     *
     * @return array
     */
    public function getACL($object);
    
    /**
     * Enable automatic ACL setting
     */
    public function enableAutoAcl();
    
    /**
     * Disable automatic ACL setting
     */
    public function disableAutoAcl();
    
    /**
     * Is automatic ACL setting enabled
     * 
     * @return boolean
     */
    public function isAutoAclEnabled();
    
    /**
     * Get default ACL
     * 
     * @param object         $object
     * @param UserInterface  $user
     * @return array
     */
    public function getDefaultACL($object = null, $user = null);
    
}
