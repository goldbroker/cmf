<?php

namespace Symfony\Cmf\Bundle\BlockBundle\Block;

use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\BlockBundle\Block\BlockContextManagerInterface;
use Sonata\BlockBundle\Block\BlockRendererInterface;
use Sonata\BlockBundle\Block\Service\AbstractBlockService;
use Sonata\BlockBundle\Block\Service\BlockServiceInterface;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class ReferenceBlockService extends AbstractBlockService implements BlockServiceInterface
{
    protected BlockRendererInterface $blockRenderer;

    protected BlockContextManagerInterface $blockContextManager;

    public function __construct(Environment $twig, BlockRendererInterface $blockRenderer, BlockContextManagerInterface $blockContextManager)
    {
        parent::__construct($twig);
        $this->blockRenderer = $blockRenderer;
        $this->blockContextManager = $blockContextManager;
    }

    public function execute(BlockContextInterface $blockContext, Response $response = null): ?Response
    {
        if (!$response) {
            $response = new Response();
        }

        // if the reference target block does not exist, we just skip the rendering
        if ($blockContext->getBlock()->getEnabled() && null !== $blockContext->getBlock()->getReferencedBlock()) {
            $referencedBlockContext = $this->blockContextManager->get($blockContext->getBlock()->getReferencedBlock());

            $response = $this->blockRenderer->render($referencedBlockContext);
        }

        return $response;
    }
}
