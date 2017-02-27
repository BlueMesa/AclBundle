<?php

/*
 * This file is part of the ACL Bundle.
 *
 * Copyright (c) 2017 BlueMesa LabDB Contributors <labdb@bluemesa.eu>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bluemesa\Bundle\AclBundle\EventListener;

use Bluemesa\Bundle\AclBundle\Controller\Annotations\Action;
use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\Util\ClassUtils;
use FOS\RestBundle\Controller\Annotations\NamePrefix;
use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\RouterInterface;

/**
 * The AclAnnotationListener handles ACL annotations for controllers.
 *
 * @DI\Service("bluemesa.acl.listener.annotation")
 * @DI\Tag("kernel.event_listener",
 *     attributes = {
 *         "event" = "kernel.controller",
 *         "method" = "onKernelController",
 *         "priority" = 5
 *     }
 * )
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class AclAnnotationListener
{
    /**
     * @var Reader
     */
    protected $reader;

    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * Constructor.
     *
     * @DI\InjectParams({
     *     "reader" = @DI\Inject("annotation_reader"),
     *     "router" = @DI\Inject("router")
     * })
     *
     * @param Reader                $reader   A Reader instance
     * @param RouterInterface       $router   A RouterInterface instance
     */
    public function __construct(Reader $reader, RouterInterface $router)
    {
        $this->reader = $reader;
        $this->router = $router;
    }

    /**
     * Adds ACL parameters to the Request object.
     *
     * @param FilterControllerEvent $event A FilterControllerEvent instance
     */
    public function onKernelController(FilterControllerEvent $event)
    {
        $controller = $event->getController();
        $request = $event->getRequest();

        if (is_array($controller)) {
            $c = new \ReflectionClass(ClassUtils::getClass($controller[0]));
            $m = new \ReflectionMethod($controller[0], $controller[1]);
        } elseif (is_object($controller) && is_callable($controller, '__invoke')) {
            /** @var object $controller */
            $c = new \ReflectionClass(ClassUtils::getClass($controller));
            $m = new \ReflectionMethod($controller, '__invoke');
        } else {
            return;
        }

        /** @var Action $actionAnnotation */
        $actionAnnotation = $this->reader->getMethodAnnotation($m, Action::class);
        if (! $actionAnnotation) {
            return;
        }

        $action = $this->getActionName($actionAnnotation, $m);
        $editRedirect = $this->getRedirectRoute($actionAnnotation, $request, $c);

        $this->addRequestAttribute($request, 'acl_action', $action);
        $this->addRequestAttribute($request, 'permissions_redirect_route', $editRedirect);
    }

    /**
     * @param Action $actionAnnotation
     * @param \ReflectionMethod $m
     *
     * @return string
     * @throws \LogicException
     */
    private function getActionName(Action $actionAnnotation, \ReflectionMethod $m)
    {
        $action = $actionAnnotation->getAction();
        if (null === $action) {
            $method = $m->getName();
            $action = str_replace("Action", "", $method);
        }
        if (! in_array($action, array('permissions'))) {
            $message  = "The action '" . $action;
            $message .= "' is not one of the allowed ACL actions ('permissions').";
            throw new \LogicException($message);
        }

        return $action;
    }

    /**
     * @param Action            $actionAnnotation
     * @param Request           $request
     * @param \ReflectionClass  $c
     *
     * @return string
     */
    private function getRedirectRoute(Action $actionAnnotation, Request $request, \ReflectionClass $c)
    {
        $route = $actionAnnotation->getRedirectRoute();
        if (null === $route) {
            $route = $request->get('edit_redirect_route');
            if (null !== $route) {

                return $route;
            }
            $route = $this->getRoutePrefix($c) . "show";
        }

        $this->verifyRouteExists($route);

        return $route;
    }

    /**
     * @param \ReflectionClass $c
     *
     * @return string
     */
    private function getRoutePrefix(\ReflectionClass $c)
    {
        /** @var NamePrefix $namePrefixAnnotation */
        $namePrefixAnnotation = $this->reader->getClassAnnotation($c, NamePrefix::class);
        return $namePrefixAnnotation->value;
    }

    /**
     * @param  string                  $route
     * @throws RouteNotFoundException
     */
    private function verifyRouteExists($route)
    {
        try {
            $this->router->generate($route);
        } catch (\Exception $e) {
            if ($e instanceof RouteNotFoundException) {
                throw $e;
            }
        }
    }

    /**
     * @param Request $request
     * @param string  $attribute
     * @param string  $value
     */
    private function addRequestAttribute(Request $request, $attribute, $value)
    {
        if (! $request->attributes->has($attribute)) {
            $request->attributes->set($attribute, $value);
        }
    }
}
