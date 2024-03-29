<?php

namespace Tests\Symfony\Cmf\Component\Glob\Unit;

use Symfony\Cmf\Component\Glob\GlobHelper;

class GlobHelperTest extends \PHPUnit\Framework\TestCase
{
    public function setUp(): void
    {
        $this->helper = new GlobHelper();
    }

    public function provideIsGlobbed()
    {
        return array(
            array(
                '/hello', false
            ),
            array(
                '/hello/*', true
            ),
            array(
                '/hello/*/goodbye', true
            ),
        );
    }

    /**
     * @dataProvider provideIsGlobbed
     */
    public function testIsGlobbed($string, $expectedResult)
    {
        $result = $this->helper->isGlobbed($string);
        $this->assertEquals($expectedResult, $result);
    }
}
