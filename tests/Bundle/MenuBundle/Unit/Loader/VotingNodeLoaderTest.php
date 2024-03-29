<?php

namespace Tests\Symfony\Cmf\Bundle\MenuBundle\Unit\Loader;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Cmf\Bundle\MenuBundle\Loader\VotingNodeLoader;
use Knp\Menu\ItemInterface;
use Knp\Menu\FactoryInterface;
use Knp\Menu\NodeInterface;

class VotingNodeLoaderTest extends \PHPUnit\Framework\TestCase
{
    private $subject;

    private $factory;

    private $dispatcher;

    public function setUp(): void
    {
        $this->factory = $this->createMock(FactoryInterface::class);
        $this->dispatcher = $this->createMock(EventDispatcherInterface::class);
        $this->subject = new VotingNodeLoader($this->factory, $this->dispatcher);
    }

    /**
     * @dataProvider getCreateFromNodeData
     */
    public function testCreateFromNode($options)
    {
        // promises
        $node2 = $this->getNode('node2');
        $node3 = $this->getNode('node3');
        $node1 = $this->getNode('node1', [], [$node2, $node3]);

        // predictions
        $options = array_merge([
            'node2_is_published' => true,
        ], $options);

        $dispatchMethodMock = $this->dispatcher->expects($this->exactly(3))->method('dispatch');

        $nodes = 3;
        if (!$options['node2_is_published']) {
            $dispatchMethodMock->will($this->returnCallback(function ($name, $event) use ($node2) {
                if ($event->getNode() === $node2) {
                    $event->setSkipNode(true);
                }
            }));
            $nodes = 2;
        }

        $that = $this;
        $this->factory->expects($this->exactly($nodes))->method('createItem')->will($this->returnCallback(function () use ($that) {
            return $that->createMock(ItemInterface::class);
        }));

        // test
        $res = $this->subject->load($node1);
        $this->assertInstanceOf(ItemInterface::class, $res);
    }

    public function getCreateFromNodeData(): array
    {
        return [
            [[
            ]],
            [[
                'node2_is_published' => false,
            ]],
        ];
    }

    protected function getNode($name, $options = [], $children = [])
    {
        $node = $this->createMock(NodeInterface::class);

        $node->expects($this->any())->method('getName')->willReturn($name);
        $node->expects($this->any())->method('getOptions')->willReturn($options);
        $node->expects($this->any())->method('getChildren')->willReturn($children);

        return $node;
    }
}
