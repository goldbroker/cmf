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

namespace Sonata\DoctrinePHPCRAdminBundle\Filter;

use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Sonata\DoctrinePHPCRAdminBundle\Filter\Filter as BaseFilter;
use Sonata\Form\Type\BooleanType;
use Sonata\AdminBundle\Filter\Model\FilterData;

class BooleanFilter extends BaseFilter
{
    /**
     * {@inheritdoc}
     */
    public function filter(ProxyQueryInterface $proxyQuery, $alias, $field, FilterData $data)
    {
        if (!$data->hasValue()) {
            return;
        }

        $value = $data->getValue();

        if (\is_array($value) || !\in_array($value, [BooleanType::TYPE_NO, BooleanType::TYPE_YES], true)) {
            return;
        }

        $where = $this->getWhere($proxyQuery);
        $where->eq()->field('a.'.$field)->literal(BooleanType::TYPE_YES === $value);

        // filter is active as we have now modified the query
        $this->setActive(true);
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultOptions(): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getFormOptions(): array
    {
        return [
            'field_type' => $this->getFieldType(),
            'field_options' => $this->getFieldOptions(),
            'operator_type' => 'hidden',
            'operator_options' => [],
            'label' => $this->getLabel(),
        ];
    }
}
