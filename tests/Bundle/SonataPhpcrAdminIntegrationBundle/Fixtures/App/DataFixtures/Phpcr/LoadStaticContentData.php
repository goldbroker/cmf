<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2017 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Symfony\Cmf\Bundle\SonataPhpcrAdminIntegrationBundle\Fixtures\App\DataFixtures\Phpcr;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Doctrine\ODM\PHPCR\Document\Generic;
use PHPCR\Util\NodeHelper;
use Symfony\Cmf\Bundle\ContentBundle\Doctrine\Phpcr\StaticContent;

class LoadStaticContentData implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        NodeHelper::createPath($manager->getPhpcrSession(), '/test');
        $root = $manager->find(null, '/test');

        $contentRoot = new Generic();
        $contentRoot->setNodename('contents');
        $contentRoot->setParentDocument($root);
        $manager->persist($contentRoot);

        $content = new StaticContent();
        $content->setName('content-1');
        $content->setTitle('Content 1');
        $content->setBody('Content 1');
        $content->setParentDocument($contentRoot);
        $manager->persist($content);

        $content = new StaticContent();
        $content->setName('content-2');
        $content->setTitle('Content 2');
        $content->setBody('Content 2');
        $content->setParentDocument($contentRoot);
        $manager->persist($content);

        $manager->flush();
    }
}
