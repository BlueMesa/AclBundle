<?php

/*
 * This file is part of the BluemesaAclBundle.
 * 
 * Copyright (c) 2016 BlueMesa LabDB Contributors <labdb@bluemesa.eu>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bluemesa\Bundle\AclBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Secure Entity class
 *
 * @ORM\MappedSuperclass
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class OwnedEntity extends SecureEntity implements OwnedEntityInterface
{
}
