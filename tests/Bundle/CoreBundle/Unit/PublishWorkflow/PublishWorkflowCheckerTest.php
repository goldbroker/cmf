<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Symfony\Cmf\Bundle\CoreBundle\Unit\PublishWorkflow;

use PHPUnit\Framework\TestCase;
use Symfony\Cmf\Bundle\CoreBundle\PublishWorkflow\PublishableReadInterface;
use Symfony\Cmf\Bundle\CoreBundle\PublishWorkflow\PublishWorkflowChecker;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class PublishWorkflowCheckerTest extends TestCase
{
    private $publishWorkflowChecker;

    private $role;

    private $document;

    private $accessDecisionManager;

    private $authorizationChecker;

    private $tokenStorage;

    public function setUp(): void
    {
        $this->role = 'IS_FOOBAR';
        $this->authorizationChecker = \Mockery::mock(AuthorizationCheckerInterface::class);
        $this->tokenStorage = \Mockery::mock(TokenStorageInterface::class);
        $this->document = \Mockery::mock(PublishableReadInterface::class);
        $this->accessDecisionManager = \Mockery::mock(AccessDecisionManagerInterface::class);

        $this->publishWorkflowChecker = new PublishWorkflowChecker(
            $this->tokenStorage,
            $this->authorizationChecker,
            $this->accessDecisionManager,
            $this->role
        );
    }

    protected function tearDown(): void
    {
        \Mockery::close();
    }

    public function testIsGranted()
    {
        $token = new AnonymousToken('', '');
        $this->tokenStorage->shouldReceive('getToken')->andReturn($token);

        $this->authorizationChecker->shouldNotReceive('isGranted');

        $this->accessDecisionManager
            ->shouldReceive('decide')->once()
            ->with($token, [PublishWorkflowChecker::VIEW_ANONYMOUS_ATTRIBUTE], $this->document)
            ->andReturn(true);

        $this->assertTrue($this->publishWorkflowChecker->isGranted(PublishWorkflowChecker::VIEW_ANONYMOUS_ATTRIBUTE, $this->document));
    }

    public function testNotHasBypassRole()
    {
        $token = new AnonymousToken('', '');
        $this->tokenStorage->shouldReceive('getToken')->andReturn($token);

        $this->authorizationChecker->shouldReceive('isGranted')->once()->with($this->role)->andReturn(false);

        $this->accessDecisionManager
            ->shouldReceive('decide')->once()
            ->with($token, [PublishWorkflowChecker::VIEW_ATTRIBUTE], $this->document)
            ->andReturn(true);

        $this->assertTrue($this->publishWorkflowChecker->isGranted(PublishWorkflowChecker::VIEW_ATTRIBUTE, $this->document));
    }

    public function testHasBypassRole()
    {
        $token = new AnonymousToken('', '');
        $this->tokenStorage->shouldReceive('getToken')->andReturn($token);

        $this->authorizationChecker->shouldReceive('isGranted')->once()->with($this->role)->andReturn(true);

        $this->accessDecisionManager->shouldNotReceive('decide');

        $this->assertTrue($this->publishWorkflowChecker->isGranted(PublishWorkflowChecker::VIEW_ATTRIBUTE, $this->document));
    }

    public function testNoFirewall()
    {
        $token = new AnonymousToken('', '');
        $this->tokenStorage->shouldReceive('getToken')->andReturn($token);

        $this->authorizationChecker->shouldReceive('isGranted')->once();

        $this->accessDecisionManager
            ->shouldReceive('decide')->once()
            ->with(\Mockery::type(AnonymousToken::class), [PublishWorkflowChecker::VIEW_ATTRIBUTE], $this->document)
            ->andReturn(true);

        $this->assertTrue($this->publishWorkflowChecker->isGranted(PublishWorkflowChecker::VIEW_ATTRIBUTE, $this->document));
    }
}
