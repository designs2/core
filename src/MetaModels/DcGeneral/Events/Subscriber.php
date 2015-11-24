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
 * @author     Stefan Heimes <stefan_heimes@hotmail.com>
 * @author     Christopher Boelter <c.boelter@cogizz.de>
 * @copyright  2012-2015 The MetaModels team.
 * @license    https://github.com/MetaModels/core/blob/master/LICENSE LGPL-3.0
 * @filesource
 */

namespace MetaModels\DcGeneral\Events;

use ContaoCommunityAlliance\DcGeneral\Contao\View\Contao2BackendView\Event\GetBreadcrumbEvent;
use ContaoCommunityAlliance\DcGeneral\Event\AbstractModelAwareEvent;
use ContaoCommunityAlliance\DcGeneral\Event\PostCreateModelEvent;
use ContaoCommunityAlliance\DcGeneral\Event\PostDeleteModelEvent;
use ContaoCommunityAlliance\DcGeneral\Event\PostDuplicateModelEvent;
use ContaoCommunityAlliance\DcGeneral\Event\PostPasteModelEvent;
use ContaoCommunityAlliance\DcGeneral\Event\PostPersistModelEvent;
use MetaModels\BackendIntegration\PurgeCache;
use MetaModels\DcGeneral\Events\BreadCrumb\BreadCrumbFilter;

/**
 * Central event subscriber implementation.
 */
class Subscriber extends BaseSubscriber
{
    /**
     * Register all listeners to handle creation of a data container.
     *
     * @return void
     */
    protected function registerEventsInDispatcher()
    {
        $this->registerTableMetaModelsEvents();
        $this->registerTableMetaModelAttributeEvents();
        $this->registerTableMetaModelDcaEvents();
        $this->registerTableMetaModelDcaCombineEvents();
        $this->registerTableMetaModelDcaSettingEvents();
        $this->registerTableMetaModelDcaSettingConditionsEvents();
        $this->registerTableMetaModelFilterEvents();
        $this->registerTableMetaModelFilterSettingEvents();
        $this->registerTableMetaModelRenderSettingEvents();
        $this->registerTableMetaModelRenderSettingsEvents();
        $this->registerTableMetaModelDcaSortGroupEvents();
        $this->registerTableMetaModelSearchablePagesEvents();

        $this->registerTableWatcher();
    }

    /**
     * Register the events for table tl_metamodel.
     *
     * @return void
     */
    private function registerTableMetaModelsEvents()
    {
        new \MetaModels\DcGeneral\Events\Table\MetaModels\Subscriber($this->getServiceContainer());
    }

    /**
     * Register the events for table tl_metamodel_attribute.
     *
     * @return void
     */
    private function registerTableMetaModelAttributeEvents()
    {
        new \MetaModels\DcGeneral\Events\Table\Attribute\Subscriber($this->getServiceContainer());
    }

    /**
     * Register the events for table tl_metamodel_dca.
     *
     * @return void
     */
    public function registerTableMetaModelDcaEvents()
    {
        new \MetaModels\DcGeneral\Events\Table\InputScreen\Subscriber($this->getServiceContainer());
    }

    /**
     * Register the events for table tl_metamodel_dca_sortgroup.
     *
     * @return void
     */
    public function registerTableMetaModelDcaSortGroupEvents()
    {
        new \MetaModels\DcGeneral\Events\Table\InputScreenSortGroup\Subscriber($this->getServiceContainer());
    }

    /**
     * Register the events for table tl_metamodel_dca_combine.
     *
     * @return void
     */
    public function registerTableMetaModelDcaCombineEvents()
    {
        new \MetaModels\DcGeneral\Events\Table\DcaCombine\Subscriber($this->getServiceContainer());
    }

    /**
     * Register the events for table tl_metamodel_dcasetting.
     *
     * @return void
     */
    public function registerTableMetaModelDcaSettingEvents()
    {
        new \MetaModels\DcGeneral\Events\Table\InputScreens\Subscriber($this->getServiceContainer());
    }

    /**
     * Register the events for table tl_metamodel_dcasetting_condition.
     *
     * @return void
     */
    public function registerTableMetaModelDcaSettingConditionsEvents()
    {
        new \MetaModels\DcGeneral\Events\Table\InputScreenCondition\Subscriber($this->getServiceContainer());
    }

    /**
     * Register the events for table tl_metamodel_dcasetting_condition.
     *
     * @return void
     */
    public function registerTableMetaModelSearchablePagesEvents()
    {
        new \MetaModels\DcGeneral\Events\Table\SearchablePages\Subscriber($this->getServiceContainer());
    }

    /**
     * Register the events for table tl_metamodel_filter.
     *
     * @return void
     */
    public function registerTableMetaModelFilterEvents()
    {
        $serviceContainer = $this->getServiceContainer();
        $this
            ->addListener(
                GetBreadcrumbEvent::NAME,
                function (GetBreadcrumbEvent $event) use ($serviceContainer) {
                    if (($event->getEnvironment()->getDataDefinition()->getName() !== 'tl_metamodel_filter')) {
                        return;
                    }
                    $subscriber = new BreadCrumbFilter($serviceContainer);
                    $subscriber->getBreadcrumb($event);
                }
            );
    }

    /**
     * Register the events for table tl_metamodel_filtersetting.
     *
     * @return void
     */
    public function registerTableMetaModelFilterSettingEvents()
    {
        new \MetaModels\DcGeneral\Events\Table\FilterSetting\Subscriber($this->getServiceContainer());
    }

    /**
     * Register the events for table tl_metamodel_rendersetting.
     *
     * @return void
     */
    public function registerTableMetaModelRenderSettingEvents()
    {
        new \MetaModels\DcGeneral\Events\Table\RenderSetting\Subscriber($this->getServiceContainer());
    }

    /**
     * Register the events for table tl_metamodel_rendersettings.
     *
     * @return void
     */
    public function registerTableMetaModelRenderSettingsEvents()
    {
        new \MetaModels\DcGeneral\Events\Table\RenderSettings\Subscriber($this->getServiceContainer());
    }

    /**
     * Check if we have to purge the MetaModels cache.
     *
     * @param AbstractModelAwareEvent $event The event holding the model being manipulated.
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function checkPurge(AbstractModelAwareEvent $event)
    {
        $table = $event->getModel()->getProviderName();
        if (($table == 'tl_metamodel') ||
            ($table == 'tl_metamodel_dca') ||
            ($table == 'tl_metamodel_dca_sortgroup') ||
            ($table == 'tl_metamodel_dcasetting') ||
            ($table == 'tl_metamodel_dcasetting_condition') ||
            ($table == 'tl_metamodel_attribute') ||
            ($table == 'tl_metamodel_filter') ||
            ($table == 'tl_metamodel_filtersetting') ||
            ($table == 'tl_metamodel_rendersettings') ||
            ($table == 'tl_metamodel_rendersetting') ||
            ($table == 'tl_metamodel_dca_combine')
        ) {
            $purger = new PurgeCache();
            $purger->purge();
        }
    }

    /**
     * Register event to clear the cache when a relevant data model has been saved.
     *
     * @return void
     */
    public function registerTableWatcher()
    {
        $this
            ->addListener(PostCreateModelEvent::NAME, array($this, 'checkPurge'))
            ->addListener(PostDeleteModelEvent::NAME, array($this, 'checkPurge'))
            ->addListener(PostDuplicateModelEvent::NAME, array($this, 'checkPurge'))
            ->addListener(PostPasteModelEvent::NAME, array($this, 'checkPurge'))
            ->addListener(PostPersistModelEvent::NAME, array($this, 'checkPurge'));
    }
}
