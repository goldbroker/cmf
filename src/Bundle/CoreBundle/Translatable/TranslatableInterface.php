<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\CoreBundle\Translatable;

/**
 * An interface for (potentially) translatable models.
 *
 * A metadata listener configures if the model really is translated or not.
 * The locale will be set to boolean false if translations are disabled.
 */
interface TranslatableInterface
{
    /**
     * @return ?string The locale of this model or null if
     *                     translations are disabled in this project
     */
    public function getLocale(): ?string;

    /**
     * @param ?string $locale The local for this model, or null if
     *                            translations are disabled in this project
     */
    public function setLocale(?string $locale): void;
}
