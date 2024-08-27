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
use Sonata\AdminBundle\Filter\Model\FilterData;
use Sonata\AdminBundle\Form\Type\Operator\ContainsOperatorType;

class NodeNameFilter extends Filter
{
    /**
     * {@inheritdoc}
     */
    public function filter(ProxyQueryInterface $proxyQuery, $alias, $field, FilterData $data)
    {
        if (!$data->hasValue()) {
            return;
        }

        $value = trim((string) ($data->getValue() ?? ''));
        $type = $data->getType() ?? ContainsOperatorType::TYPE_CONTAINS;


        if ('' === $value) {
            return;
        }

        $where = $this->getWhere($proxyQuery);

        switch ($type) {
            case ContainsOperatorType::TYPE_EQUAL:
                $where->eq()->localName($alias)->literal($value);

                break;
            case ContainsOperatorType::TYPE_CONTAINS:
            default:
                $where->like()->localName($alias)->literal('%'.$value.'%');
        }

        // filter is active as we have now modified the query
        $this->active = true;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultOptions(): array
    {
        return [
            'format' => '%%%s%%',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getRenderSettings(): array
    {
        return ['Sonata\DoctrinePHPCRAdminBundle\Form\Type\Filter\ChoiceType', [
            'field_type' => $this->getFieldType(),
            'field_options' => $this->getFieldOptions(),
            'label' => $this->getLabel(),
        ]];
    }
}
