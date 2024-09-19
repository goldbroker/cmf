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
use Sonata\AdminBundle\Form\Type\Operator\DateOperatorType;
use Sonata\AdminBundle\Filter\Model\FilterData;

class DateFilter extends Filter
{
    /**
     * {@inheritdoc}
     */
    public function filter(ProxyQueryInterface $proxyQuery, $alias, $field, FilterData $data)
    {
        if (!$data->hasValue()) {
            return;
        }

        $type = $data->getType() ?? DateOperatorType::TYPE_EQUAL;

        $where = $this->getWhere($proxyQuery);

        $from = $data->getValue();
        $to = new \DateTime($from->format('Y-m-d').' +86399 seconds'); // 23 hours 59 minutes 59 seconds

        switch ($type) {
            case DateOperatorType::TYPE_GREATER_EQUAL:
                $where->gte()->field('a.'.$field)->literal($from);

                break;
            case DateOperatorType::TYPE_GREATER_THAN:
                $where->gt()->field('a.'.$field)->literal($from);

                break;
            case DateOperatorType::TYPE_LESS_EQUAL:
                $where->lte()->field('a.'.$field)->literal($from);

                break;
            case DateOperatorType::TYPE_LESS_THAN:
                $where->lt()->field('a.'.$field)->literal($from);

                break;
            case DateOperatorType::TYPE_EQUAL:
            default:
                $where->andX()
                    ->gte()->field('a.'.$field)->literal($from)->end()
                    ->lte()->field('a.'.$field)->literal($to)->end();
        }

        // filter is active as we have now modified the query
        $this->setActive(true);
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultOptions(): array
    {
        return [
            'date_format' => 'yyyy-MM-dd',
        ];
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
            'operator_type' => DateOperatorType::class,
        ];
    }
}
