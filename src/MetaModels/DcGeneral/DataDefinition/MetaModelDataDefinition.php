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

namespace MetaModels\DcGeneral\DataDefinition;

use ContaoCommunityAlliance\DcGeneral\DataDefinition\DefaultContainer;
use MetaModels\DcGeneral\DataDefinition\Definition\IMetaModelDefinition;

/**
 * Default implementation of IMetaModelDataDefinition.
 */
class MetaModelDataDefinition extends DefaultContainer implements IMetaModelDataDefinition
{
    /**
     * {@inheritDoc}
     */
    public function setMetaModelDefinition(IMetaModelDefinition $definition)
    {
        $this->setDefinition(IMetaModelDefinition::NAME, $definition);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function hasMetaModelDefinition()
    {
        return $this->hasDefinition(IMetaModelDefinition::NAME);
    }

    /**
     * {@inheritDoc}
     */
    public function getMetaModelDefinition()
    {
        return $this->getDefinition(IMetaModelDefinition::NAME);
    }
}
