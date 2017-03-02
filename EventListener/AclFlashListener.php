<?php

/*
 * This file is part of the CRUD Bundle.
 * 
 * Copyright (c) 2016 BlueMesa LabDB Contributors <labdb@bluemesa.eu>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Bluemesa\Bundle\AclBundle\EventListener;

use Bluemesa\Bundle\AclBundle\Event\AclControllerEvents;
use Bluemesa\Bundle\CrudBundle\Event\EntityEvent;
use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;


/**
 * The CrudAnnotationListener handles Pagination annotation for controllers.
 *
 * @DI\Service("bluemesa.acl.listener.flash")
 * @DI\Tag("kernel.event_subscriber")
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class AclFlashListener implements EventSubscriberInterface
{
    /**
     * @var string[]
     */
    private static $successMessages = array(
        AclControllerEvents::PERMISSIONS_SUCCESS => "Changes to %s %s permissions were saved.",
    );

    /**
     * @var SessionInterface
     */
    protected $session;


    /**
     * Constructor.
     *
     * @DI\InjectParams({
     *     "session" = @DI\Inject("session"),
     * })
     *
     * @param SessionInterface $session
     */
    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents()
    {
        return array(
            AclControllerEvents::PERMISSIONS_SUCCESS => array('addSessionFlash', 100),
        );
    }

    /**
     * @param Event   $event
     * @param string  $eventName
     */
    public function addSessionFlash(Event $event, $eventName)
    {
        if (!$event instanceof EntityEvent) {
            return;
        }

        if (!isset(self::$successMessages[$eventName])) {
            throw new \InvalidArgumentException('This event does not correspond to a known flash message');
        }

        $request = $event->getRequest();
        $entity = $event->getEntity();
        $name = strtolower($request->get('entity_name', 'entity'));
        $message = ucfirst(sprintf(self::$successMessages[$eventName], $name, $entity));

        if ($this->session instanceof Session) {
            $this->session->getFlashBag()->add('success', $message);
        } else {
            throw new \InvalidArgumentException("Session should be an instance of Session");
        }
    }
}
