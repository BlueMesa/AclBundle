<?php

/*
 * This file is part of the ACL Bundle.
 * 
 * Copyright (c) 2017 BlueMesa LabDB Contributors <labdb@bluemesa.eu>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Bluemesa\Bundle\AclBundle\Request;


use Bluemesa\Bundle\AclBundle\Doctrine\SecureObjectManagerInterface;
use Bluemesa\Bundle\AclBundle\Event\AclControllerEvents;
use Bluemesa\Bundle\AclBundle\Event\PermissionsActionEvent;
use Bluemesa\Bundle\AclBundle\Form\AclType;
use Bluemesa\Bundle\CoreBundle\Entity\Entity;
use Bluemesa\Bundle\CoreBundle\EventListener\RoutePrefixTrait;
use Bluemesa\Bundle\CoreBundle\Request\AbstractHandler;
use Bluemesa\Bundle\CoreBundle\Request\FormHandlerTrait;
use FOS\RestBundle\View\View;
use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\HttpFoundation\Request;


/**
 * Class AclHandler
 *
 * @DI\Service("bluemesa.acl.handler")
 *
 * @package Bluemesa\Bundle\AclBundle\Request
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class AclHandler extends AbstractHandler
{
    use RoutePrefixTrait;
    use FormHandlerTrait;

    /**
     * This method calls a proper handler for the incoming request
     *
     * @param Request $request
     *
     * @return View
     * @throws \LogicException
     */
    public function handle(Request $request)
    {
        $action = $request->get('action');
        switch($action) {
            case 'permissions':
                $result = $this->handlePermissionsAction($request);
                break;
            default:
                $message  = "The action '" . $action;
                $message .= "' is not one of the allowed ACL actions ('permissions').";
                throw new \LogicException($message);
        }

        return $result;
    }

    /**
     * This method handles permissions action requests.
     *
     * @param Request $request
     *
     * @return View
     */
    public function handlePermissionsAction(Request $request)
    {
        $entity = $request->get('entity');
        $om = $this->registry->getManagerForClass($entity);
        if (! $om instanceof SecureObjectManagerInterface) {
            throw new \LogicException("Permissions can only be modified in entities managed " .
                "by an instance of SecureObjectManagerInterface");
        }
        $form = $this->factory->create(AclType::class, $om->getACL($entity), array('method' => 'PUT'));

        $events = array(
            'class' => PermissionsActionEvent::class,
            'initialize' => AclControllerEvents::PERMISSIONS_INITIALIZE,
            'submitted' => AclControllerEvents::PERMISSIONS_SUBMITTED,
            'success' => AclControllerEvents::PERMISSIONS_SUCCESS,
            'completed' => AclControllerEvents::PERMISSIONS_COMPLETED
        );

        $handler = function(Request $request, EntityEvent $event) use ($om) {
            $entity = $event->getEntity();
            $form = $event->getForm();
            $om->updateACL($entity, $form->getData());

            return $entity;
        };

        return $this->handleFormRequest($request, $entity, $form, $events, $handler);
    }

    /**
     * @param  Request $request
     * @param  mixed   $entity
     * @return string
     */
    protected function getRedirect(Request $request, $entity)
    {
        $route = $request->get('redirect');
        $parameters = array();
        if (null === $route) {
            switch($request->get('action')) {
                case 'permissions':
                    $route = $this->getPrefix($request) . 'show';
                    $parameters = array('id' => $entity->getId());
                    break;
            }
        }

        return array('route' => $route, 'parameters' => $parameters);
    }
}
