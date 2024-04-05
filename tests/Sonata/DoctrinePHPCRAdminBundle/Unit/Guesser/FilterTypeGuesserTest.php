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

namespace Tests\Sonata\DoctrinePHPCRAdminBundle\Unit\Guesser;

use Doctrine\Bundle\PHPCRBundle\ManagerRegistry;
use Doctrine\ODM\PHPCR\DocumentRepository;
use Doctrine\Persistence\Mapping\ClassMetadata;
use PHPUnit\Framework\TestCase;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\FieldDescription\FieldDescriptionFactoryInterface;
use Sonata\AdminBundle\FieldDescription\FieldDescriptionInterface;
use Sonata\AdminBundle\Model\ModelManagerInterface;
use Sonata\DoctrinePHPCRAdminBundle\Admin\Admin;
use Sonata\DoctrinePHPCRAdminBundle\Filter\StringFilter;
use Sonata\DoctrinePHPCRAdminBundle\Guesser\FilterTypeGuesser;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Guess\Guess;
use Symfony\Component\Form\Guess\TypeGuess;

class FilterTypeGuesserTest extends TestCase
{
    public function testGuessType(): void
    {
        $managerRegistry = $this->createMock(ManagerRegistry::class);

        $guesser = new FilterTypeGuesser(
            $managerRegistry
        );

        $admin = $this->createMock(AbstractAdmin::class);
        $admin->method('getClass')->willReturn('Whatever');
        $admin->method('getModelManager')->willReturn($this->createMock(ModelManagerInterface::class));
        $admin->method('getFieldDescriptionFactory')->willReturn($this->createMock(FieldDescriptionFactoryInterface::class));
        $fieldDescription = $admin->createFieldDescription('whatever');
        $typeGuess = $guesser->guess($fieldDescription);

        static::assertInstanceOf(
            TypeGuess::class,
            $typeGuess
        );
        static::assertSame(
            StringFilter::class,
            $typeGuess->getType()
        );
        static::assertSame(
            [
                'field_type' => TextType::class,
                'field_options' => [],
                'options' => [],
                'field_name' => $fieldDescription->getName(),
            ],
            $typeGuess->getOptions()
        );

        static::assertSame(
            Guess::LOW_CONFIDENCE,
            $typeGuess->getConfidence()
        );
    }
}
