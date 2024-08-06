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
use Sonata\AdminBundle\Form\Type\Operator\StringOperatorType;
use Sonata\DoctrinePHPCRAdminBundle\Form\Type\Filter\ChoiceType;

class StringFilter extends Filter
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
        $type = $data->getType() ?? StringOperatorType::TYPE_CONTAINS;

        if ('' === $value) {
            return;
        }

        $where = $this->getWhere($proxyQuery);
        $isComparisonLowerCase = $this->getOption('compare_case_insensitive');
        $value = $isComparisonLowerCase ? strtolower($value) : $value;
        switch ($type) {
            case StringOperatorType::TYPE_EQUAL:
                if ($isComparisonLowerCase) {
                    $where->eq()->lowerCase()->field('a.'.$field)->end()->literal($value);
                } else {
                    $where->eq()->field('a.'.$field)->literal($value);
                }

                break;
            case StringOperatorType::TYPE_NOT_CONTAINS:
                $where->fullTextSearch('a.'.$field, '* -'.$value);

                break;
            case StringOperatorType::TYPE_CONTAINS:
                if ($isComparisonLowerCase) {
                    $where->like()->lowerCase()->field('a.'.$field)->end()->literal('%'.$value.'%');
                } else {
                    $where->like()->field('a.'.$field)->literal('%'.$value.'%');
                }

                break;
            default:
                $where->fullTextSearch('a.'.$field, $value);
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
            'compare_lower_case' => false,
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
