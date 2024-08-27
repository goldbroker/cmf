<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2017 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Symfony\Cmf\Bundle\SonataPhpcrAdminIntegrationBundle\Fixtures\App\Document;

use Doctrine\ODM\PHPCR\Mapping\Annotations as PHPCRODM;
use Symfony\Cmf\Bundle\CoreBundle\PublishWorkflow\PublishableInterface;
use Symfony\Cmf\Bundle\CoreBundle\PublishWorkflow\PublishTimePeriodInterface;

/**
 * @PHPCRODM\Document(referenceable=true)
 */
class CoreExtensionsAwareContent extends ContentBase implements PublishableInterface, PublishTimePeriodInterface
{
    /**
     * {@inheritdoc}
     */
    public function setPublishStartDate(\DateTime $publishDate = null)
    {
        // TODO: Implement setPublishStartDate() method.
    }

    /**
     * {@inheritdoc}
     */
    public function setPublishEndDate(\DateTime $publishDate = null)
    {
        // TODO: Implement setPublishEndDate() method.
    }

    /**
     * {@inheritdoc}
     */
    public function getPublishStartDate(): ?\DateTime
    {
        // TODO: Implement getPublishStartDate() method.
    }

    /**
     * {@inheritdoc}
     */
    public function getPublishEndDate(): ?\DateTime
    {
        // TODO: Implement getPublishEndDate() method.
    }

    /**
     * {@inheritdoc}
     */
    public function setPublishable($publishable): void
    {
        // TODO: Implement setPublishable() method.
    }

    /**
     * {@inheritdoc}
     */
    public function isPublishable(): bool
    {
        // TODO: Implement isPublishable() method.
    }
}
