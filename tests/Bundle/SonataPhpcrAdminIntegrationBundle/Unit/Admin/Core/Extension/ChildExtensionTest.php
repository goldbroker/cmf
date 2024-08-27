<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2017 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Symfony\Cmf\Bundle\SonataPhpcrAdminIntegrationBundle\Unit\Admin\Core\Extension;

use Symfony\Cmf\Bundle\SonataPhpcrAdminIntegrationBundle\Admin\Core\Extension\ChildExtension;

class ChildExtensionTest extends \PHPUnit\Framework\TestCase
{
    public function testAlterNewInstance()
    {
        $parent = new \StdClass();

        $documentManager = $this->createMock(\Doctrine\ODM\PHPCR\DocumentManager::class);
        $documentManager->expects($this->any())->method('find')->willReturn($parent);
        $modelManager = $this->createTestProxy(\Sonata\DoctrinePHPCRAdminBundle\Model\ModelManager::class, [$documentManager]);

        $request = $this->createMock(\Symfony\Component\HttpFoundation\Request::class);
        $request->expects($this->any())
            ->method('get')
            ->will($this->returnValue('parent-id'))
        ;

        $admin = $this->createMock(\Sonata\AdminBundle\Admin\AdminInterface::class);
        $admin->expects($this->any())
            ->method('getModelManager')
            ->will($this->returnValue($modelManager))
        ;
        $admin->expects($this->any())
            ->method('hasRequest')
            ->will($this->returnValue(true))
        ;
        $admin->expects($this->any())
            ->method('getRequest')
            ->will($this->returnValue($request))
        ;

        $child = $this->createMock(\Symfony\Cmf\Bundle\CoreBundle\Model\ChildInterface::class);
        $child->expects($this->once())
            ->method('setParentObject')
            ->with($this->equalTo($parent));

        $extension = new ChildExtension();
        $extension->alterNewInstance($admin, $child);
    }
}
