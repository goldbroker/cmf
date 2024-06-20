<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2017 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\MediaBundle\Form\DataTransformer;

use Doctrine\ODM\PHPCR\Document\File;
use Symfony\Cmf\Bundle\MediaBundle\File\UploadFileHelperInterface;
use Symfony\Cmf\Bundle\MediaBundle\FileInterface;

class ModelToFileChildAwareTransformer extends ModelToFileTransformer
{
    /**
     * @var
     */
    private $emptyData;
    /**
     * @var
     */
    private $childOfNode;

    /**
     * @param UploadFileHelperInterface $helper
     * @param $class
     * @param $emptyData
     * @param $childOfNode
     */
    public function __construct(UploadFileHelperInterface $helper, $class, $emptyData = null, $childOfNode = null)
    {
        parent::__construct($helper, $class);

        $this->emptyData = $emptyData;
        $this->childOfNode = $childOfNode;
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($value)
    {
        $file = parent::reverseTransform($value);

        if (!$file instanceof FileInterface) {
            return $file;
        }

        if (!$this->emptyData instanceof FileInterface) {
            return $file;
        }

        if (null !== $this->childOfNode) {
            // The file node will get the file name as node name else, which conflicts in phpcr
            $this->emptyData->setName($this->childOfNode);
        }
        $this->emptyData->setContentFromStream($file->getContentAsStream());

        return $this->emptyData;
    }
}
