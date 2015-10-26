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
use Symfony\Component\Security\Acl\Permission\MaskBuilder;

/**
 * Unique owner identity validator for ACL
 * 
 * @Annotation
 * 
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class UniqueOwnerIdentityValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        $count = 0;
        foreach ($value as $entry) {
            if ($entry['permission'] == MaskBuilder::MASK_OWNER) {
                $count++;
                if ($count > 1) {
                    $this->context->addViolation($constraint->message);
                    break;
                }
            }
        }
    }
}
