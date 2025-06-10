<?php

namespace Symfony\Cmf\Bundle\MultiDomainBundle\Doctrine\Phpcr;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Cmf\Component\Routing\Candidates\CandidatesInterface;
use PHPCR\Util\PathHelper;
use Symfony\Cmf\Bundle\RoutingBundle\Doctrine\Phpcr\PrefixCandidates;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Cmf\Component\Routing\Candidates\Candidates;

/**
 * Host based strategy.
 */
class HostCandidates extends Candidates
{
    private CandidatesInterface $prefixCandidates;

    public function __construct(
        private readonly array $prefixes,
        private readonly array $domains = [],
        ManagerRegistry $doctrine = null,
        int $limit = 20,
        private readonly array $routeBasepaths = [],
    ) {
        $locales = array_keys($domains);
        parent::__construct($locales, $limit);
        $this->prefixCandidates = new PrefixCandidates($prefixes, $locales, $doctrine, $limit);
    }

    public function getCandidates(Request $request): array
    {
        $candidates = $this->prefixCandidates->getCandidates($request);
        $host = $request->getHost();
        $locale = array_search($host, $this->domains);

        foreach ($candidates as $key => $candidate) {
            foreach ($this->routeBasepaths as $routeBasePath) {
                if (str_starts_with($candidate, $routeBasePath . '/' . $locale)) {
                    continue;
                }

                unset($candidates[$key]);
            }
        }

        return $candidates;
    }

    public function isCandidate(string $name): bool
    {
        return $this->prefixCandidates->isCandidate($name);
    }

    public function restrictQuery(object $queryBuilder): void
    {
        $this->prefixCandidates->restrictQuery($queryBuilder);
    }

    protected function determineLocale($url): bool|string
    {
        return $this->prefixCandidates->determineLocale($url);
    }

    public function setManagerName(?string $manager): void
    {
        $this->prefixCandidates->setManagerName($manager);
    }
}
