<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2017 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Symfony\Cmf\Bundle\SeoBundle\Unit\Controller;

use Symfony\Cmf\Bundle\SeoBundle\Controller\SitemapController;
use Symfony\Cmf\Bundle\SeoBundle\Model\AlternateLocale;
use Symfony\Cmf\Bundle\SeoBundle\Model\UrlInformation;
use Symfony\Cmf\Bundle\SeoBundle\Sitemap\UrlInformationProvider;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Templating\EngineInterface;
use Twig\Environment;

/**
 * @author Maximilian Berghoff <Maximilian.Berghoff@gmx.de>
 */
class SitemapControllerTest extends \PHPUnit\Framework\TestCase
{
    protected Environment $twig;

    private SitemapController $controller;

    public function setUp(): void
    {
        $provider = $this->createMock(UrlInformationProvider::class);
        $provider
            ->expects($this->any())
            ->method('getUrlInformation')
            ->will($this->returnValue($this->createUrlInformation()));

        $this->twig = $this->createMock(Environment::class);
        $this->controller = new SitemapController(
            $provider,
            $this->twig,
            [
                'test' => [
                    'templates' => [
                        'xml' => 'CmfSeoBundle:Sitemap:index.xml.twig',
                        'html' => 'CmfSeoBundle:Sitemap:index.html.twig',
                    ],
                ],
            ]
        );
    }

    public function testRequestJson()
    {
        /** @var Response $response */
        $response = $this->controller->indexAction('json', 'test');
        $expected = [
            [
                'loc' => 'http://www.test-alternate-locale.de',
                'label' => 'Test alternate locale',
                'changefreq' => 'never',
                'lastmod' => '2014-11-07T00:00:00+01:00',
                'priority' => 0.85,
                'alternate_locales' => [
                    ['href' => 'http://www.test-alternate-locale.com', 'href_locale' => 'en'],
                ],
            ],
            [
                'loc' => 'http://www.test-domain.de',
                'label' => 'Test label',
                'changefreq' => 'always',
                'lastmod' => '2014-11-06T00:00:00+01:00',
                'priority' => 0.85,
                'alternate_locales' => [],
            ],
        ];

        $this->assertEquals($expected, json_decode($response->getContent(), true));
    }

    public function testRequestXml()
    {
        $this->twig->expects($this->once())
            ->method('render')
            ->with($this->equalTo('CmfSeoBundle:Sitemap:index.xml.twig'), $this->anything())
            ->will($this->returnValue('some-xml-string'));

        /** @var Response $response */
        $response = $this->controller->indexAction('xml', 'test');

        $this->assertEquals('some-xml-string', $response->getContent());
    }

    public function testRequestHtml()
    {
        $expectedResponse = new Response('some-html-string');
        $this->twig->expects($this->once())->method('render')->will($this->returnValue($expectedResponse));

        /** @var Response $response */
        $response = $this->controller->indexAction('html', 'test');

        $this->assertEquals($expectedResponse, $response->getContent());
    }

    private function createUrlInformation()
    {
        $resultList = [];

        $urlInformation = new UrlInformation();
        $urlInformation
            ->setLocation('http://www.test-alternate-locale.de')
            ->setChangeFrequency('never')
            ->setLabel('Test alternate locale')
            ->setPriority(0.85)
            ->setLastModification(new \DateTime('2014-11-07', new \DateTimeZone('Europe/Berlin')))
        ;
        $alternateLocale = new AlternateLocale('http://www.test-alternate-locale.com', 'en');
        $urlInformation->addAlternateLocale($alternateLocale);
        $resultList[] = $urlInformation;

        $urlInformation = new UrlInformation();
        $urlInformation
            ->setLocation('http://www.test-domain.de')
            ->setChangeFrequency('always')
            ->setLabel('Test label')
            ->setPriority(0.85)
            ->setLastModification(new \DateTime('2014-11-06', new \DateTimeZone('Europe/Berlin')))
        ;
        $resultList[] = $urlInformation;

        return $resultList;
    }
}
