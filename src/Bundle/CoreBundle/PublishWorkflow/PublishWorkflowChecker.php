<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\CoreBundle\PublishWorkflow;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * The publish workflow decides if a content is allowed to be shown. Contrary
 * to the symfony core security context, this is even possible without a
 * firewall configured for the current route.
 *
 * The access decision manager is configured to be unanimous by default, and
 * provided with all published voters tagged with cmf_published_voter.
 *
 * If the VIEW attribute is used and there is a firewall in place, there is a
 * check if the current user is granted the bypassing role and if so, he can
 * see even unpublished content.
 *
 * If VIEW_ANONYMOUS is used, the publication check is never bypassed.
 *
 * @author David Buchmann <mail@davidbu.ch>
 */
class PublishWorkflowChecker implements AuthorizationCheckerInterface
{
    /**
     * This attribute means the user is allowed to see this content, either
     * because it is published or because he is granted the bypassingRole.
     */
    const VIEW_ATTRIBUTE = 'VIEW';

    /**
     * This attribute means the content is available for viewing by anonymous
     * users. This can be used where the role based exception from the
     * publication check is not wanted.
     *
     * The bypass role is handled by the workflow checker, the individual
     * voters should treat VIEW and VIEW_ANONYMOUS the same.
     */
    const VIEW_ANONYMOUS_ATTRIBUTE = 'VIEW_ANONYMOUS';

    /**
     * @var bool|string Role allowed to bypass the published check if the
     *                  VIEW attribute is used, or false to never bypass
     */
    private $bypassingRole;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * @var AccessDecisionManagerInterface
     */
    private $accessDecisionManager;

    /**
     * @var TokenInterface
     */
    private $token;

    /**
     * @param AccessDecisionManagerInterface $accessDecisionManager Service to do the actual decision
     * @param bool|string                    $bypassingRole         A role that is allowed to bypass
     *                                                              the published check if we ask for
     *                                                              the VIEW permission. Ignored on
     *                                                              VIEW_ANONYMOUS
     */
    public function __construct(TokenStorageInterface $tokenStorage, AuthorizationCheckerInterface $authorizationChecker, AccessDecisionManagerInterface $accessDecisionManager, $bypassingRole = false)
    {
        $this->tokenStorage = $tokenStorage;
        $this->authorizationChecker = $authorizationChecker;
        $this->accessDecisionManager = $accessDecisionManager;
        $this->bypassingRole = $bypassingRole;
    }

    /**
     * {@inheritdoc}
     */
    public function isGranted($attribute, $object = null): bool
    {
        $token = $this->tokenStorage->getToken();

        if (self::VIEW_ATTRIBUTE === $attribute
            && null !== $token
            && $this->authorizationChecker->isGranted($this->bypassingRole)
        ) {
            return true;
        }

        return $this->accessDecisionManager->decide($token, [$attribute], $object);
    }
}
