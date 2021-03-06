<?php
/**
 * The MetaModels extension allows the creation of multiple collections of custom items,
 * each with its own unique set of selectable attributes, with attribute extendability.
 * The Front-End modules allow you to build powerful listing and filtering of the
 * data in each collection.
 *
 * PHP version 5
 *
 * @package    MetaModels
 * @subpackage Core
 * @author     Christian Schiffler <c.schiffler@cyberspectrum.de>
 * @author     David Maack <david.maack@arcor.de>
 * @copyright  The MetaModels team.
 * @license    LGPL.
 * @filesource
 */

namespace MetaModels\Attribute;

/**
 * Interface for "complex" MetaModel attributes.
 * Complex attributes are attributes that can not be fetched with a simple:
 * "SELECT colName FROM mm_table" and therefore need to be handled differently.
 *
 * @package    MetaModels
 * @subpackage Interfaces
 * @author     Christian Schiffler <c.schiffler@cyberspectrum.de>
 */
// FIXME: Should be renamed to IComplexAttribute
interface IComplex extends IAttribute
{
    /**
     * This method is called to retrieve the data for certain items from the database.
     *
     * @param string[] $arrIds The ids of the items to retrieve.
     *
     * @return mixed[] The nature of the resulting array is a mapping from id => "native data" where
     *                 the definition of "native data" is only of relevance to the given item.
     */
    public function getDataFor($arrIds);

    /**
     * Remove values for items.
     *
     * @param string[] $arrIds The ids of the items to retrieve.
     *
     * @return void
     */
    public function unsetDataFor($arrIds);
}
