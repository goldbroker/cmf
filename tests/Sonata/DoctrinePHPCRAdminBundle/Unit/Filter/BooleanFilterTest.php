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

namespace Tests\Sonata\DoctrinePHPCRAdminBundle\Unit\Filter;

use Sonata\DoctrinePHPCRAdminBundle\Filter\BooleanFilter;
use Sonata\Form\Type\BooleanType;

class BooleanFilterTest extends BaseTestCase
{
    /**
     * @var BooleanFilter
     */
    private $filter;

    protected function setUp(): void
    {
        parent::setUp();
        $this->filter = new BooleanFilter();
    }

    public function testFilterNullData(): void
    {
        $this->filter->filter($this->proxyQuery, null, 'somefield', null);
        static::assertFalse($this->filter->isActive());
    }

    public function testFilterEmptyArrayData(): void
    {
        $this->filter->filter($this->proxyQuery, null, 'somefield', []);
        static::assertFalse($this->filter->isActive());
    }

    public function testFilterEmptyArrayDataSpecifiedType(): void
    {
        $this->filter->filter($this->proxyQuery, null, 'somefield', ['type' => BooleanType::TYPE_YES]);
        static::assertFalse($this->filter->isActive());
    }

    public function testFilterEmptyArrayDataWithMeaninglessValue(): void
    {
        $this->filter->filter($this->proxyQuery, null, 'somefield', ['type' => BooleanType::TYPE_YES, 'value' => 'someValue']);
        static::assertFalse($this->filter->isActive());
    }

    public function getFilters()
    {
        return [
            ['eq', BooleanType::TYPE_YES, true],
            ['eq', BooleanType::TYPE_NO, false],
        ];
    }

    /**
     * @dataProvider getFilters
     */
    public function testFilterSwitch($operatorMethod, $value, $expectedValue): void
    {
        $this->filter->filter(
            $this->proxyQuery,
            null,
            'somefield',
            ['type' => '', 'value' => $value]
        );

        $opDynamic = $this->qbTester->getNode('where.constraint.operand_dynamic');
        $opStatic = $this->qbTester->getNode('where.constraint.operand_static');

        static::assertSame('a', $opDynamic->getAlias());
        static::assertSame('somefield', $opDynamic->getField());
        static::assertSame($expectedValue, $opStatic->getValue());

        static::assertTrue($this->filter->isActive());
    }
}
