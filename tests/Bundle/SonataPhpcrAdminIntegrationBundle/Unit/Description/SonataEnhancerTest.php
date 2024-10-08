<?php

namespace Tests\Symfony\Cmf\Bundle\SonataPhpcrAdminIntegrationBundle\Unit\Description;

use Prophecy\Argument;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Admin\Pool;
use Sonata\AdminBundle\Model\AuditManagerInterface;
use Sonata\AdminBundle\Model\ModelManagerInterface;
use Sonata\AdminBundle\Route\PathInfoBuilder;
use Symfony\Cmf\Bundle\SonataPhpcrAdminIntegrationBundle\Description\SonataEnhancer;
use Symfony\Cmf\Component\Resource\Description\Description;
use Symfony\Cmf\Component\Resource\Description\Descriptor;
use Symfony\Cmf\Component\Resource\Repository\Resource\CmfResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class SonataEnhancerTest extends \PHPUnit\Framework\TestCase
{
    private $admin;

    private $pool;

    public function setUp(): void
    {
        $this->admin = new TestAdmin();
        $this->admin->setCode('test');
        $this->admin->setModelClass('stdClass');
        $this->admin->setBaseControllerName('FooController');
        $this->admin->setSecurityHandler($this->createMock(\Sonata\AdminBundle\Security\Handler\SecurityHandlerInterface::class));

        $this->container = new ContainerBuilder();
        $this->pool = new Pool(
            $this->container,
            [
                'Test',
                'std_class_admin'
            ],
            [
                'logo'
            ],
            [
                'stdClass' => ['std_class_admin'],
                'Exception' => ['std_class_admin'],
            ]
        );

        $this->container->set('std_class_admin', $this->admin);
        $this->generator = $this->prophesize(UrlGeneratorInterface::class);
        $this->resource = $this->prophesize(CmfResource::class);

        $this->modelManager = $this->prophesize(ModelManagerInterface::class);
        $this->modelManager->getUrlsafeIdentifier(Argument::cetera())->willReturn('id');

        $auditManager = $this->prophesize(AuditManagerInterface::class);
        $auditManager->hasReader(Argument::cetera())->willReturn(false);

        $this->routeBuilder = new PathInfoBuilder($auditManager->reveal());
        $this->admin->setRouteBuilder($this->routeBuilder);
        $this->admin->setModelManager($this->modelManager->reveal());
    }

    /**
     * @dataProvider provideDescriptionData
     */
    public function testDescriptionProvide($class)
    {
        $this->resource->getPayload()->willReturn($class);

        $this->generator->generate(Argument::cetera())->will(function ($args) {
            return '/'.$args[0];
        });

        $description = new Description($this->resource->reveal());
        $enhancer = new SonataEnhancer($this->pool, $this->generator->reveal());
        $enhancer->enhance($description);

        $this->assertEquals('/std_class_edit', $description->get(Descriptor::LINK_EDIT_HTML));
        $this->assertEquals('/std_class_create', $description->get(Descriptor::LINK_CREATE_HTML));
        $this->assertEquals('/std_class_show', $description->get(Descriptor::LINK_SHOW_HTML));
        $this->assertEquals('/std_class_delete', $description->get(Descriptor::LINK_REMOVE_HTML));
    }

    public function provideDescriptionData(): array
    {
        return [
            [new \stdClass()],
            [new \LogicException()],
        ];
    }
}

class TestAdmin extends AbstractAdmin
{
    public function __toString()
    {
        return 'Standard Class';
    }

    protected function generateBaseRouteName(bool $isChildAdmin = false): string
    {
        return 'std_class';
    }

    protected function generateBaseRoutePattern(bool $isChildAdmin = false): string
    {
        return 'Standard Class';
    }
}
