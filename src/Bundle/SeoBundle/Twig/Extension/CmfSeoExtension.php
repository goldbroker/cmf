<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2017 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\SeoBundle\Twig\Extension;

use Symfony\Cmf\Bundle\SeoBundle\SeoPresentationInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class CmfSeoExtension extends AbstractExtension
{
    /**
     * @var SeoPresentationInterface
     */
    protected $seoPresentation;

    /**
     * @param SeoPresentationInterface $seoPresentation
     */
    public function __construct(SeoPresentationInterface $seoPresentation)
    {
        $this->seoPresentation = $seoPresentation;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('cmf_seo_update_metadata', [$this, 'updateMetadata']),
        ];
    }

    /**
     * @see SeoPresentationInterface
     */
    public function updateMetadata($content): void
    {
        $this->seoPresentation->updateSeoPage($content);
    }

    public function getName(): string
    {
        return 'cmf_seo';
    }
}
