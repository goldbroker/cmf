<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2017 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\MediaBundle;

/**
 * Interface for file objects containing directories.
 *
 * The path to a file is: /path/to/file/filename.ext
 *
 * For PHPCR the id is being the path.
 */
interface DirectoryInterface extends HierarchyInterface
{
    /**
     * Returns the contents of this directory.
     *
     * @return HierarchyInterface[]
     */
    public function getChildren();
}
