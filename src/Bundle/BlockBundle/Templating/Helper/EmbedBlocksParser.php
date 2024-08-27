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

/**
 * Finds all the embedded blocks.
 *
 * @author Viorel Craescu <viorel@craescu.com>
 */
class EmbedBlocksParser
{
    private string $prefix;

    private string $postfix;

    public function __construct(string $prefix, string $postfix)
    {
        $this->prefix = $prefix;
        $this->postfix = $postfix;
    }

    public function parse(string $text, callable $callback): string
    {
        $segments = $this->segmentize($text);
        foreach ($segments as &$segment) {
            if (!is_array($segment)) {
                continue;
            }

            $segment[0] = $callback($segment[0]);
            $segment = implode('', $segment);
        }

        return implode('', $segments);
    }

    protected function segmentize(string $text): array
    {
        $segments = explode($this->prefix, $text);
        foreach ($segments as $index => &$segment) {
            if (0 === $index) {
                continue;
            }

            if (false !== strpos($segment, $this->postfix)) {
                $segment = array_filter(explode($this->postfix, $segment));
                $segment[0] = trim($segment[0]);
            }
        }

        return $segments;
    }
}
