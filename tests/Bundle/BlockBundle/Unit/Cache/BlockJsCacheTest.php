<?php

namespace Tests\Symfony\Cmf\Bundle\BlockBundle\Unit\Cache;

use Sonata\BlockBundle\Block\BlockContextManagerInterface;
use Sonata\BlockBundle\Block\BlockLoaderInterface;
use Sonata\BlockBundle\Block\BlockRendererInterface;
use Sonata\BlockBundle\Model\EmptyBlock;
use Sonata\Cache\CacheElement;
use Symfony\Cmf\Bundle\BlockBundle\Cache\BlockJsCache;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

class BlockJsCacheTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider getExceptionCacheKeys
     */
    public function testExceptions($keys)
    {
        $cache = new BlockJsCache(
            $this->createMock(RouterInterface::class),
            $this->createMock(BlockRendererInterface::class),
            $this->createMock(BlockLoaderInterface::class),
            $this->createMock(BlockContextManagerInterface::class),
            false
        );

        $this->expectException(\RuntimeException::class);
        $cache->get($keys, 'data');
    }

    public static function getExceptionCacheKeys(): array
    {
        return [
            [[]],
            [['block_id' => '/cms/content/home/additionalInfoBlock']],
            [['updated_at' => 'foo']],
        ];
    }

    public function testInitCache()
    {
        $router = $this->createMock(RouterInterface::class);
        $router->expects($this->once())->method('generate')->will($this->returnValue('http://cmf.symfony.com/symfony-cmf/block/cache/js-async.js'));

        $cache = new BlockJsCache(
            $router,
            $this->createMock(BlockRendererInterface::class),
            $this->createMock(BlockLoaderInterface::class),
            $this->createMock(BlockContextManagerInterface::class),
            false
        );

        $this->assertTrue($cache->flush([]));
        $this->assertTrue($cache->flushAll());

        $keys = [
            'block_id' => '/cms/content/home/additionalInfoBlock',
            'updated_at' => 'as',
        ];

        $cacheElement = $cache->set($keys, 'data');

        $this->assertInstanceOf(CacheElement::class, $cacheElement);

        $this->assertTrue($cache->has(['id' => 7]));

        $cacheElement = $cache->get($keys);

        $this->assertInstanceOf(CacheElement::class, $cacheElement);

        $expected = <<<'EXPECTED'
<div id="block-cms-content-home-additionalInfoBlock" >
    <script type="text/javascript">
        /*<![CDATA[*/

            (function () {
                var b = document.createElement('script');
                b.type = 'text/javascript';
                b.async = true;
                b.src = 'http://cmf.symfony.com/symfony-cmf/block/cache/js-async.js'
                var s = document.getElementsByTagName('script')[0];
                s.parentNode.insertBefore(b, s);
            })();

        /*]]>*/
    </script>
</div>
EXPECTED;

        $this->assertEquals($expected, $cacheElement->getData()->getContent());
    }

    public function testCacheAction()
    {
        $blockLoader = $this->createMock(BlockLoaderInterface::class);
        $blockLoader->method('load')->willReturn(new EmptyBlock());

        $cache = new BlockJsCache(
            $this->createMock(RouterInterface::class),
            $this->createMock(BlockRendererInterface::class),
            $blockLoader,
            $this->createMock(BlockContextManagerInterface::class),
            false
        );

        $request = $this->createMock(Request::class);

        // block not found
        $block = $cache->cacheAction($request);
        $this->assertEquals(new Response('', Response::HTTP_NOT_FOUND), $block);
    }
}
