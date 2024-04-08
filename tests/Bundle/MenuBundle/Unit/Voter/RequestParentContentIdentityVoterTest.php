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

use Symfony\Cmf\Bundle\MenuBundle\Voter\RequestParentContentIdentityVoter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class RequestParentContentIdentityVoterTest extends \PHPUnit\Framework\TestCase
{
    private $voter;

    private $request;

    protected function setUp(): void
    {
        $this->request = $this->prophesize(Request::class);

        $requestStack = $this->createMock(RequestStack::class);
        $requestStack->method('getMasterRequest')->willReturn($this->request->reveal());

        $this->voter = new RequestParentContentIdentityVoter(
            '_content',
            __CLASS__.'_ChildContent',
            $requestStack
        );
    }

    public function testSkipsWhenNoContentIsAvailable()
    {
        $this->assertNull($this->voter->matchItem($this->createItem()));
    }

    public function testSkipsWhenNoRequestIsAvailable()
    {
        $voter = new RequestParentContentIdentityVoter(
            '_content',
            __CLASS__.'_ChildContent'
        );

        $this->assertNull($voter->matchItem($this->createItem()));
    }

    public function testSkipsWhenNoContentAttributeWasDefined()
    {
        $attributes = $this->prophesize('Symfony\Component\HttpFoundation\ParameterBag');
        $attributes->has('_content')->willReturn(false);
        $this->request->attributes = $attributes;

        $this->assertNull($this->voter->matchItem($this->createItem(new \stdClass())));
    }

    public function testSkipsWhenContentObjectDoesNotImplementChildClass()
    {
        $attributes = $this->prophesize('Symfony\Component\HttpFoundation\ParameterBag');
        $attributes->has('_content')->willReturn(false);
        $this->request->attributes = $attributes;

        $this->assertNull($this->voter->matchItem($this->createItem(new \stdClass())));
    }

    public function testMatchesWhenParentContentIsEqualToCurrentContent()
    {
        $parent = new \stdClass();
        $content = new RequestParentContentIdentityVoterTest_ChildContent($parent);

        $attributes = $this->prophesize('Symfony\Component\HttpFoundation\ParameterBag');
        $attributes->has('_content')->willReturn(true);
        $attributes->get('_content')->willReturn($content);
        $this->request->attributes = $attributes;

        $this->assertTrue($this->voter->matchItem($this->createItem($parent)));
    }

    public function testSkipsWhenParentContentIsNotEqual()
    {
        $content = new RequestParentContentIdentityVoterTest_ChildContent(new \stdClass());

        $attributes = $this->prophesize('Symfony\Component\HttpFoundation\ParameterBag');
        $attributes->has('_content')->willReturn(true);
        $attributes->get('_content')->willReturn($content);
        $this->request->attributes = $attributes;

        $this->assertNull($this->voter->matchItem($this->createItem(new \stdClass())));
    }

    private function createItem($content = null)
    {
        $item = $this->prophesize('Knp\Menu\ItemInterface');
        $item->getExtra('content')->willReturn($content);

        return $item->reveal();
    }
}

class RequestParentContentIdentityVoterTest_ChildContent
{
    private $parent;

    public function __construct($parent)
    {
        $this->parent = $parent;
    }

    public function getParentDocument()
    {
        return $this->parent;
    }
}
