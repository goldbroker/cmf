<?php

namespace Tests\Symfony\Cmf\Bundle\MultiDomainBundle\Unit\EventListener;

use Doctrine\ODM\PHPCR\Event\MoveEventArgs;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Cmf\Bundle\MultiDomainBundle\EventListener\RouteListener;
use Symfony\Cmf\Bundle\RoutingBundle\Doctrine\Phpcr\Route;

class RouteListenerTest extends \PHPUnit\Framework\TestCase
{
    private $routeBasePaths = array('/cms/routes', '/cms/routes2');
    private $domains = array('en' => 'www.example.org', 'fr' => 'fr.example.org');
    private $route;

    public function setUp(): void
    {
        $this->route = new Route();
        $this->route->setId('/cms/routes/fr.example.org/home');
    }

    public function testUpdateHost()
    {
        $locale = 'fr';
        $host = 'fr.example.org';
        $dm = $this->getMockBuilder('Doctrine\ODM\PHPCR\DocumentManager')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $routeHostListener = new RouteListener($this->routeBasePaths, $this->domains);

        $route = $this->route;

        $event = new LifecycleEventArgs($route, $dm);
        $routeHostListener->postLoad($event);
        $this->assertEquals($host, $route->getHost());
        $this->assertEquals($locale, $route->getRequirement('_locale'));
        $this->assertEquals($locale, $route->getDefault('_locale'));

        $event = new LifecycleEventArgs($route, $dm);
        $routeHostListener->postPersist($event);
        $this->assertEquals($host, $route->getHost());
        $this->assertEquals($locale, $route->getRequirement('_locale'));
        $this->assertEquals($locale, $route->getDefault('_locale'));

        $event = new MoveEventArgs($route, $dm, null, null);
        $routeHostListener->postMove($event);
        $this->assertEquals($host, $route->getHost());
        $this->assertEquals($locale, $route->getRequirement('_locale'));
        $this->assertEquals($locale, $route->getDefault('_locale'));
    }
}
