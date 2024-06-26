<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2017 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$config = [
    'firewalls' => [
        'main' => [
            'pattern' => '^/',
            'security' => false,
        ],
    ],
];

$container->loadFromExtension('security', $config);
