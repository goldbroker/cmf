<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2017 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Symfony\Cmf\Component\Resource\Unit\Repository;

use Symfony\Cmf\Component\Resource\Description\Description;
use Symfony\Cmf\Component\Resource\Description\Descriptor;
use Symfony\Cmf\Component\Resource\Puli\Api\PuliResource;

class DescriptionTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Description
     */
    private $description;

    /**
     * @var PuliResource
     */
    private $resource;

    public function setUp(): void
    {
        $this->resource = $this->prophesize(PuliResource::class);
        $this->description = new Description($this->resource->reveal());
    }

    /**
     * It should allow values to be set and retrieved.
     */
    public function testGetSet()
    {
        $this->description->set(Descriptor::TYPE_ALIAS, 'page');
        $this->description->set(Descriptor::LINK_EDIT_HTML, '/path/to/edit');
        $this->description->set('custom.key', 'Hello');

        $this->assertEquals('page', $this->description->get(Descriptor::TYPE_ALIAS));

        $this->assertTrue($this->description->has(Descriptor::TYPE_ALIAS));
        $this->assertFalse($this->description->has('hello'));
        $this->assertEquals([
            Descriptor::TYPE_ALIAS => 'page',
            Descriptor::LINK_EDIT_HTML => '/path/to/edit',
            'custom.key' => 'Hello',
        ], $this->description->all());
    }

    /**
     * It should throw an exception if a non-scalar value is set.
     */
    public function testSetNonScalar()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Only scalar and array values are allowed as descriptor values, got "object" when setting descriptor "hello"');

        $this->description->set('hello', new \stdClass());
    }

    /**
     * It should throw an exception when requesting an unsupported descriptor.
     */
    public function testGetUnsupported()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Supported descriptors: "foo", "bar"');

        $this->description->set('foo', 'bar');
        $this->description->set('bar', 'foo');
        $this->description->get('not there');
    }

    /**
     * It should return the resource that it describes.
     */
    public function testGetResource()
    {
        $resource = $this->description->getResource();
        $this->assertSame($this->resource->reveal(), $resource);
    }
}
