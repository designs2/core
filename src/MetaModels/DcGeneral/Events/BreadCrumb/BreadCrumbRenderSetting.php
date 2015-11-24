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
 * @copyright  2012-2015 The MetaModels team.
 * @license    https://github.com/MetaModels/core/blob/master/LICENSE LGPL-3.0
 * @filesource
 */

namespace MetaModels\DcGeneral\Events\BreadCrumb;

use ContaoCommunityAlliance\DcGeneral\EnvironmentInterface;

/**
 * Generate a breadcrumb for table tl_metamodel_rendersettings.
 */
class BreadCrumbRenderSetting extends BreadCrumbRenderSettings
{
    /**
     * Id of the render setting.
     *
     * @var int
     */
    protected $renderSettingId;

    /**
     * Retrieve the render setting.
     *
     * @return object
     */
    protected function getRenderSettingItem()
    {
        return (object) $this
            ->getServiceContainer()
            ->getDatabase()
            ->prepare('SELECT * FROM tl_metamodel_rendersetting WHERE id=?')
            ->execute($this->renderSettingId)
            ->row();
    }

    /**
     * {@inheritDoc}
     */
    public function getBreadcrumbElements(EnvironmentInterface $environment, $elements)
    {
        if (!isset($this->renderSettingsId)) {
            if (!isset($this->renderSettingId)) {
                $this->renderSettingsId = $this->extractIdFrom($environment, 'pid');
            } else {
                $this->renderSettingsId = $this->getRenderSettingItem()->pid;
            }
        }

        $elements       = parent::getBreadcrumbElements($environment, $elements);
        $renderSettings = $this->getRenderSettings();

        $elements[] = array(
            'url' => $this->generateUrl(
                'tl_metamodel_rendersetting',
                $this->seralizeId('tl_metamodel_rendersettings', $this->renderSettingsId)
            ),
            'text' => sprintf(
                $this->getBreadcrumbLabel($environment, 'tl_metamodel_rendersetting'),
                $renderSettings->name
            ),
            'icon' => $this->getBaseUrl() . '/system/modules/metamodels/assets/images/icons/rendersetting.png'
        );

        return $elements;
    }
}
