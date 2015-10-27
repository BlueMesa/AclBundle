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
 * Ownership aware ObjectManagerInterface
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
interface OwnedObjectManagerInterface {
    
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
    
}
