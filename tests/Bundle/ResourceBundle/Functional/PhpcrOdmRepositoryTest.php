<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2017 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Symfony\Cmf\Bundle\ResourceBundle\Functional;

class PhpcrOdmRepositoryTest extends PhpcrRepositoryTestCase
{
    public static function getKernelClass(): string
    {
        return \Tests\Symfony\Cmf\Bundle\ResourceBundle\Fixtures\App\Kernel::class;
    }

    protected function getRepository()
    {
        return $this->repositoryRegistry->get('test_repository_phpcr_odm');
    }
}
