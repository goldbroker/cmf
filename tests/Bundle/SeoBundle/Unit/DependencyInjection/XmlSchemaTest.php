<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2017 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Symfony\Cmf\Bundle\SeoBundle\Unit\DependencyInjection;

use Symfony\Cmf\Component\Testing\Unit\XmlSchemaTestCase;

class XmlSchemaTest extends XmlSchemaTestCase
{
    public function testSchema()
    {
        $xmlFiles = array_map(function ($file) {
            return __DIR__.'/../../Fixtures/fixtures/config/'.$file;
        }, [
            'config.xml',
            'config1.xml',
            'config2.xml',
            'config3.xml',
            'config4.xml',
        ]);

        foreach ($xmlFiles as $xmlFile) {
            $this->assertSchemaAcceptsXml([$xmlFile], __DIR__.'/../../../../../src/Bundle/SeoBundle/Resources/config/schema/seo-1.0.xsd');
        }
    }
}
