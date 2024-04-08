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

use Symfony\Cmf\Bundle\SeoBundle\Extractor\KeywordsExtractor;
use Symfony\Cmf\Bundle\SeoBundle\Extractor\KeywordsReadInterface;
use Symfony\Cmf\Bundle\SeoBundle\SeoAwareInterface;

class KeywordsExtractorBaseTest extends AbstractBaseTest
{
    public function setUp(): void
    {
        parent::setUp();

        $this->extractor = new KeywordsExtractor();
        $this->extractMethod = 'getSeoKeywords';
        $this->metadataMethod = 'setMetaKeywords';
    }

    public function getSupportsData()
    {
        return [
            [$this->createMock(KeywordsReadInterface::class)],
            [$this->createMock(SeoAwareInterface::class), false],
        ];
    }
}
