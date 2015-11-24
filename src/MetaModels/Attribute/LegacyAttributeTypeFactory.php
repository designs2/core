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

namespace MetaModels\Attribute;

/**
 * This is the factory interface to query instances of attributes.
 * Usually this is only used internally from within the MetaModel class.
 *
 * @deprecated This class is part of the backwards compatible layer.
 */
class LegacyAttributeTypeFactory extends AbstractAttributeTypeFactory
{
    /**
     * The name of the legacy factory that is capable of creating attributes of this type.
     *
     * @var string
     */
    protected $factoryName;

    /**
     * {@inheritdoc}
     */
    public function createInstance($information, $metaModel)
    {
        // Check if we know the a factory.
        if ($this->factoryName) {
            if (is_object($this->factoryName)) {
                return call_user_func_array(array($this->factoryName, 'createInstance'), array($information));
            } elseif (class_exists($this->factoryName)) {
                $class  = new \ReflectionClass($this->factoryName);
                $method = $class->getMethod('createInstance');
                if ($method) {
                    if ($method->isStatic()) {
                        return call_user_func_array(array($this->factoryName, 'createInstance'), array($information));
                    }

                    /** @var LegacyAttributeTypeFactory $instance */
                    $instance = new $this->factoryName();
                    return $instance->createInstance($information, $metaModel);
                }
            }
            return null;
        }

        // Fallback to class name.
        return parent::createInstance($information, $metaModel);
    }

    /**
     * Add an attribute type factory for all registered legacy types to the passed factory.
     *
     * @param string $typeName             The name of the type.
     *
     * @param array  $attributeInformation The attribute type information (keys: "class", "factory", "icon").
     *
     * @return LegacyAttributeTypeFactory
     *
     * @throws \RuntimeException For types that have neither a class nor a factory defined.
     */
    public static function createLegacyFactory($typeName, $attributeInformation)
    {
        $typeFactory           = new static();
        $typeFactory->typeName = $typeName;
        $typeFactory->typeIcon = isset($attributeInformation['image'])
            ? $attributeInformation['image']
            : 'system/modules/metamodels/assets/images/icons/fields.png';

        if (isset($attributeInformation['class'])) {
            $typeFactory->typeClass = $attributeInformation['class'];
        }
        if (isset($attributeInformation['factory'])) {
            $typeFactory->factoryName = $attributeInformation['factory'];
        }
        if (!($typeFactory->typeClass || $typeFactory->factoryName)) {
            throw new \RuntimeException(
                'Attribute type ' .
                $typeName .
                ' has neither a handler class nor a factory defined.'
            );
        }

        return $typeFactory;
    }
}
