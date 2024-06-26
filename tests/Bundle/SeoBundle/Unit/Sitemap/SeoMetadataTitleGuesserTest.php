<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2017 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Symfony\Cmf\Bundle\SeoBundle\Unit\Sitemap;

use Symfony\Cmf\Bundle\SeoBundle\Model\SeoMetadata;
use Symfony\Cmf\Bundle\SeoBundle\SeoPresentation;
use Symfony\Cmf\Bundle\SeoBundle\Sitemap\SeoMetadataTitleGuesser;

class SeoMetadataTitleGuesserTest extends GuesserTestCase
{
    public function testGuessCreate()
    {
        $urlInformation = parent::testGuessCreate();
        $this->assertEquals('Symfony CMF', $urlInformation->getLabel());
    }

    /**
     * {@inheritdoc}
     */
    protected function createGuesser()
    {
        $seoMetadata = new SeoMetadata();
        $seoMetadata->setTitle('Symfony CMF');
        $seoPresentation = $this->createMock(SeoPresentation::class);
        $seoPresentation
            ->expects($this->any())
            ->method('getSeoMetadata')
            ->with($this)
            ->will($this->returnValue($seoMetadata))
        ;

        return new SeoMetadataTitleGuesser($seoPresentation);
    }

    /**
     * {@inheritdoc}
     */
    protected function createData()
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function getFields()
    {
        return ['Label'];
    }
}
