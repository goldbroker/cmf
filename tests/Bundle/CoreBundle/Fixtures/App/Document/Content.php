<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Symfony\Cmf\Bundle\CoreBundle\Fixtures\App\Document;

use Doctrine\ODM\PHPCR\Mapping\Annotations as PHPCRODM;
use Symfony\Cmf\Component\Routing\RouteReferrersReadInterface;

/**
 * @PHPCRODM\Document(referenceable=true)
 */
class Content implements RouteReferrersReadInterface
{
    /** @PHPCRODM\Id */
    public $id;

    /** @PHPCRODM\Referrers(referringDocument="Symfony\Cmf\Bundle\RoutingBundle\Doctrine\Phpcr\Route", referencedBy="content") */
    public $routes;

    public function getId()
    {
        return $this->id;
    }

    public function getRoutes()
    {
        return $this->routes;
    }
}
