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
use Sonata\AdminBundle\Form\Type\Operator\ContainsOperatorType;
use Sonata\AdminBundle\Filter\Model\FilterData;

class ChoiceFilter extends Filter
{
    /**
     * {@inheritdoc}
     */
    public function filter(ProxyQueryInterface $proxyQuery, $alias, $field, FilterData $data)
    {
        if (!$data->hasValue()) {
            return;
        }

        $values = (array) $data->getValue();
        $type = $data->getType();

        // clean values
        foreach ($values as $key => $value) {
            $value = trim((string) $value);
            if (!$value) {
                unset($values[$key]);
            } else {
                $values[$key] = $value;
            }
        }

        // if values not set or "all" specified, do not do this filter
        if (!$values || \in_array('all', $values, true)) {
            return;
        }

        $andX = $this->getWhere($proxyQuery)->andX();

        foreach ($values as $value) {
            if (ContainsOperatorType::TYPE_NOT_CONTAINS === $type) {
                $andX->not()->like()->field('a.'.$field)->literal('%'.$value.'%');
            } elseif (ContainsOperatorType::TYPE_CONTAINS === $type) {
                $andX->like()->field('a.'.$field)->literal('%'.$value.'%');
            } elseif (ContainsOperatorType::TYPE_EQUAL === $type) {
                $andX->like()->field('a.'.$field)->literal($value);
            }
        }

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
            'label' => $this->getLabel(),
            'operator_type' => ContainsOperatorType::class,
        ];
    }
}
