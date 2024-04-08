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

namespace Sonata\DoctrinePHPCRAdminBundle\Twig\Extension;

use PHPCR\NodeInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class SonataDoctrinePHPCRAdminExtension extends AbstractExtension
{
    /**
     * {@inheritdoc}
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('render_node_property', [$this, 'renderNodeProperty'], ['is_safe' => ['html']]),
            new TwigFilter('render_node_path', [$this, 'renderNodePath'], ['is_safe' => ['html']]),
        ];
    }

    /**
     * Renders a property of a node.
     *
     * @param string $property
     *
     * @return string String representation of the property
     */
    public function renderNodeProperty(NodeInterface $node, string $property): string
    {
        return $node->getProperty($property)->getString();
    }

    /**
     * Renders a path of a node.
     *
     * @return string Node path
     */
    public function renderNodePath(NodeInterface $node): string
    {
        return $node->getPath();
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'sonata_doctrine_phpcr_admin';
    }
}
