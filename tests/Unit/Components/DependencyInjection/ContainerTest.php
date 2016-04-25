<?php
/**
 * Shopware 5
 * Copyright (c) shopware AG
 *
 * According to our dual licensing model, this program can be used either
 * under the terms of the GNU Affero General Public License, version 3,
 * or under a proprietary license.
 *
 * The texts of the GNU Affero General Public License with an additional
 * permission and of our proprietary license can be found at and
 * in the LICENSE file you have received along with this program.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * "Shopware" is a registered trademark of shopware AG.
 * The licensing of the program under the AGPLv3 does not imply a
 * trademark license. Therefore any rights, title and interest in
 * our trademarks remain entirely with us.
 */

namespace Components\DependencyInjection;

use Prophecy\Argument;
use Shopware\Components\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerInterface;


/**
 * @category  Shopware
 * @package   Components\DependencyInjection
 * @copyright Copyright (c) shopware AG (http://www.shopware.com)
 */
class ContainerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Container
     */
    private $container;

    protected function setUp()
    {
        $this->container = new ProjectServiceContainer();
        $service = $this->getMock(\Enlight_Event_EventManager::class);
        $this->container->set('events', $service);
    }

    public function testSet()
    {
        $object = new \stdClass();

        $this->container->set('someKey', $object);
        $this->assertSame($object, $this->container->get('someKey'));
        $this->assertSame($object, $this->container->get('somekey'));
    }

    public function testHas()
    {
        $this->assertTrue($this->container->has('bar'));
        $this->assertTrue($this->container->has('BAR'));
        $this->assertTrue($this->container->has('alias'));
        $this->assertTrue($this->container->has('ALIAS'));

        $this->assertFalse($this->container->has('some'));
    }

    /**
     * @expectedException \Exception
     */
    public function testGetOnNonExistentWithDefaultBehaviour()
    {
        $this->container->get('foo');
    }

    /**
     * @expectedException \Exception
     */
    public function testGetOnNonExistentWithExceptionBehaviour()
    {
        $this->container->get('foo', ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE);
    }

    public function testGetOnNonExistentWithNullBehaviour()
    {
        $this->assertSame(
            null,
            $this->container->get('foo', ContainerInterface::NULL_ON_INVALID_REFERENCE)
        );
    }


    public function testGetOnNonExistentWithIgnoreBehaviour()
    {
        $this->assertSame(
            null,
            $this->container->get('foo', ContainerInterface::IGNORE_ON_INVALID_REFERENCE)
        );
    }

    public function testEventsAreEmitedDuringServiceInitialisation()
    {
        $service = $this->prophesize(\Enlight_Event_EventManager::class);

        $service->notify('Enlight_Bootstrap_AfterRegisterResource_events', Argument::any())->shouldBeCalled();
        $service->notifyUntil('Enlight_Bootstrap_InitResource_bar', Argument::any())->shouldBeCalled();
        $service->notify('Enlight_Bootstrap_AfterInitResource_bar', Argument::any())->shouldBeCalled();

        $service = $service->reveal();
        $this->container->set('events', $service);

        $this->assertInstanceOf('stdClass', $this->container->get('Bar'));
    }

    public function testEventsAreEmitedDuringServiceInitialisationWhenUsingAlias()
    {
        $service = $this->prophesize(\Enlight_Event_EventManager::class);

        $service->notify('Enlight_Bootstrap_AfterRegisterResource_events', Argument::any())->shouldBeCalled();
        $service->notifyUntil('Enlight_Bootstrap_InitResource_bar', Argument::any())->shouldBeCalled();
        $service->notify('Enlight_Bootstrap_AfterInitResource_bar', Argument::any())->shouldBeCalled();

        $service = $service->reveal();
        $this->container->set('events', $service);

        $this->assertInstanceOf('stdClass', $this->container->get('alias'));
    }

    /**
     * @expectedException \Exception
     */
    public function testEventsAreEmitedDuringServiceInitialisationWhenUsingUnknownServices()
    {
        $service = $this->prophesize(\Enlight_Event_EventManager::class);

        $service->notify('Enlight_Bootstrap_AfterRegisterResource_events', Argument::any())->shouldBeCalled();
        $service->notifyUntil('Enlight_Bootstrap_InitResource_foo', Argument::any())->shouldBeCalled();
        $service->notify('Enlight_Bootstrap_AfterInitResource_foo', Argument::any())->shouldBeCalled();

        $service = $service->reveal();
        $this->container->set('events', $service);

        $this->assertInstanceOf('stdClass', $this->container->get('Foo'));
    }
}

class ProjectServiceContainer extends Container
{
    public $__bar;

    public function __construct()
    {
        parent::__construct();

        $this->__bar = new \stdClass();
        $this->aliases = array('alias' => 'bar');
    }

    protected function getBarService()
    {
        return $this->__bar;
    }
}