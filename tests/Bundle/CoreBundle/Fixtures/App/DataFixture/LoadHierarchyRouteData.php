<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Symfony\Cmf\Bundle\CoreBundle\Fixtures\App\DataFixture;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Tests\Symfony\Cmf\Bundle\CoreBundle\Fixtures\App\Document\RouteAware;

/**
 * Fixtures class for test data.
 */
class LoadHierarchyRouteData implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $session = $manager->getPhpcrSession();
        $root = $session->getRootNode();

        /*
         * /a
         * /a/b
         * /a/b/c
         * /a/b/d
         * /a/b/e
         * /a/f
         * /a/f/g
         * /a/f/g/h
         * /a/i
         */
        $a = $root->addNode('a');
        $b = $a->addNode('b');
        $c = $b->addNode('c');
        $c->addMixin('phpcr:managed');
        $c->setProperty('phpcr:class', RouteAware::class);
        $b->addNode('d');
        $e = $b->addNode('e');
        $e->addMixin('phpcr:managed');
        $e->setProperty('phpcr:class', RouteAware::class);
        $f = $a->addNode('f');
        $g = $f->addNode('g');
        $g->addNode('h');
        $a->addNode('i');

        $session->save();
    }
}
