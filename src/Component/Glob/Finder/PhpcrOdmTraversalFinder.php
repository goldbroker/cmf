<?php

namespace Symfony\Cmf\Component\Glob\Finder;

use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Symfony\Cmf\Component\Glob\Parser\SelectorParser;

/**
 * PHPCR ODM finder which users traversal.
 *
 * {@inheritDoc}
 *
 * @author Daniel Leech <daniel@dantleech.com>
 */
class PhpcrOdmTraversalFinder extends AbstractTraversalFinder
{
    private ManagerRegistry $managerRegistry;

    public function __construct(ManagerRegistry $managerRegistry, SelectorParser $parser = null)
    {
        parent::__construct($parser);
        $this->managerRegistry = $managerRegistry;
    }

    private function getManager(): ObjectManager
    {
        return $this->managerRegistry->getManager();
    }

    /**
     * {@inheritDoc}
     */
    protected function getNode(array $pathSegments)
    {
        $absPath = '/' . implode('/', $pathSegments);
        return $this->getManager()->find(null, $absPath);
    }

    /**
     * {@inheritDoc}
     */
    protected function getChildren($parentNode, $selector)
    {
        return $this->getManager()->getChildren($parentNode, $selector);
    }
}
