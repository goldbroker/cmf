<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2017 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Symfony\Cmf\Bundle\SeoBundle\Unit\Extractor;

use Symfony\Cmf\Bundle\SeoBundle\Extractor\TitleReadExtractor;
use Symfony\Cmf\Bundle\SeoBundle\SeoAwareInterface;

class TitleReadExtractorBaseTest extends AbstractBaseTest
{
    public function setUp(): void
    {
        parent::setUp();

        $this->extractor = new TitleReadExtractor();
        $this->extractMethod = 'getTitle';
        $this->metadataMethod = 'setTitle';
    }

    public function getSupportsData()
    {
        return [
            [$this->getMockBuilder('Foo')->setMethods(['getTitle'])->getMock()],
            [$this->createMock(SeoAwareInterface::class), false],
        ];
    }
}
