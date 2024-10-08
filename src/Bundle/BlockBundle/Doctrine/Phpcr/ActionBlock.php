<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2017 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\BlockBundle\Doctrine\Phpcr;

use Sonata\BlockBundle\Block\BlockContextInterface;
use Symfony\Cmf\Bundle\BlockBundle\Doctrine\Phpcr\AbstractBlock;
use Symfony\Component\HttpFoundation\Request;

/**
 * Block that renders a controller action.
 */
class ActionBlock extends AbstractBlock
{
    /**
     * The Symfony action string.
     */
    protected ?string $actionName = null;

    /**
     * List of request attributes or parameters to pass to the subrequest when
     * calling the action.
     *
     * The arguments are fetched with $request->get(), so if a parameter is
     * missing in the original request, the value will be null in the
     * subrequest.
     *
     * Defaults to pass on the _locale
     *
     * @var string[]
     */
    protected array $requestParams = ['_locale'];

    public function getType(): string
    {
        return 'cmf.block.action';
    }

    public function getActionName(): ?string
    {
        return $this->actionName;
    }

    public function setActionName(string $actionName): ActionBlock
    {
        $this->actionName = $actionName;

        return $this;
    }

    /**
     * Initialize default values if not explicitly set.
     */
    public function mergeDefaults(): void
    {
        if (null === $this->actionName) {
            $this->actionName = $this->getDefaultActionName();
        }
        if (empty($this->requestParams)) {
            $this->requestParams = $this->getRequestParams();
        }
    }

    /**
     * Set the list of request parameter names to pass to the subrequest when
     * rendering the action.
     */
    public function setRequestParams(array $params): ActionBlock
    {
        $this->requestParams = $params;

        return $this;
    }

    /**
     * Set the list of request parameter names to pass to the subrequest when
     * rendering the action.
     */
    public function getRequestParams(): array
    {
        return $this->requestParams;
    }

    /**
     * Extract information from the request or context to pass on to the
     * subrequest to render this action. Also adds the block and the
     * blockContext to the parameters.
     *
     * Note that the kind of subrequest the ActionBlockService is doing allows
     * to pass objects as request arguments.
     *
     * @param Request               $request      the master request
     * @param BlockContextInterface $blockContext passed in case an extending
     *                                            block needs the context to
     *                                            determine values to pass to
     *                                            the subrequest
     *
     * @return array list of arguments to pass to the subrequest
     */
    public function resolveRequestParams(Request $request, BlockContextInterface $blockContext): array
    {
        $params = [];
        foreach ($this->getRequestParams() as $param) {
            $params[$param] = $request->get($param);
        }
        $params['block'] = $this;
        $params['blockContext'] = $blockContext;

        return $params;
    }

    /**
     * Overload this method to define a default action name.
     */
    public function getDefaultActionName(): ?string
    {
        return null;
    }
}
