<?php

namespace Tests\Symfony\Cmf\Bundle\MultiDomainBundle\Unit\EventListener;

use Symfony\Cmf\Bundle\MultiDomainBundle\EventListener\LocaleListener;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class LocaleListenerTest extends \PHPUnit\Framework\TestCase
{
    private array $domains = array(
        'en' => 'en.example.org',
        'fr' => 'fr.example.org',
    );

    public function onKernelRequestDataProvider(): array
    {
        return array(
            array('www.example.org', 'en'),
            array('en.example.org', 'en'),
            array('fr.example.org', 'fr'),
        );
    }

    /**
     * @dataProvider onKernelRequestDataProvider
     */
    public function testOnKernelRequest($host, $locale)
    {
        $kernel = $this->createMock('Symfony\Component\HttpKernel\HttpKernelInterface');
        $request = new Request(array(), array(), array(), array(), array(), array('HTTP_HOST' => $host));
        $event = new RequestEvent($kernel, $request, HttpKernelInterface::MASTER_REQUEST);

        $localeListener = new LocaleListener($this->domains);
        $localeListener->onKernelRequest($event);

        $this->assertEquals($locale, $request->getLocale());
    }
}
