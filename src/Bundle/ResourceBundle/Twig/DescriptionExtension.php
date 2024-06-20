<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2017 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\ResourceBundle\Twig;

use Symfony\Cmf\Component\Resource\Description\DescriptionFactory;
use Symfony\Cmf\Component\Resource\Puli\Api\PuliResource;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * @author Daniel Leech <daniel@dantleech.com>
 */
class DescriptionExtension extends AbstractExtension
{
    private $descriptionFactory;

    public function __construct(DescriptionFactory $descriptionFactory)
    {
        $this->descriptionFactory = $descriptionFactory;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('cmf_resource_description', [$this, 'getDescription']),
        ];
    }

    public function getDescription(PuliResource $resource)
    {
        return $this->descriptionFactory->getPayloadDescriptionFor($resource);
    }

    public function getName(): string
    {
        return 'cmf_resource_description';
    }
}
