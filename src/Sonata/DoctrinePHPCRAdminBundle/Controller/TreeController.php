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

namespace Sonata\DoctrinePHPCRAdminBundle\Controller;

use Doctrine\Bundle\PHPCRBundle\ManagerRegistry;
use PHPCR\SessionInterface;
use PHPCR\Util\PathHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * A controller to render the tree block.
 */
class TreeController extends AbstractController
{
    private string $template = '@SonataDoctrinePHPCRAdmin/Tree/tree.html.twig';

    /**
     * @var SessionInterface
     */
    private $session;

    private array $treeConfiguration;

    public function __construct(
        ManagerRegistry $manager,
        string $sessionName,
        array $treeConfiguration,
        string $defaultRepositoryName,
        ?string $template = null
    ) {
        if ($template) {
            $this->template = $template;
        }

        $this->session = $manager->getConnection($sessionName);
        if (null === $treeConfiguration['repository_name']) {
            $treeConfiguration['repository_name'] = $defaultRepositoryName;
        }
        $this->treeConfiguration = $treeConfiguration;
    }

    /**
     * Renders a tree, passing the routes for each of the admin types (document types)
     * to the view.
     *
     * @return Response
     */
    public function treeAction(Request $request)
    {
        $root = $request->attributes->get('root');

        return $this->render($this->template, [
            'root_node' => $root,
            'routing_defaults' => $this->treeConfiguration['routing_defaults'],
            'repository_name' => $this->treeConfiguration['repository_name'],
            'reorder' => $this->treeConfiguration['reorder'],
            'move' => $this->treeConfiguration['move'],
            'sortable_by' => $this->treeConfiguration['sortable_by'],
        ]);
    }

    /**
     * Reorder $moved (child of $parent) before or after $target.
     *
     * @return Response
     */
    public function reorderAction(Request $request)
    {
        $parentPath = $request->get('parent');
        $dropedAtPath = $request->get('dropped');
        $targetPath = $request->get('target');
        $position = $request->get('position');

        if (null === $parentPath || null === $dropedAtPath || null === $targetPath) {
            return new JsonResponse(['Parameters parent, dropped and target has to be set to reorder.'], Response::HTTP_BAD_REQUEST);
        }

        if (\in_array($position, ['over', 'child'], true)) {
            return new JsonResponse(['Can not reorder when dropping into a collection.'], Response::HTTP_BAD_REQUEST);
        }

        $before = 'before' === $position;
        $parentNode = $this->session->getNode($parentPath);
        $targetName = PathHelper::getNodeName($targetPath);
        if (!$before) {
            $nodesIterator = $parentNode->getNodes();
            $nodesIterator->rewind();
            while ($nodesIterator->valid()) {
                if ($nodesIterator->key() === $targetName) {
                    break;
                }
                $nodesIterator->next();
            }
            $targetName = null;
            if ($nodesIterator->valid()) {
                $nodesIterator->next();
                if ($nodesIterator->valid()) {
                    $targetName = $nodesIterator->key();
                }
            }
        }
        $parentNode->orderBefore($targetName, PathHelper::getNodeName($dropedAtPath));
        $this->session->save();

        return new Response('', Response::HTTP_NO_CONTENT);
    }
}
