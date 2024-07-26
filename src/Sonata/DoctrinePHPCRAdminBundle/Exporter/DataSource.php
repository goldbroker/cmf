<?php

namespace Sonata\DoctrinePHPCRAdminBundle\Exporter;

use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Sonata\AdminBundle\Exporter\DataSourceInterface;

class DataSource implements DataSourceInterface
{
    public function createIterator(ProxyQueryInterface $query, array $fields): \Iterator
    {
        $query->getQueryBuilder()->distinct();
        $query->getQueryBuilder()->select($fields);
        $query->setFirstResult(null);
        $query->setMaxResults(null);

        return $query->getQueryBuilder()->getQuery()->getResult()->getIterator();
    }
}