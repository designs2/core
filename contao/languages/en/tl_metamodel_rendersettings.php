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
 * Legends
 */
$GLOBALS['TL_LANG']['tl_metamodel_rendersettings']['title_legend']         = 'Name';
$GLOBALS['TL_LANG']['tl_metamodel_rendersettings']['general_legend']       = 'General settings';
$GLOBALS['TL_LANG']['tl_metamodel_rendersettings']['expert_legend']        = 'Expert settings';
$GLOBALS['TL_LANG']['tl_metamodel_rendersettings']['view_legend']          = 'View settings';

/**
 * Fields
 */
$GLOBALS['TL_LANG']['tl_metamodel_rendersettings']['name']                 = array('Name', 'Setting name.');
$GLOBALS['TL_LANG']['tl_metamodel_rendersettings']['tstamp']               = array('Revision date', 'Date and time of the latest revision.');
$GLOBALS['TL_LANG']['tl_metamodel_rendersettings']['isdefault']            = array('Is default', 'Determines that this setting shall be used as default for the parenting MetaModel.');
$GLOBALS['TL_LANG']['tl_metamodel_rendersettings']['template']             = array('Template', 'The template to use to render the items.');
$GLOBALS['TL_LANG']['tl_metamodel_rendersettings']['format']               = array('Output format', 'Define the output format. Leave empty to use the format used by current page.');
$GLOBALS['TL_LANG']['tl_metamodel_rendersettings']['formatOptions']        = array('html5' => 'HTML5', 'xhtml' => 'XHTML', 'text' => 'Text');
$GLOBALS['TL_LANG']['tl_metamodel_rendersettings']['jumpTo']               = array('JumpTo page', 'The page that shall be used as "show details" urls. Note that the detailed URL params will get generated by the filter setting that is currently in use.');
$GLOBALS['TL_LANG']['tl_metamodel_rendersettings']['jumpTo_allLanguages']  = 'All languages';
$GLOBALS['TL_LANG']['tl_metamodel_rendersettings']['jumpTo_language']      = array('Language', 'The language for the jump to page.');
$GLOBALS['TL_LANG']['tl_metamodel_rendersettings']['jumpTo_page']          = array('Jump to page', 'The page to use for detail links.');
$GLOBALS['TL_LANG']['tl_metamodel_rendersettings']['jumpTo_filter']        = array('Filter settings', 'The filter settings that define how the target (the reader/lister on the detail page) will identify it\'s matches.');
$GLOBALS['TL_LANG']['tl_metamodel_rendersettings']['hideEmptyValues']      = array('Hide empty values', 'Hide empty values in backend and frontend.');
$GLOBALS['TL_LANG']['tl_metamodel_rendersettings']['hideLabels']           = array('Hide labels', 'Hide all labels in backend and frontend.');
$GLOBALS['TL_LANG']['tl_metamodel_rendersettings']['additionalCss']        = array('Additional css files', 'Choose this, if you want to include additional css files.');
$GLOBALS['TL_LANG']['tl_metamodel_rendersettings']['additionalJs']         = array('Additional javascript files', 'Choose this, if you want to include additional javascript files.');

$GLOBALS['TL_LANG']['tl_metamodel_rendersettings']['file']                 = 'File';
$GLOBALS['TL_LANG']['tl_metamodel_rendersettings']['publish']              = 'Publish';

/**
 * Buttons
 */
$GLOBALS['TL_LANG']['tl_metamodel_rendersettings']['new']                  = array('New', 'Create new setting');
$GLOBALS['TL_LANG']['tl_metamodel_rendersettings']['edit']                 = array('Edit setting', 'Edit setting ID %s');
$GLOBALS['TL_LANG']['tl_metamodel_rendersettings']['copy']                 = array('Copy setting definition', 'Copy setting ID %s');
$GLOBALS['TL_LANG']['tl_metamodel_rendersettings']['delete']               = array('Delete setting', 'Delete setting ID %s');
$GLOBALS['TL_LANG']['tl_metamodel_rendersettings']['show']                 = array('Filter details', 'Show details of setting ID %s');
$GLOBALS['TL_LANG']['tl_metamodel_rendersettings']['settings']             = array('Define attribute settings', 'Define attribute settings for setting ID %s');

// Error message content.
$GLOBALS['TL_LANG']['tl_metamodel_rendersettings']['error_unknown_id']        = 'unknown ID: %s';
$GLOBALS['TL_LANG']['tl_metamodel_rendersettings']['error_unknown_attribute'] = 'unknown attribute';
$GLOBALS['TL_LANG']['tl_metamodel_rendersettings']['error_unknown_column']    = 'unknown column';
