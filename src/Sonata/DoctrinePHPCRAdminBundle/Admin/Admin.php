<?php

declare(strict_types=1);

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\DoctrinePHPCRAdminBundle\Admin;

use PHPCR\Util\PathHelper;
use PHPCR\Util\UUIDHelper;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Route\RouteCollectionInterface;

/**
 * Extend the Admin class to incorporate phpcr changes.
 *
 * Especially make sure that there are no duplicated slashes in the generated urls
 *
 * @author Uwe Jäger <uwej711@googlemail.com>
 */
class Admin extends AbstractAdmin
{
    /**
     * Path to the root node in the repository under which documents of this
     * admin should be created.
     *
     * @var string
     */
    private $rootPath;

    /**
     * Set the root path in the repository. To be able to create new items,
     * this path must already exist.
     *
     * @param string $rootPath
     */
    public function setRootPath(?string $rootPath): void
    {
        $this->rootPath = $rootPath;
    }

    public function getRootPath(): ?string
    {
        return $this->rootPath;
    }

    protected function configure(): void
    {
        /**
         * Get subject.
         *
         * Overridden to allow a broader set of valid characters in the ID, and
         * if the ID is not a UUID, to call absolutizePath on the ID.
         */
        if (!$this->hasSubject() && $this->getRequest()) {
            $id = $this->getRequest()->get($this->getIdParameter());
            if (null === $id || !preg_match('#^[0-9A-Za-z/\-_]+$#', $id)) {
                $this->subject = false;
            } else {
                if (!UUIDHelper::isUUID($id)) {
                    $id = PathHelper::absolutizePath($id, '/');
                }
                $this->setSubject($this->getObject($id));
            }
        }
    }

    protected function configureQuery(ProxyQueryInterface $query): ProxyQueryInterface
    {
        $query->setRootPath($this->getRootPath());

        return $query;
    }

    /**
     * @param object $object
     *
     * @return string
     */
    public function id($object): ?string
    {
        return $this->getUrlsafeIdentifier($object);
    }

    /**
     * {@inheritdoc}
     */
    public function toString($object): string
    {
        if (!\is_object($object)) {
            return parent::toString($object);
        }

        if (method_exists($object, '__toString') && null !== $object->__toString()) {
            $string = (string) $object;

            return '' !== $string ? $string : $this->getTranslator()->trans('link_add', [], 'SonataAdminBundle');
        }

        $dm = $this->getModelManager()->getDocumentManager();
        if ($dm->contains($object)) {
            return PathHelper::getNodeName($dm->getUnitOfWork()->getDocumentId($object));
        }

        return parent::toString($object);
    }

    protected function configureRoutes(RouteCollectionInterface $collection): void
    {
        foreach (['edit', 'create', 'delete'] as $name) {
            if ($collection->has($name)) {
                $collection->get($name)->addOptions(['expose' => true]);
            }
        }
    }
}
