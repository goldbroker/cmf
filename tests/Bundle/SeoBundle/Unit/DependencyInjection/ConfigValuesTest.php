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

use Symfony\Cmf\Bundle\SeoBundle\DependencyInjection\ConfigValues;
use Symfony\Cmf\Bundle\SeoBundle\Exception\ExtractorStrategyException;

class ConfigValuesTest extends \PHPUnit\Framework\Testcase
{
    public function testInvalidStrategy()
    {
        $this->expectException(ExtractorStrategyException::class);
        $configValues = new ConfigValues();
        $configValues->setOriginalUrlBehaviour('nonexistent');
    }
}
