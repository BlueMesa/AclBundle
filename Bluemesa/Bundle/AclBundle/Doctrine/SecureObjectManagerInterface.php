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
     * Get object's owner
     *
     * @param  object                                             $object
     * @return Symfony\Component\Security\Core\User\UserInterface
     */
    public function getOwner($object);
    
    /**
     * Set object's owner
     *
     * @param  object                                             $objects
     * @param  Symfony\Component\Security\Core\User\UserInterface $owner
     */
    public function setOwner($objects, $owner);
    
    /**
     * Get object's group
     *
     * @param  object $object
     * @return string
     */
    public function getGroup($object);
    
    /**
     * Set object's group
     *
     * @param  object                                             $objects
     * @param  string                                             $group
     * @return Symfony\Component\Security\Core\User\UserInterface
     */
    public function setGroup($objects, $group);
    
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
     * @param object                                              $object
     * @param Symfony\Component\Security\Core\User\UserInterface  $user
     * @return array
     */
    public function getDefaultACL($object = null, $user = null);
    
}
