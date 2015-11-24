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

namespace MetaModels\DcGeneral\Data;

use ContaoCommunityAlliance\DcGeneral\Data\ConfigInterface;
use MetaModels\Attribute\IAttribute;
use MetaModels\Filter\IFilter;
use MetaModels\Filter\Rules\Comparing\GreaterThan;
use MetaModels\Filter\Rules\Comparing\LessThan;
use MetaModels\Filter\Rules\Condition\ConditionAnd;
use MetaModels\Filter\Rules\Condition\ConditionOr;
use MetaModels\Filter\Rules\SearchAttribute;
use MetaModels\Filter\Rules\SimpleQuery;
use MetaModels\IMetaModel;
use MetaModels\IMetaModelsServiceContainer;

/**
 * Class to generate a MetaModels filter from a data configuration.
 */
class FilterBuilder
{
    /**
     * The MetaModel instance.
     *
     * @var IMetaModel
     */
    protected $metaModel;

    /**
     * The configuration.
     *
     * @var ConfigInterface
     */
    protected $configuration;

    /**
     * Generate a filter from a passed configuration.
     *
     * @param IMetaModel      $metaModel     The MetaModel.
     *
     * @param ConfigInterface $configuration The data configuration.
     */
    public function __construct(IMetaModel $metaModel, ConfigInterface $configuration)
    {
        $this->metaModel     = $metaModel;
        $this->configuration = $configuration;
    }

    /**
     * Retrieve the MetaModel.
     *
     * @return IMetaModel
     */
    protected function getMetaModel()
    {
        return $this->metaModel;
    }

    /**
     * Retrieve the service container.
     *
     * @return IMetaModelsServiceContainer
     */
    protected function getServiceContainer()
    {
        return $this->getMetaModel()->getServiceContainer();
    }

    /**
     * Retrieve the Database.
     *
     * @return \Contao\Database
     */
    protected function getDatabase()
    {
        return $this->getServiceContainer()->getDatabase();
    }

    /**
     * Build the sub query for a comparing operator like =,<,>.
     *
     * @param IAttribute $attribute The attribute.
     *
     * @param IFilter    $filter    The filter to add the operations to.
     *
     * @param array      $operation The operation to convert.
     *
     * @return void
     *
     * @throws \RuntimeException When the operation can not be parsed.
     */
    private function getFilterForComparingOperator($attribute, IFilter $filter, $operation)
    {
        if ($attribute) {
            switch ($operation['operation']) {
                case '=':
                    $filter->addFilterRule(new SearchAttribute(
                        $attribute,
                        $operation['value'],
                        $this->getMetaModel()->getAvailableLanguages() ?: array()
                    ));
                    return;

                case '>':
                    $filter->addFilterRule(new GreaterThan(
                        $attribute,
                        $operation['value']
                    ));
                    return;

                case '<':
                    $filter->addFilterRule(new LessThan(
                        $attribute,
                        $operation['value']
                    ));
                    return;

                default:
                    throw new \RuntimeException(
                        'Error processing filter array - unknown operation ' .
                        var_export($operation['operation'], true),
                        1
                    );
            }
        }

        if ($this->getDatabase()->fieldExists($operation['property'], $this->getMetaModel()->getTableName())) {
            // System column?
            $filter->addFilterRule(new SimpleQuery(
                sprintf(
                    'SELECT id FROM %s WHERE %s %s?',
                    $this->getMetaModel()->getTableName(),
                    $operation['property'],
                    $operation['operation']
                ),
                array($operation['value']),
                'id',
                $this->getDatabase()
            ));

            return;
        }

        throw new \RuntimeException(
            'Error processing filter array - unknown property ' . var_export($operation['property'], true),
            1
        );
    }

    /**
     * Return the filter query for a "foo IN ('a', 'b')" filter.
     *
     * @param IFilter $filter    The filter to add the operations to.
     *
     * @param array   $operation The operation to convert.
     *
     * @return void
     *
     * @throws \RuntimeException When the operation can not be parsed.
     */
    private function getFilterForInList(IFilter $filter, $operation)
    {
        // Rewrite the IN operation to a rephrased term: "(x=a) OR (x=b) OR ...".
        $subRules = array();
        foreach ($operation['values'] as $varValue) {
            $subRules[] = array(
                'property'  => $operation['property'],
                'operation' => '=',
                'value'     => $varValue
            );
        }
        $this->calculateSubfilter(array(
            'operation' => 'OR',
            'children'    => $subRules
        ), $filter);
    }

    /**
     * Return the filter query for a "foo LIKE '%ba_r%'" filter.
     *
     * The searched value may contain the wildcards '*' and '?' which will get converted to proper SQL.
     *
     * @param IAttribute $attribute The attribute.
     *
     * @param IFilter    $filter    The filter to add the operations to.
     *
     * @param array      $operation The operation to convert.
     *
     * @return void
     *
     * @throws \RuntimeException When the operation can not be parsed.
     */
    private function getFilterForLike($attribute, IFilter $filter, $operation)
    {
        if ($attribute) {
            $filter->addFilterRule(new SearchAttribute(
                $attribute,
                $operation['value'],
                $this->getMetaModel()->getAvailableLanguages() ?: array()
            ));

            return;
        }

        if ($this->getDatabase()->fieldExists($operation['property'], $this->getMetaModel()->getTableName())) {
            // System column?
            $filter->addFilterRule(new SimpleQuery(
                sprintf(
                    'SELECT id FROM %s WHERE %s LIKE ?',
                    $this->getMetaModel()->getTableName(),
                    $operation['property']
                ),
                array($operation['value']),
                'id',
                $this->getDatabase()
            ));

            return;
        }

        throw new \RuntimeException(
            'Error processing filter array - unknown property ' . var_export($operation['property'], true),
            1
        );
    }

    /**
     * Calculate a native SQL sub procedure.
     *
     * @param FilterBuilderSql $procedure The procedure to which to append to.
     *
     * @param array            $children  The children to calculate.
     *
     * @return array
     */
    protected function buildNativeSqlProcedure(FilterBuilderSql $procedure, $children)
    {
        $skipped   = array();
        $metaModel = $this->getMetaModel();
        $tableName = $metaModel->getTableName();
        foreach ($children as $child) {
            // If there is an attribute contained within this rule, skip it.
            if (isset($child['property']) && $metaModel->hasAttribute($child['property'])) {
                $skipped[] = $child;

                continue;
            }

            // Try to parse the sub procedure and extract as much as possible.
            if (($child['operation'] == 'AND') || ($child['operation'] == 'OR')) {
                $subProcedure = new FilterBuilderSql($tableName, $child['operation'], $this->getDatabase());
                $subSkipped   = $this->buildNativeSqlProcedure($subProcedure, $child['children']);

                if (count($subSkipped) !== count($child['operation'])) {
                    $procedure->addSubProcedure($subProcedure);
                }

                if (!empty($subSkipped)) {
                    $skipped += $subSkipped;
                }

                continue;
            }

            $procedure->addChild($child);
        }

        return $skipped;

    }

    /**
     * Method to optimize as many system column lookup filters as possible into a combined filter rule.
     *
     * @param ConditionAnd|ConditionOr $filterRule The filter to which the optimized rule shall be added to.
     *
     * @param array                    $children   The children to parse.
     *
     * @param string                   $operation  The operation to parse (AND or OR).
     *
     * @return array
     */
    protected function optimizedFilter($filterRule, $children, $operation)
    {
        $procedure = new FilterBuilderSql($this->getMetaModel()->getTableName(), $operation, $this->getDatabase());
        $skipped   = $this->buildNativeSqlProcedure($procedure, $children);

        if (!$procedure->isEmpty()) {
            $filterRule->addChild($this->getMetaModel()->getEmptyFilter()->addFilterRule($procedure->build()));
        }

        return $skipped;
    }

    /**
     * Build an AND or OR query.
     *
     * @param IFilter $filter    The filter to add the operations to.
     *
     * @param array   $operation The operation to convert.
     *
     * @return void
     */
    protected function getAndOrFilter(IFilter $filter, $operation)
    {
        if (!$operation['children']) {
            return;
        }

        if ($operation['operation'] == 'AND') {
            $filterRule = new ConditionAnd();
        } else {
            $filterRule = new ConditionOr();
        }
        $filter->addFilterRule($filterRule);

        $children = $this->optimizedFilter($filterRule, $operation['children'], $operation['operation']);

        foreach ($children as $child) {
            $subFilter = $this->getMetaModel()->getEmptyFilter();
            $filterRule->addChild($subFilter);
            $this->calculateSubfilter($child, $subFilter);
        }
    }

    /**
     * Retrieve the attribute for a filter operation.
     *
     * @param array $operation The operation to retrieve the attribute for.
     *
     * @return IAttribute
     *
     * @throws \InvalidArgumentException When the attribute can not be retrieved.
     */
    protected function getAttributeFromFilterOperation($operation)
    {
        $attribute = null;
        if (!empty($operation['property'])) {
            $attribute = $this->getMetaModel()->getAttribute($operation['property']);
        }

        if ($attribute === null) {
            throw new \InvalidArgumentException('Attribute ' . $operation['property'] . ' not found.');
        }

        return $attribute;
    }

    /**
     * Combine a filter in standard filter array notation.
     *
     * Supported operations are:
     * operation      needed arguments     argument type.
     * AND
     *                'children'           array
     * OR
     *                'children'           array
     * =
     *                'property'           string (the name of a property)
     *                'value'              literal
     * >
     *                'property'           string (the name of a property)
     *                'value'              literal
     * <
     *                'property'           string (the name of a property)
     *                'value'              literal
     * IN
     *                'property'           string (the name of a property)
     *                'values'             array of literal
     *
     * @param array   $operation The filter to be combined into the passed filter object.
     *
     * @param IFilter $filter    The filter object where the rules shall get appended to.
     *
     * @return void
     *
     * @throws \RuntimeException When an improper filter condition is encountered, an exception is thrown.
     */
    private function calculateSubfilter($operation, IFilter $filter)
    {
        if (!is_array($operation)) {
            throw new \RuntimeException('Error Processing subfilter: ' . var_export($operation, true), 1);
        }

        switch ($operation['operation']) {
            case 'AND':
            case 'OR':
                $this->getAndOrFilter($filter, $operation);
                break;

            case '=':
            case '>':
            case '<':
                $this->getFilterForComparingOperator(
                    $this->getAttributeFromFilterOperation($operation),
                    $filter,
                    $operation
                );
                break;

            case 'IN':
                $this->getFilterForInList($filter, $operation);
                break;

            case 'LIKE':
                $this->getFilterForLike(
                    $this->getAttributeFromFilterOperation($operation),
                    $filter,
                    $operation
                );
                break;

            default:
                throw new \RuntimeException(
                    'Error processing filter array - unknown operation ' . var_export($operation, true),
                    1
                );
        }
    }

    /**
     * Prepare a filter and return it.
     *
     * @return IFilter
     */
    public function build()
    {
        $filter = $this->getMetaModel()->getEmptyFilter();

        if ($this->configuration->getFilter()) {
            $this->calculateSubfilter(
                array
                (
                    'operation' => 'AND',
                    'children' => $this->configuration->getFilter()
                ),
                $filter
            );
        }

        return $filter;
    }
}
