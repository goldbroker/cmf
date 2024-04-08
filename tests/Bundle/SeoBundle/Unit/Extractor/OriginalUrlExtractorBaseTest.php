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

use Symfony\Cmf\Bundle\SeoBundle\Extractor\OriginalUrlExtractor;
use Symfony\Cmf\Bundle\SeoBundle\Extractor\OriginalUrlReadInterface;
use Symfony\Cmf\Bundle\SeoBundle\SeoAwareInterface;

class OriginalUrlExtractorBaseTest extends AbstractBaseTest
{
    public function setUp(): void
    {
        parent::setUp();

        $this->extractor = new OriginalUrlExtractor();
        $this->extractMethod = 'getSeoOriginalUrl';
        $this->metadataMethod = 'setOriginalUrl';
    }

    public function getSupportsData()
    {
        return [
            [$this->createMock(OriginalUrlReadInterface::class)],
            [$this->createMock(SeoAwareInterface::class), false],
        ];
    }
}
