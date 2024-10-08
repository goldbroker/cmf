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

use Symfony\Cmf\Bundle\MediaBundle\Editor\UploadEditorHelperInterface;
use Symfony\Cmf\Bundle\MediaBundle\FileInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

interface UploadFileHelperInterface
{
    /**
     * Allow non-uploaded files to validate for testing purposes.
     */
    public function setAllowNonUploadedFiles(bool $boolean);

    /**
     * Add an editor helper.
     */
    public function addEditorHelper(string $name, UploadEditorHelperInterface $helper);

    /**
     * Get helper.
     *
     * @param null|string $name leave null to get the default helper
     */
    public function getEditorHelper(?string $name = null): ?UploadEditorHelperInterface;

    /**
     * Handle the UploadedFile and create a FileInterface object.
     *
     * If $class is specified, an instance of that class should be created if
     * possible. If $class is null, the implementation chooses a suitable
     * class, e.g. through configuration.
     *
     * @param UploadedFile $uploadedFile
     * @param string|null $class        optional class name for the file class to generate
     *
     * @return FileInterface
     *
     * @internal param Request $request
     */
    public function handleUploadedFile(UploadedFile $uploadedFile, ?string $class = null): FileInterface;

    /**
     * Process upload and get a response.
     *
     * @param Request        $request
     * @param UploadedFile[] $uploadedFiles optionally get the uploaded file(s)
     *                                      from the Request yourself
     *
     * @return Response
     */
    public function getUploadResponse(Request $request, array $uploadedFiles = []): Response;
}
