<?php

declare(strict_types=1);

namespace Sonata\PHPCRTranslationBundle\Admin\Extension;

use Doctrine\ODM\PHPCR\DocumentManager;
use Doctrine\ODM\PHPCR\Translation\LocaleChooser\LocaleChooser;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Sonata\TranslationBundle\Admin\Extension\AbstractTranslatableAdminExtension;
use Sonata\TranslationBundle\Checker\TranslatableChecker;
use Sonata\TranslationBundle\Provider\LocaleProviderInterface;

class TranslatableAdminExtension extends AbstractTranslatableAdminExtension
{
    private LocaleChooser $localeChooser;

    public function __construct(
        TranslatableChecker $translatableChecker,
        LocaleChooser $localeChooser,
        LocaleProviderInterface $defaultTranslationLocaleOrLocaleProvider
    ) {
        parent::__construct($translatableChecker, $defaultTranslationLocaleOrLocaleProvider);
        $this->localeChooser = $localeChooser;
    }

    public function alterObject(AdminInterface $admin, object $object): void
    {
        $locale = $this->getTranslatableLocale();
        $documentManager = $this->getDocumentManager($admin);
        $unitOfWork = $documentManager->getUnitOfWork();

        if (
            $this->getTranslatableChecker()->isTranslatable($object)
            && ($unitOfWork->getCurrentLocale($object) !== $locale)
        ) {
            if (!\is_callable([$object, 'getId'])) {
                throw new \InvalidArgumentException(sprintf(
                    'The object passed to "%s()" method MUST be properly configured using'
                    .' "doctrine/phpcr-odm" in order to have a "getId" method.',
                    __METHOD__
                ));
            }

            $object = $this->getDocumentManager($admin)->findTranslation($admin->getClass(), $object->getId(), $locale);
            // if the translation did not yet exists, the locale will be
            // the fallback locale. This makes sure the new locale is set.
            if ($unitOfWork->getCurrentLocale($object) !== $locale) {
                $documentManager->bindTranslation($object, $locale);
            }
        }
    }

    public function configureQuery(AdminInterface $admin, ProxyQueryInterface $query): void
    {
        $this->localeChooser->setLocale($this->getTranslatableLocale());
    }

    /**
     * @template T of object
     * @phpstan-param AdminInterface<T> $admin
     *
     * @return DocumentManager
     */
    protected function getDocumentManager(AdminInterface $admin): DocumentManager
    {
        return $admin->getModelManager()->getDocumentManager();
    }
}
