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
 * Unique identities constraint for ACL
 * 
 * @Annotation
 * 
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class UniqueIdentities extends Constraint
{
    public $message = 'Each identity can be specified only once.';
    
    /**
     * {@inheritdoc}
     */
    public function getDefaultOption()
    {
        return 'message';
    }
}
