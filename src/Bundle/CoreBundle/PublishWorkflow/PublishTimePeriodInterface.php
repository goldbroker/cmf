<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\CoreBundle\PublishWorkflow;

/**
 * Interface to expose editable time period publishing information.
 */
interface PublishTimePeriodInterface extends PublishTimePeriodReadInterface
{
    /**
     * Set the date from which the content should
     * be considered publishable.
     *
     * Setting a NULL value asserts that the content
     * has always been publishable.
     */
    public function setPublishStartDate(\DateTime $publishStartDate = null);

    /**
     * Set the date at which the content should
     * stop being published.
     *
     * Setting a NULL value asserts that the
     * content will always be publishable.
     */
    public function setPublishEndDate(\DateTime $publishEndDate = null);
}
