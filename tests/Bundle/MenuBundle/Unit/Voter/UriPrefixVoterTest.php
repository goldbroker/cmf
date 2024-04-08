<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2017 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Symfony\Cmf\Bundle\MenuBundle\Unit\Voter;

use Knp\Menu\ItemInterface;
use Symfony\Cmf\Bundle\MenuBundle\Voter\UriPrefixVoter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Route;

class UriPrefixVoterTest extends \PHPUnit\Framework\TestCase
{
    private $voter;

    private $request;

    protected function setUp(): void
    {
        $this->request = $this->prophesize(Request::class);
        $this->request->getLocale()->willReturn('');

        $requestStack = $this->createMock(RequestStack::class);
        $requestStack->method('getMasterRequest')->willReturn($this->request->reveal());

        $this->voter = new UriPrefixVoter($requestStack);
    }

    public function testSkipsWhenNoContentIsAvailable()
    {
        $this->assertNull($this->voter->matchItem($this->createItem()));
    }

    public function testSkipsWhenNoRequestIsAvailable()
    {
        $voter = new UriPrefixVoter();

        $this->assertNull($voter->matchItem($this->createItem()));
    }

    public function testSkipsIfContentDoesNotExtendRoute()
    {
        $this->assertNull($this->voter->matchItem($this->createItem(new \stdClass())));
    }

    public function testSkipsIfContentHasNoCurrentUriPrefixOption()
    {
        $content = $this->prophesize(Route::class);
        $content->hasOption('currentUriPrefix')->willReturn(false);

        $this->assertNull($this->voter->matchItem($this->createItem($content->reveal())));
    }

    public function testMatchesCurrentUriPrefixOptionWithCurrentUri()
    {
        $content = $this->prophesize(Route::class);
        $content->hasOption('currentUriPrefix')->willReturn(true);
        $content->getOption('currentUriPrefix')->willReturn('/some/prefix');

        $this->request->getPathInfo()->willReturn('/some/prefix/page/12');

        $this->assertTrue($this->voter->matchItem($this->createItem($content->reveal())));
    }

    public function testSkipsWhenThereIsNoMatch()
    {
        $content = $this->prophesize(Route::class);
        $content->hasOption('currentUriPrefix')->willReturn(true);
        $content->getOption('currentUriPrefix')->willReturn('/some/prefix');

        $this->request->getPathInfo()->willReturn('/page/12');

        $this->assertNull($this->voter->matchItem($this->createItem($content->reveal())));
    }

    public function testReplacesSpecialLocalePlaceholderInCurrentUriPrefix()
    {
        $content = $this->prophesize(Route::class);
        $content->hasOption('currentUriPrefix')->willReturn(true);
        $content->getOption('currentUriPrefix')->willReturn('/{_locale}/prefix');

        $this->request->getPathInfo()->willReturn('/en/prefix/page/12');
        $this->request->getLocale()->willReturn('en');

        $this->assertTrue($this->voter->matchItem($this->createItem($content->reveal())));
    }

    private function createItem($content = null)
    {
        $item = $this->prophesize(ItemInterface::class);
        $item->getExtra('content')->willReturn($content);

        return $item->reveal();
    }
}
