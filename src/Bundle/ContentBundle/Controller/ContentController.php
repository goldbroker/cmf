<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2017 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\ContentBundle\Controller;

use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Twig\Environment;

/**
 * This controller renders the content object with a template defined on the route.
 */
class ContentController
{
    protected Environment $twig;

    protected ?string $defaultTemplate = null;

    protected ?ViewHandlerInterface $viewHandler = null;

    public function __construct(Environment $twig, ?ViewHandlerInterface $viewHandler = null, ?string $defaultTemplate = null)
    {
        $this->defaultTemplate = $defaultTemplate;
        $this->viewHandler = $viewHandler;
        $this->twig = $twig;
    }

    /**
     * Render the provided content.
     *
     * When using the publish workflow, enable the publish_workflow.request_listener
     * of the core bundle to have the contentDocument as well as the route
     * checked for being published.
     * We don't need an explicit check in this method.
     *
     * @param Request      $request
     * @param null|object  $contentDocument
     * @param null|string  $template   Symfony path of the template to render
     *                                 the content document. If omitted, the
     *                                 default template is used
     *
     * @return Response
     */
    public function indexAction(Request $request, $contentDocument = null, ?string $template = null): Response
    {
        if (null === $contentDocument) {
            throw new NotFoundHttpException();
        }

        $contentTemplate = $template ?: $this->defaultTemplate;

        $contentTemplate = str_replace(
            ['{_format}', '{_locale}'],
            [$request->getRequestFormat(), $request->getLocale()],
            $contentTemplate
        );

        $params = $this->getParams($request, $contentDocument);

        return $this->renderResponse($contentTemplate, $params);
    }

    protected function renderResponse(string $contentTemplate, array $params): Response
    {
        if ($this->viewHandler) {
            if (1 === count($params)) {
                $templateVar = key($params);
                $params = ['data' => reset($params)];
            }

            if (isset($templateVar)) {
                $params['templateVar'] = $templateVar;
            }

            $params['template'] = $contentTemplate;


            return $this->viewHandler->handle(
                new View($params)
            );
        }

        $response = new Response();
        $response->setContent($this->twig->render($contentTemplate, $params));

        return $response;
    }

    /**
     * Determine the parameters for rendering the template.
     *
     * This is mainly meant as a possible extension point in a custom
     * controller.
     *
     * @param Request $request
     * @param object  $contentDocument
     *
     * @return array
     */
    protected function getParams(Request $request, $contentDocument): array
    {
        return [
            'cmfMainContent' => $contentDocument,
        ];
    }
}
