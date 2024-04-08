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

use Symfony\Cmf\Bundle\SeoBundle\Extractor\DescriptionExtractor;
use Symfony\Cmf\Bundle\SeoBundle\Extractor\DescriptionReadInterface;
use Symfony\Cmf\Bundle\SeoBundle\SeoAwareInterface;

class DescriptionExtractorBaseTest extends AbstractBaseTest
{
    public function setUp(): void
    {
        parent::setUp();

        $this->extractor = new DescriptionExtractor();
        $this->extractMethod = 'getSeoDescription';
        $this->metadataMethod = 'setMetaDescription';
    }

    public function getSupportsData()
    {
        return [
            [$this->createMock(DescriptionReadInterface::class)],
            [$this->createMock(SeoAwareInterface::class), false],
        ];
    }
}
