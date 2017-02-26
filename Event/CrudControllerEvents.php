<?php

/*
 * This file is part of the CRUD Bundle.
 * 
 * Copyright (c) 2016 BlueMesa LabDB Contributors <labdb@bluemesa.eu>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Bluemesa\Bundle\AclBundle\Event;


final class AclControllerEvents
{
    /**
     * @Event
     */
    const PERMISSIONS_INITIALIZE = 'bluemesa.controller.permissions_initialize';

    /**
     * @Event
     */
    const PERMISSIONS_SUBMITTED = 'bluemesa.controller.permissions_submitted';

    /**
     * @Event
     */
    const PERMISSIONS_SUCCESS = 'bluemesa.controller.permissions_success';

    /**
     * @Event
     */
    const PERMISSIONS_COMPLETED = 'bluemesa.controller.permissions_completed';
}
