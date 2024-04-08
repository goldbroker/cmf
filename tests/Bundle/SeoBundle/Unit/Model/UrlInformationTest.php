<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2017 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Symfony\Cmf\Bundle\SeoBundle\Unit\Model;

use Symfony\Cmf\Bundle\SeoBundle\Exception\InvalidArgumentException;
use Symfony\Cmf\Bundle\SeoBundle\Model\UrlInformation;

/**
 * @author Maximilian Berghoff <Maximilian.Berghoff@gmx.de>
 */
class UrlInformationTest extends \PHPUnit\Framework\Testcase
{
    /**
     * @var UrlInformation
     */
    private $model;

    public function setUp(): void
    {
        $this->model = new UrlInformation();
    }

    public function testSetChangeFrequencyShouldThrowExceptionForInvalidArguments()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid change frequency "some one", use one of always, hourly, daily, weekly, monthly, yearly, never.');
        $this->model->setChangeFrequency('some one');
    }

    public function testValidChangeFrequency()
    {
        $this->model->setChangeFrequency('never');

        $this->assertEquals('never', $this->model->getChangeFrequency());
    }
}
