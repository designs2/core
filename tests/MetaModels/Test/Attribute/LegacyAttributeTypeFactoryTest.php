<?php

/**
 * This file is part of MetaModels/core.
 *
 * (c) 2012-2015 The MetaModels team.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * This project is provided in good faith and hope to be usable by anyone.
 *
 * @package    MetaModels
 * @subpackage Core
 * @author     Christian Schiffler <c.schiffler@cyberspectrum.de>
 * @copyright  2012-2015 The MetaModels team.
 * @license    https://github.com/MetaModels/core/blob/master/LICENSE LGPL-3.0
 * @filesource
 */

namespace MetaModels\Test\Attribute;

use MetaModels\Attribute\Events\LegacyListener;
use MetaModels\Attribute\AttributeFactory;
use MetaModels\Attribute\IAttributeTypeFactory;
use MetaModels\IMetaModelsServiceContainer;
use MetaModels\MetaModelsEvents;
use MetaModels\Test\Attribute\Mock\AttributeFactoryMocker;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Test the attribute factory.
 */
class LegacyAttributeTypeFactoryTest extends AttributeTypeFactoryTest
{
    /**
     * The classes to test.
     *
     * @var array
     */
    protected $testFactories = array();

    /**
     * Sets up the fixture, creates the classes to test.
     *
     * This method is called before a test is executed.
     *
     * @return void
     */
    public function setUp()
    {
        if (version_compare(PHP_VERSION, '5.4', '<')) {
            $this->markTestSkipped('Need at least PHP 5.4 for this test.');
        }

        $reflection    = new \ReflectionClass('MetaModels\Attribute\LegacyAttributeTypeFactory');
        $typeNameProp  = $reflection->getProperty('typeName');
        $typeClassProp = $reflection->getProperty('typeClass');
        $typeIconProp  = $reflection->getProperty('typeIcon');
        $typeNameProp->setAccessible(true);
        $typeClassProp->setAccessible(true);
        $typeIconProp->setAccessible(true);

        foreach (array
        (
         'test_translated' => 'MetaModels\Attribute\ITranslated',
         'test_simple'     => 'MetaModels\Attribute\ISimple',
         'test_complex'    => 'MetaModels\Attribute\IComplex',

        ) as $typeName => $typeInfo) {

            $mockAttributeClass = $this->getMock($typeInfo);

            $instance = $reflection->newInstanceWithoutConstructor();
            $typeNameProp->setValue($instance, $typeName);
            $typeClassProp->setValue($instance, get_class($mockAttributeClass));
            $typeIconProp->setValue($instance, $typeName . '.png');

            $this->testFactories[$typeName] = $instance;
        }
    }

    /**
     * Override the method to run the tests on the attribute factories to be tested.
     *
     * @return IAttributeTypeFactory[]
     */
    protected function getAttributeFactories()
    {
        return $this->testFactories;
    }

    /**
     * Test the addFactoriesToFactory method.
     *
     * @return void
     */
    public function testAddFactoriesToFactoryNone()
    {
        $factory = new AttributeFactory();

        $this->assertEquals(array(), $factory->getTypeNames());
    }

    /**
     * Test the addFactoriesToFactory method.
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     * @SuppressWarnings(PHPMD.CamelCaseVariableName)
     */
    public function testAddFactoriesToFactory()
    {
        $reflection    = new \ReflectionClass('MetaModels\Attribute\LegacyAttributeTypeFactory');
        $typeClassProp = $reflection->getProperty('typeClass');
        $typeClassProp->setAccessible(true);

        $factoryReflection = new \ReflectionClass('MetaModels\Attribute\IFactory');
        $methods           = array_map(
            function ($method) {
                /** @var \ReflectionMethod $method */
                return $method->getName();
            },
            $factoryReflection->getMethods(\ReflectionMethod::IS_ABSTRACT)
        );
        $methods[] = 'createInstance';

        foreach ($this->testFactories as $typeName => $instance) {
            $className     = $typeClassProp->getValue($instance);
            $mockedFactory = $this
                ->getMock(
                    'MetaModels\Attribute\IFactory',
                    $methods
                );
            $mockedFactory
                ->expects($this->any())
                ->method('createInstance')
                ->with(array('type' => $typeName), null)
                ->will($this->returnValue(new $className()));

            $GLOBALS['METAMODELS']['attributes'][$typeName]['factory'] = get_class($mockedFactory);
            $GLOBALS['METAMODELS']['attributes'][$typeName]['class']   = $typeClassProp->getValue($instance);
        }

        $factory = new AttributeFactory();
        $factory->setServiceContainer($this->mockServiceContainer());

        $this->assertEquals(array_keys($this->testFactories), $factory->getTypeNames());

        foreach ($this->testFactories as $typeName => $instance) {
            $this->assertSame(
                $typeClassProp->getValue($instance),
                $typeClassProp->getValue($factory->getTypeFactory($typeName))
            );
            // Sadly this can not be tested, the mocked factory looses it's $__phpunit_invocationMocker. :(
            /*
            $this->assertInstanceOf(
                $typeClassProp->getValue($instance),
                $factory->createAttribute(array('type' => $typeName), null)
            );
            */

        }

        unset($GLOBALS['METAMODELS']);
    }

    /**
     * Test the addFactoriesToFactory method.
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     * @SuppressWarnings(PHPMD.CamelCaseVariableName)
     */
    public function testAddClassesToFactory()
    {
        $reflection    = new \ReflectionClass('MetaModels\Attribute\LegacyAttributeTypeFactory');
        $typeClassProp = $reflection->getProperty('typeClass');
        $typeClassProp->setAccessible(true);

        foreach ($this->testFactories as $typeName => $instance) {
            $GLOBALS['METAMODELS']['attributes'][$typeName]['class'] = $typeClassProp->getValue($instance);
        }

        $factory = new AttributeFactory();
        $factory->setServiceContainer($this->mockServiceContainer());

        $this->assertEquals(array_keys($this->testFactories), $factory->getTypeNames());

        foreach ($this->testFactories as $typeName => $instance) {
            $this->assertSame(
                $typeClassProp->getValue($instance),
                $typeClassProp->getValue($factory->getTypeFactory($typeName))
            );

            $this->assertInstanceOf(
                $typeClassProp->getValue($instance),
                $factory->createAttribute(array('type' => $typeName), null)
            );
        }

        unset($GLOBALS['METAMODELS']);
    }

    /**
     * Test the addFactoriesToFactory method.
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     * @SuppressWarnings(PHPMD.CamelCaseVariableName)
     */
    public function testGetTypeIconsFromFactory()
    {
        $reflection    = new \ReflectionClass('MetaModels\Attribute\LegacyAttributeTypeFactory');
        $typeClassProp = $reflection->getProperty('typeClass');
        $typeIconProp  = $reflection->getProperty('typeIcon');
        $typeClassProp->setAccessible(true);
        $typeIconProp->setAccessible(true);

        foreach ($this->testFactories as $typeName => $instance) {
            $GLOBALS['METAMODELS']['attributes'][$typeName]['class'] = $typeClassProp->getValue($instance);
            $GLOBALS['METAMODELS']['attributes'][$typeName]['image'] = $typeIconProp->getValue($instance);
        }

        $factory = new AttributeFactory();
        $factory->setServiceContainer($this->mockServiceContainer());

        $this->assertEquals(array_keys($this->testFactories), $factory->getTypeNames());

        foreach ($this->testFactories as $typeName => $instance) {
            $this->assertEquals(
                $typeIconProp->getValue($instance),
                $factory->getIconForType($typeName)
            );
        }

        unset($GLOBALS['METAMODELS']);
    }

    /**
     * Mock a service container.
     *
     * @return IMetaModelsServiceContainer
     */
    protected function mockServiceContainer()
    {
        $serviceContainer = $this->getMock('MetaModels\IMetaModelsServiceContainer');

        $serviceContainer
            ->expects($this->any())
            ->method('getEventDispatcher')
            ->will($this->returnValue($this->mockEventDispatcher()));

        return $serviceContainer;
    }

    /**
     * Mock an event dispatcher.
     *
     * @param string $expectedEvent The name of the expected event.
     *
     * @param int    $expectedCount The amount how often this event shall get dispatched.
     *
     * @return EventDispatcherInterface
     */
    protected function mockEventDispatcher($expectedEvent = '', $expectedCount = 0)
    {
        $eventDispatcher = $this->getMock(
            'Symfony\Component\EventDispatcher\EventDispatcher',
            null
        );

        if ($expectedEvent) {
            $eventDispatcher
                ->expects($this->exactly($expectedCount))
                ->method('dispatch')
                ->with($this->equalTo($expectedEvent));
        }
        /** @var EventDispatcherInterface $eventDispatcher */
        $eventDispatcher->addListener(
            MetaModelsEvents::ATTRIBUTE_FACTORY_CREATE,
            array(new LegacyListener(), 'registerLegacyAttributeFactoryEvents')
        );

        return $eventDispatcher;
    }

    /**
     * Mock an attribute type factory.
     *
     * @param string $typeName   The type name to mock.
     *
     * @param bool   $translated Flag if the type shall be translated.
     *
     * @param bool   $simple     Flag if the type shall be simple.
     *
     * @param bool   $complex    Flag if the type shall be complex.
     *
     * @param string $class      Name of the class to instantiate when createInstance() is called.
     *
     * @return IAttributeTypeFactory
     */
    protected function mockAttributeFactory($typeName, $translated, $simple, $complex, $class = 'stdClass')
    {
        return AttributeFactoryMocker::mockAttributeFactory($this, $typeName, $translated, $simple, $complex, $class);
    }
}
