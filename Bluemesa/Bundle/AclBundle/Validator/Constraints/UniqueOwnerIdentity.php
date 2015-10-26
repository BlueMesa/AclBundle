<?php

/*
 * This file is part of the BluemesaCoreBundle.
 * 
 * Copyright (c) 2016 BlueMesa LabDB Contributors <labdb@bluemesa.eu>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bluemesa\Bundle\CoreBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;


/**
 * Unique owner identity constraint for ACL
 * 
 * @Annotation
 * 
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class UniqueOwnerIdentity extends Constraint
{
    public $message = 'Only one identity can be the owner.';
    
    /**
     * {@inheritdoc}
     */
    public function getDefaultOption()
    {
        return 'message';
    }
}
