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
 * @author     Tim Becker <please.tim@metamodel.me>
 * @copyright  The MetaModels team.
 * @license    LGPL.
 * @filesource
 */

$GLOBALS['TL_DCA']['tl_metamodel_searchable_pages'] = array
(
    'config'                          => array
    (
        'dataContainer'               => 'General',
        'ptable' => 'tl_metamodel',
        'switchToEdit'                => false,
        'enableVersioning'            => false,
    ),
    'dca_config'                      => array
    (
        'data_provider'               => array
        (
            'parent'                  => array
            (
                'source'              => 'tl_metamodel'
            )
        ),
        'childCondition'              => array
        (
            array
            (
                'from'                => 'tl_metamodel',
                'to'                  => 'tl_metamodel_searchable_pages',
                'setOn'               => array
                (
                    array
                    (
                        'to_field'    => 'pid',
                        'from_field'  => 'id',
                    ),
                ),
                'filter'              => array
                (
                    array
                    (
                        'local'       => 'pid',
                        'remote'      => 'id',
                        'operation'   => '=',
                    ),
                ),
                'inverse' => array
                (
                    array
                    (
                        'local' => 'pid',
                        'remote' => 'id',
                        'operation' => '=',
                    ),
                )
            )
        ),
    ),
    'list'                  => array
    (
        'sorting'           => array
        (
            'mode'         => 4,
            'fields'       => array('name'),
            'panelLayout'  => 'filter,limit',
            'headerFields' => array('name'),
            'flag'         => 1,
        ),
        'label'             => array
        (
            'fields' => array('name'),
            'format' => '%s',
        ),
        'global_operations' => array
        (
            'all' => array
            (
                'label'      => &$GLOBALS['TL_LANG']['MSC']['all'],
                'href'       => 'act=select',
                'class'      => 'header_edit_all',
                'attributes' => 'onclick="Backend.getScrollOffset();"'
            ),
        ),
        'operations'        => array
        (
            'edit'     => array
            (
                'label' => &$GLOBALS['TL_LANG']['tl_metamodel_searchable_pages']['edit'],
                'href'  => 'act=edit',
                'icon'  => 'edit.gif',
            ),
            'copy'     => array
            (
                'label' => &$GLOBALS['TL_LANG']['tl_metamodel_searchable_pages']['copy'],
                'href'  => 'act=copy',
                'icon'  => 'copy.gif',
            ),
            'delete'   => array
            (
                'label'      => &$GLOBALS['TL_LANG']['tl_metamodel_searchable_pages']['delete'],
                'href'       => 'act=delete',
                'icon'       => 'delete.gif',
                'attributes' => sprintf(
                    'onclick="if (!confirm(\'%s\')) return false; Backend.getScrollOffset();"',
                    $GLOBALS['TL_LANG']['MSC']['deleteConfirm']
                )
            ),
            'show'     => array
            (
                'label' => &$GLOBALS['TL_LANG']['tl_metamodel_searchable_pages']['show'],
                'href'  => 'act=show',
                'icon'  => 'show.gif'
            )
        )
    ),
    'metapalettes'          => array
    (
        'default' => array
        (
            'title'   => array
            (
                'name',
            ),
            'general'    => array
            (
                'filter',
                'filterparams',
                'rendersetting',
            ),
        )
    ),
    'fields'                => array
    (
        'name'            => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_metamodel_searchable_pages']['name'],
            'exclude'   => true,
            'search'    => true,
            'inputType' => 'text',
            'eval'      => array
            (
                'mandatory' => true,
                'maxlength' => 255,
                'tl_class'  => 'w50'
            )
        ),

        'filter'        => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_metamodel_searchable_pages']['filter'],
            'exclude'   => true,
            'inputType' => 'select',
            'eval'      => array
            (
                'includeBlankOption' => true,
                'chosen'             => true,
                'submitOnChange'     => true
            )
        ),

        'filterparams'        => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_metamodel_searchable_pages']['filterparams'],
            'exclude'   => true,
            'inputType' => 'mm_subdca',
            'eval'      => array
            (
                'tl_class'   => 'clr m12',
                'flagfields' => array
                (
                    'use_get' => array
                    (
                        'label'     => &$GLOBALS['TL_LANG']['tl_metamodel_searchable_pages']['filterparams'],
                        'inputType' => 'checkbox'
                    ),
                ),
            )
        ),

        'rendersetting'        => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_metamodel_searchable_pages']['rendersetting'],
            'exclude'   => true,
            'inputType' => 'select',
            'eval'      => array
            (
                'includeBlankOption' => true,
                'mandatory'          => true,
                'chosen'             => true
            )
        ),
    )
);
