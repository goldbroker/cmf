<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2017 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\BlockBundle\Block;

use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\BlockBundle\Block\BlockRendererInterface;
use Sonata\BlockBundle\Block\Service\AbstractBlockService;
use Sonata\BlockBundle\Block\Service\BlockServiceInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Twig\Environment;

class ContainerBlockService extends AbstractBlockService implements BlockServiceInterface
{
    protected BlockRendererInterface $blockRenderer;

    protected string $template = '@CmfBlock/Block/block_container.html.twig';

    public function __construct(Environment $twig, BlockRendererInterface $blockRenderer, ?string $template = null)
    {
        parent::__construct($twig);

        $this->blockRenderer = $blockRenderer;

        if ($template) {
            $this->template = $template;
        }
    }

    public function execute(BlockContextInterface $blockContext, Response $response = null): Response
    {
        if (!$response) {
            $response = new Response();
        }

        $block = $blockContext->getBlock();

        // merge block settings with default settings
        $settings = $blockContext->getSettings();
        $resolver = new OptionsResolver();
        $resolver->setDefaults($settings);
        $settings = $resolver->resolve($block->getSettings());

        if ($block->getEnabled()) {
            return $this->renderResponse($settings['template'], [
                'block' => $block,
                'settings' => $settings,
            ], $response);
        }

        return $response;
    }

    public function configureSettings(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'template' => $this->template,
            'divisible_by' => 0,
            'divisible_class' => '',
            'child_class' => '',
        ]);

        if (method_exists($resolver, 'setDefault')) {
            // Symfony >2.6
            $resolver->addAllowedTypes('divisible_by', 'integer');
            $resolver->addAllowedTypes('divisible_class', 'string');
            $resolver->addAllowedTypes('child_class', 'string');
        } else {
            $resolver->addAllowedTypes([
                'divisible_by' => ['integer'],
                'divisible_class' => ['string'],
                'child_class' => ['string'],
            ]);
        }
    }
}
