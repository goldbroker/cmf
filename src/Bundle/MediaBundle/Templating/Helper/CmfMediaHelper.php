<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2017 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\MediaBundle\Templating\Helper;

use Symfony\Cmf\Bundle\MediaBundle\FileInterface;
use Symfony\Cmf\Bundle\MediaBundle\ImageInterface;
use Symfony\Cmf\Bundle\MediaBundle\MediaManagerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Templating\Helper\Helper;

class CmfMediaHelper extends Helper
{
    protected $mediaManager;
    protected $generator;

    /**
     * Constructor.
     *
     * @param MediaManagerInterface $mediaManager
     * @param UrlGeneratorInterface $router        A Router instance
     */
    public function __construct(MediaManagerInterface $mediaManager, UrlGeneratorInterface $router)
    {
        $this->mediaManager = $mediaManager;
        $this->generator = $router;
    }

    /**
     * Generates a download URL from the given file.
     *
     * @param FileInterface   $file
     * @param bool|int|string $referenceType The type of reference (one of the constants in UrlGeneratorInterface)
     *
     * @return string The generated URL
     */
    public function downloadUrl(FileInterface $file, $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH)
    {
        $urlSafePath = $this->mediaManager->getUrlSafePath($file);

        return $this->generator->generate('cmf_media_download', ['path' => $urlSafePath], $referenceType);
    }

    /**
     * Generates a display URL from the given image.
     *
     * @param ImageInterface  $file
     * @param array           $options
     * @param bool|int|string $referenceType The type of reference (one of the constants in UrlGeneratorInterface)
     *
     * @return string The generated URL
     */
    public function displayUrl(ImageInterface $file, array $options = [], $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH)
    {
        $urlSafePath = $this->mediaManager->getUrlSafePath($file);

        return $this->generator->generate('cmf_media_image_display', ['path' => $urlSafePath], $referenceType);
    }

    /**
     * Returns the canonical name of this helper.
     *
     * @return string The canonical name
     */
    public function getName()
    {
        return 'cmf_media';
    }
}
