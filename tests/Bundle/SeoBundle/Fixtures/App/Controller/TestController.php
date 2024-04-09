<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2017 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Symfony\Cmf\Bundle\SeoBundle\Fixtures\App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TestController extends AbstractController
{
    public function indexAction($contentDocument)
    {
        $params = [
            'cmfMainContent' => [
                'title' => $contentDocument->getTitle(),
                'body' => $contentDocument->getBody(),
            ],
        ];

        return $this->render('index.html.twig', $params);
    }
}
