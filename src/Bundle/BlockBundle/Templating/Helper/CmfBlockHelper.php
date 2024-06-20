<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2017 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\BlockBundle\Templating\Helper;

use Psr\Log\LoggerInterface;
use Sonata\BlockBundle\Templating\Helper\BlockHelper as SonataBlockHelper;
use Symfony\Component\Templating\Helper\Helper;

/**
 * Helper functions for blocks.
 *
 * @author Wouter J <waldio.webdesign@gmail.com>
 */
class CmfBlockHelper extends Helper
{
    private SonataBlockHelper $sonataBlock;

    private EmbedBlocksParser $parser;

    private LoggerInterface $logger;

    public function __construct(SonataBlockHelper $sonataBlock, EmbedBlocksParser $parser, LoggerInterface $logger = null)
    {
        $this->sonataBlock = $sonataBlock;
        $this->parser = $parser;
        $this->logger = $logger;
    }

    /**
     * Looks for special markers that identify blocks and replaces
     * them with the result of rendering the specified identifier.
     */
    public function embedBlocks(string $text): string
    {
        return $this->parser->parse(
            $text,
            function ($id) {
                return $this->embeddedRender($id);
            }
        );
    }

    /**
     * @see SonataBlockHelper::render
     */
    public function render($block, array $options = []): string
    {
        return $this->sonataBlock->render($block, $options);
    }

    /**
     * @see SonataBlockHelper::includeJavascripts
     */
    public function includeJavascripts($media): string
    {
        return $this->sonataBlock->includeJavaScripts($media);
    }

    /**
     * @see SonataBlockHelper::includeStylesheets
     */
    public function includeStylesheets($media): string
    {
        return $this->sonataBlock->includeStylesheets($media);
    }

    public function getName(): string
    {
        return 'blocks';
    }

    /**
     * Executes the block as specified in the content.
     */
    protected function embeddedRender(string $name): string
    {
        $name = trim($name);

        try {
            return $this->sonataBlock->render(['name' => $name]);
        } catch (\Exception $e) {
            if ($this->logger) {
                $this->logger->warning(
                    sprintf('Failed to render block "%s" embedded in content: %s', $name, $e->getTraceAsString())
                );
            }
        }

        return '';
    }
}
