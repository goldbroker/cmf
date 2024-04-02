<?php

namespace Symfony\Cmf\Bundle\BlockBundle\Block;

use Symfony\Cmf\Bundle\BlockBundle\Doctrine\Phpcr\ActionBlock;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\BlockBundle\Block\Service\AbstractBlockService;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Controller\ControllerReference;
use Symfony\Component\HttpKernel\Fragment\FragmentHandler;
use Twig\Environment;

class ActionBlockService extends AbstractBlockService
{
    private RequestStack $requestStack;

    protected FragmentHandler $renderer;

    public function __construct(Environment $twig, FragmentHandler $renderer, RequestStack $requestStack)
    {
        parent::__construct($twig);
        $this->renderer = $renderer;
        $this->requestStack = $requestStack;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(BlockContextInterface $blockContext, Response $response = null): Response
    {
        /** @var $block ActionBlock */
        $block = $blockContext->getBlock();

        if (!$block->getActionName()) {
            throw new \RuntimeException(sprintf(
                'ActionBlock with id "%s" does not have an action name defined, implement a default or persist it in the document.',
                $block->getId()
            ));
        }

        if (!$block->getEnabled()) {
            return new Response();
        }

        $requestParams = $block->resolveRequestParams($this->requestStack->getCurrentRequest(), $blockContext);

        return new Response($this->renderer->render(new ControllerReference(
                $block->getActionName(),
                $requestParams
            )
        ));
    }
}
