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

namespace Sonata\DoctrinePHPCRAdminBundle\Datagrid;

use Doctrine\ODM\PHPCR\Query\Query as PHPCRQuery;
use Sonata\AdminBundle\Datagrid\Pager as BasePager;

/**
 * Doctrine pager class.
 *
 * @author     Jonathan H. Wage <jonwage@gmail.com>
 * @author     Nacho Martin <nitram.ohcan@gmail.com>
 */
class Pager extends BasePager
{
    /**
     * Returns a query for counting the total results.
     *
     * @return int
     */
    public function countResults(): int
    {
        return \count($this->getCurrentPageResults(PHPCRQuery::HYDRATE_PHPCR));
    }

    /**
     * Get all the results for the pager instance.
     *
     * @param mixed $hydrationMode A hydration mode identifier
     *
     * @return array
     */
    public function getCurrentPageResults($hydrationMode = null): iterable
    {
        return $this->getQuery()->execute([], $hydrationMode);
    }

    /**
     * Initializes the pager setting the offset and maxResults in ProxyQuery
     * and obtaining the total number of pages.
     *
     * @throws \RuntimeException the QueryBuilder is uninitialized
     */
    public function init(): void
    {
        if (!$this->getQuery()) {
            throw new \RuntimeException('Uninitialized QueryBuilder');
        }

        if (0 === $this->getPage() || 0 === $this->getMaxPerPage() || 0 === $this->countResults()) {
            $this->setLastPage(0);
            $this->getQuery()->setFirstResult(0);
            $this->getQuery()->setMaxResults(0);
        } else {
            $offset = ($this->getPage() - 1) * $this->getMaxPerPage();
            $this->setLastPage((int) ceil($this->countResults() / $this->getMaxPerPage()));
            $this->getQuery()->setFirstResult($offset);
            $this->getQuery()->setMaxResults($this->getMaxPerPage());
        }
    }
}
