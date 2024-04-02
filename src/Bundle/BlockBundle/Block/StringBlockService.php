<?php

namespace Symfony\Cmf\Bundle\BlockBundle\Block;

use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\BlockBundle\Block\Service\AbstractBlockService;
use Sonata\BlockBundle\Block\Service\BlockServiceInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Twig\Environment;

class StringBlockService extends AbstractBlockService implements BlockServiceInterface
{
    protected string $template = '@CmfBlock/Block/block_string.html.twig';

    public function __construct(Environment $twig, ?string $template = null)
    {
        parent::__construct($twig);

        if ($template) {
            $this->template = $template;
        }
    }

    public function execute(BlockContextInterface $blockContext, Response $response = null): Response
    {
        if (!$response) {
            $response = new Response();
        }

        if ($blockContext->getBlock()->getEnabled()) {
            $response = $this->renderResponse($blockContext->getTemplate(), ['block' => $blockContext->getBlock()], $response);
        }

        return $response;
    }

    public function configureSettings(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'template' => $this->template,
        ]);
    }

    /**
     * @param string $template
     */
    public function setTemplate(string $template): void
    {
        $this->template = $template;
    }
}
