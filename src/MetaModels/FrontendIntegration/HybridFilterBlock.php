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
 * @copyright  The MetaModels team.
 * @license    LGPL.
 * @filesource
 */

namespace MetaModels\FrontendIntegration;

use MetaModels\Filter\Setting\ICollection;

/**
 * FE-module for FE-filtering.
 *
 * @package    MetaModels
 * @subpackage FrontendFilter
 * @author     Christian de la Haye <service@delahaye.de>
 *
 * @property \FrontendTemplate $Template
 */
class HybridFilterBlock extends MetaModelHybrid
{
    /**
     * Template.
     *
     * @var string
     */
    protected $strTemplate = 'mm_filter_default';

    /**
     * The jumpTo page.
     *
     * @var array
     */
    private $arrJumpTo;

    /**
     * Get the jump to page data.
     *
     * @return array
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     * @SuppressWarnings(PHPMD.CamelCaseVariableName)
     */
    public function getJumpTo()
    {
        if (!isset($this->arrJumpTo)) {
            /** @var \Database\Result $page */
            $page = $GLOBALS['objPage'];
            $this->setJumpTo($page->row());

            if ($this->metamodel_jumpTo) {
                // Page to jump to when filter submit.
                $objPage = $this
                    ->getServiceContainer()
                    ->getDatabase()
                    ->prepare('SELECT id, alias FROM tl_page WHERE id=?')
                    ->limit(1)
                    ->execute($this->metamodel_jumpTo);

                if ($objPage->numRows) {
                    $this->setJumpTo($objPage->row());
                }
            }
        }

        return $this->arrJumpTo;
    }

    /**
     * Set the jump to page data.
     *
     * @param array $arrJumpTo The page data.
     *
     * @return HybridFilterBlock
     */
    public function setJumpTo($arrJumpTo)
    {
        $this->arrJumpTo = $arrJumpTo;

        return $this;
    }

    /**
     * Retrieve the filter collection.
     *
     * @return ICollection
     */
    public function getFilterCollection()
    {
        return $this
            ->getServiceContainer()
            ->getFilterFactory()
            ->createCollection($this->metamodel_filtering);
    }

    /**
     * Display a wildcard in the back end.
     *
     * @return string
     */
    public function generate()
    {
        // Get template if configured.
        if ($this->metamodel_fef_template) {
            $this->strTemplate = $this->metamodel_fef_template;
        }

        return parent::generate();
    }

    /**
     * Generate the module.
     *
     * @return void
     */
    protected function compile()
    {
        $objFilter = new FrontendFilter();
        $arrFilter = $objFilter->getMetaModelFrontendFilter($this);

        $this->Template->setData($arrFilter);
        $this->Template->submit = $arrFilter['submit'];
    }
}
