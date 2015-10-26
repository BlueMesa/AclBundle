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
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Unique identity validator for ACL
 * 
 * @Annotation
 * 
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class UniqueIdentitiesValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        $identities = array();
        foreach ($value as $entry) {
            if (in_array($entry['identity'], $identities)) {
                $this->context->addViolation($constraint->message);
            } else {
                $identities[] = $entry['identity'];
            }
        }
    }
}
