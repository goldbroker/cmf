<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2017 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Symfony\Cmf\Bundle\SeoBundle\Functional\Doctrine\Orm;

use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Symfony\Cmf\Bundle\SeoBundle\Model\SeoMetadata;
use Tests\Symfony\Cmf\Bundle\SeoBundle\Fixtures\App\Entity\SeoAwareOrmContent;
use Symfony\Cmf\Component\Testing\Functional\BaseTestCase;

class SeoMetadataTest extends BaseTestCase
{
    public function setUp(): void
    {
        (new ORMPurger($this->getDbManager('ORM')->getOm()))->purge();
    }

    protected static function getKernelConfiguration(): array
    {
        return [
            'environment' => 'orm',
        ];
    }

    protected function getEm()
    {
        return $this->db('ORM')->getOm();
    }

    public function testSeoMetadata()
    {
        $content = new SeoAwareOrmContent();
        $content
            ->setTitle('Seo Aware test')
            ->setBody('Content for SeoAware Test')
        ;

        $seoMetadata = new SeoMetadata();
        $seoMetadata
            ->setTitle('Seo Title')
            ->setMetaDescription('Seo Description')
            ->setMetaKeywords('Seo, Keys')
            ->setOriginalUrl('/test')
            ->setExtraProperties(['og:title' => 'Extra title'])
            ->setExtraNames(['robots' => 'index, follow'])
            ->setExtraHttp(['content-type' => 'text/html'])
        ;

        $content->setSeoMetadata($seoMetadata);

        $this->getEm()->persist($content);
        $this->getEm()->flush();
        $this->getEm()->clear();

        $content = $this->getEm()
                        ->getRepository('Tests\Symfony\Cmf\Bundle\SeoBundle\Fixtures\App\Entity\SeoAwareOrmContent')
                        ->findOneByTitle('Seo Aware test');

        $this->assertNotNull($content);

        $persistedSeoMetadata = $content->getSeoMetadata();
        $this->assertEquals($seoMetadata, $persistedSeoMetadata);
    }
}
