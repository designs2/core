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

/**
 * Fields
 */
$GLOBALS['TL_LANG']['tl_metamodel_rendersetting']['attr_id']         = array('Attribute', 'Attribute this setting relates to.');
$GLOBALS['TL_LANG']['tl_metamodel_rendersetting']['template']        = array('Custom template to use for generating', 'Select the template that shall be used for the selected attribute. Valid template files start with "mm_&lt;type&gt;" where the type name is put for &lt;type&gt;');
$GLOBALS['TL_LANG']['tl_metamodel_rendersetting']['additional_class'] = array('Custom CSS class', 'Enter any CSS classes that you want get added to the output of this attribute');

/**
 * Legends
 */
$GLOBALS['TL_LANG']['tl_metamodel_rendersetting']['title_legend']    = 'Type';
$GLOBALS['TL_LANG']['tl_metamodel_rendersetting']['advanced_legend'] = 'Advanced';

/**
 * Buttons
 */
$GLOBALS['TL_LANG']['tl_metamodel_rendersetting']['new']             = array('New', 'Create new setting');

$GLOBALS['TL_LANG']['tl_metamodel_rendersetting']['edit']            = array('Edit setting', 'Edit render setting ID %s');
$GLOBALS['TL_LANG']['tl_metamodel_rendersetting']['cut']             = array('Cut render setting definition', 'Cut render setting ID %s');
$GLOBALS['TL_LANG']['tl_metamodel_rendersetting']['copy']            = array('Copy render setting definition', 'Copy render setting ID %s');
$GLOBALS['TL_LANG']['tl_metamodel_rendersetting']['delete']          = array('Delete render setting', 'Delete render setting ID %s');
$GLOBALS['TL_LANG']['tl_metamodel_rendersetting']['show']            = array('Render setting details', 'Show details of render setting ID %s');
$GLOBALS['TL_LANG']['tl_metamodel_rendersetting']['addall']          = array('Add all', 'Add all attributes to render setting');

$GLOBALS['TL_LANG']['tl_metamodel_filtersetting']['row']             = '%s <strong>%s</strong> <em>[%s]</em>';

/**
 * Messages
 */

$GLOBALS['TL_LANG']['tl_metamodel_rendersetting']['addAll_willadd'] = 'Will add attribute %s to rendersetting.';
$GLOBALS['TL_LANG']['tl_metamodel_rendersetting']['addAll_alreadycontained'] = 'Attribute %s already in rendersetting.';
$GLOBALS['TL_LANG']['tl_metamodel_rendersetting']['addAll_addsuccess'] = 'Added attribute %s to rendersetting.';

