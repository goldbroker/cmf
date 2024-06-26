<?php

namespace Symfony\Cmf\Component\Glob\Parser;

/**
 * Parser for selector patterns
 *
 * @author Daniel Leech <daniel@dantleech.com>
 */
class SelectorParser
{
    const T_STATIC = 1;
    const T_PATTERN = 2;
    const T_LAST = 4;

    /**
     * Parse the given selector.
     *
     * Returns an associative array of path elements to bitmasks.
     */
    public function parse($selector): array
    {
        if ('/' === $selector) {
            return array();
        }

        if ('/' !== substr($selector, 0, 1)) {
            throw new \InvalidArgumentException(sprintf(
                'Path "%s" must be absolute',
                $selector
            ));
        }

        $selector = substr($selector, 1);

        $segments = array();
        $elements = explode('/', $selector);

        foreach ($elements as $index => $element) {
            if ($this->processWildcard($element)) {
                $flags = self::T_PATTERN;
            } else {
                $flags = self::T_STATIC;
            }

            if ($index === (count($elements) - 1)) {
                $flags = $flags | self::T_LAST;
            }

            $segments[] = array($element, $flags);
        }

        return $segments;
    }

    /**
     * Check to see if the given (by reference) string contains
     * a wildcard.
     *
     * If the wildcard is escaped then remove the escape character.
     */
    private function processWildcard(string &$string): bool
    {
        if (false === $strpos = strpos($string, '*')) {
            return false;
        }

        $escapeChars = 0;
        while (isset($string[--$strpos]) && $string[$strpos] === '\\') {
            $escapeChars++;
        }


        $isEscaped = $escapeChars % 2 !== 0;

        // remove one of the escaping characters
        if ($isEscaped) {
            $string = substr($string, 0, $strpos - 1) . substr($string, $strpos);
        }

        return !$isEscaped;
    }
}
