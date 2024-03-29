<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2017 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Symfony\Cmf\Bundle\MenuBundle\Unit;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Psr\Log\LoggerInterface;
use Symfony\Cmf\Bundle\MenuBundle\QuietFactory;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

class QuietFactoryTest extends \PHPUnit\Framework\TestCase
{
    private $innerFactory;

    private $logger;

    protected function setUp(): void
    {
        $this->innerFactory = $this->prophesize(FactoryInterface::class);
        $this->logger = $this->prophesize(LoggerInterface::class);
    }

    public function provideItemsWithNotExistingLinks(): array
    {
        return [
            [['route' => 'not_existent'], ['route' => 'not_existent']],
            [['content' => 'not_existent'], ['content' => 'not_existent']],
            [['linkType' => 'route', 'route' => 'not_existent'], ['linkType' => 'route']],
        ];
    }

    /** @dataProvider provideItemsWithNotExistingLinks */
    public function testAllowEmptyItemsReturnsItemWithoutURL(array $firstOptions, array $secondOptions)
    {
        $this->innerFactory->createItem('Home', $firstOptions)
            ->willThrow(RouteNotFoundException::class);

        $homeMenuItem = $this->createMock(ItemInterface::class);
        $this->innerFactory->createItem('Home', $secondOptions)->willReturn($homeMenuItem);

        $factory = new QuietFactory($this->innerFactory->reveal(), $this->logger->reveal(), true);

        $this->assertEquals($homeMenuItem, $factory->createItem('Home', $firstOptions));
    }

//    public function testDisallowEmptyItemsReturnsNull()
//    {
//        $this->innerFactory->createItem('Home', ['route' => 'not_existent'])
//            ->willThrow(RouteNotFoundException::class);
//
//        $factory = new QuietFactory($this->innerFactory->reveal(), $this->logger->reveal(), false);
//
//        $this->assertNull($factory->createItem('Home', ['route' => 'not_existent']));
//    }
}
