<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2017 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\MediaBundle\File;

use Symfony\Cmf\Bundle\MediaBundle\Editor\BrowserEditorHelperInterface;

class BrowserFileHelper
{
    protected array $editorHelpers = [];
    protected $defaultBrowser;

    public function __construct($defaultBrowser = null)
    {
        $this->defaultBrowser = $defaultBrowser;
    }

    /**
     * Add an editor helper.
     */
    public function addEditorHelper(string $name, string $editor, BrowserEditorHelperInterface $helper)
    {
        $this->editorHelpers[$name][$editor] = $helper;
    }

    /**
     * Get helper.
     *
     * @param null|string $name    leave null to get the default helper
     * @param null|string $browser leave null to get the default helper
     *
     * @return BrowserEditorHelperInterface|null
     */
    public function getEditorHelper(?string $name = null, ?string $browser = null): ?BrowserEditorHelperInterface
    {
        if ($name && isset($this->editorHelpers[$name]) && count($this->editorHelpers[$name]) > 0) {
            if ($browser && isset($this->editorHelpers[$name][$browser])) {
                // found name and browser
                return $this->editorHelpers[$name][$browser];
            }

            if ($this->defaultBrowser && isset($this->editorHelpers[$name][$this->defaultBrowser])) {
                // get default
                return $this->editorHelpers[$name][$this->defaultBrowser];
            }
        }

        if ($this->defaultBrowser && isset($this->editorHelpers['default'][$this->defaultBrowser])) {
            // get default
            return $this->editorHelpers['default'][$this->defaultBrowser];
        }

        return null;
    }
}
