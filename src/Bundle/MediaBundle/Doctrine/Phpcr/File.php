<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2017 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\MediaBundle\Doctrine\Phpcr;

use Doctrine\ODM\PHPCR\Document\AbstractFile;
use Doctrine\ODM\PHPCR\Document\File as DoctrineOdmFile;
use Symfony\Cmf\Bundle\MediaBundle\BinaryInterface;
use Symfony\Cmf\Bundle\MediaBundle\FileInterface;
use Symfony\Cmf\Bundle\MediaBundle\FileSystemInterface;

/**
 * This class represents a CmfMedia Doctrine Phpcr file.
 *
 * Note: the modified information from the content is used.
 */
class File extends DoctrineOdmFile implements FileInterface, BinaryInterface
{
    protected ?string $description = null;

    protected ?string $copyright = null;

    protected ?string $authorName = null;

    protected array $metadata = [];

    /**
     * {@inheritdoc}
     */
    public function getName(): ?string
    {
        return $this->nodename;
    }

    /**
     * {@inheritdoc}
     */
    public function setName($name): File
    {
        $this->nodename = $name;

        return $this;
    }

    public function getParent(): ?object
    {
        return $this->parent;
    }

    /**
     * {@inheritdoc}
     */
    public function setParentDocument($parent): self
    {
        $this->parent = $parent;

        if ($parent instanceof Directory) {
            $parent->addChild($this);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setParent($parent): self
    {
        return $this->setParentDocument($parent);
    }

    /**
     * {@inheritdoc}
     */
    public function setDescription($description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * {@inheritdoc}
     */
    public function setCopyright($copyright): self
    {
        $this->copyright = $copyright;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCopyright(): ?string
    {
        return $this->copyright;
    }

    /**
     * {@inheritdoc}
     */
    public function setAuthorName($authorName): self
    {
        $this->authorName = $authorName;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthorName(): ?string
    {
        return $this->authorName;
    }

    /**
     * {@inheritdoc}
     */
    public function setMetadata(array $metadata)
    {
        $this->metadata = $metadata;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getMetadata(): array
    {
        return $this->metadata;
    }

    /**
     * {@inheritdoc}
     */
    public function getMetadataValue($name, $default = null)
    {
        return $this->metadata[$name] ?? $default;
    }

    /**
     * {@inheritdoc}
     */
    public function setMetadataValue($name, $value): self
    {
        $this->metadata[$name] = $value;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function unsetMetadataValue($name): self
    {
        unset($this->metadata[$name]);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getContentAsString(): string
    {
        $stream = $this->getContentAsStream();
        if (!is_resource($stream)) {
            return '';
        }

        $content = stream_get_contents($stream);
        rewind($stream);

        return $content !== false ? $content : '';
    }

    /**
     * {@inheritdoc}
     */
    public function setContentFromString($content): self
    {
        if (!is_resource($content)) {
            $stream = fopen('php://memory', 'rwb+');
            fwrite($stream, $content);
            rewind($stream);
        } else {
            $stream = $content;
        }

        $this->setContentFromStream($stream);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function copyContentFromFile($file): self
    {
        if ($file instanceof \SplFileInfo) {
            $this->setFileContentFromFilesystem($file->getPathname());
        } elseif ($file instanceof BinaryInterface) {
            $this->setContentFromStream($file->getContentAsStream());
        } elseif ($file instanceof FileSystemInterface) {
            $this->setFileContentFromFilesystem($file->getFileSystemPath());
        } elseif ($file instanceof FileInterface) {
            $this->setContentFromString($file->getContentAsString());
        } else {
            $type = is_object($file) ? get_class($file) : gettype($file);
            throw new \InvalidArgumentException(sprintf(
                'File is not a valid type, "%s" given.',
                 $type
            ));
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getContentAsStream()
    {
        $stream = $this->getContent()->getData();
        if (!is_resource($stream)) {
            return null;
        }
        rewind($stream);

        return $stream;
    }

    /**
     * {@inheritdoc}
     */
    public function setContentFromStream($stream): self
    {
        if (!is_resource($stream)) {
            throw new \InvalidArgumentException('Expected a stream');
        }

        $this->getContent()->setData($stream);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSize()
    {
        try {
            $size = (int) $this->getContent()->getSize();
        } catch (\BadMethodCallException $e) {
            $stat = fstat($this->getContentAsStream());
            $size = $stat['size'];
        }

        return $size;
    }

    /**
     * @param string $contentType
     *
     * @return $this
     */
    public function setContentType($contentType): self
    {
        $this->getContent()->setMimeType($contentType);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getContentType(): string
    {
        return $this->getContent()->getMimeType();
    }

    /**
     * {@inheritdoc}
     */
    public function getExtension()
    {
        return pathinfo($this->getName(), PATHINFO_EXTENSION);
    }

    /**
     * {@inheritdoc}
     */
    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->created;
    }

    /**
     * {@inheritdoc}
     */
    public function getUpdatedAt(): \DateTimeInterface
    {
        return $this->getContent()->getLastModified();
    }

    /**
     * Getter for updatedBy.
     *
     * @return string name of the (jcr) user who updated the file
     */
    public function getUpdatedBy(): ?string
    {
        return $this->getContent()->getLastModifiedBy();
    }
}
