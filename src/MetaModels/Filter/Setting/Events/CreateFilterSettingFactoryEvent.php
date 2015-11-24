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

namespace MetaModels\Filter\Setting\Events;

use MetaModels\Filter\Setting\IFilterSettingFactory;
use Symfony\Component\EventDispatcher\Event;

/**
 * This event is triggered for every attribute factory instance that is created.
 */
class CreateFilterSettingFactoryEvent extends Event
{
    /**
     * The factory that has been created.
     *
     * @var IFilterSettingFactory
     */
    protected $factory;

    /**
     * Create a new instance.
     *
     * @param IFilterSettingFactory $factory The factory that has been created.
     */
    public function __construct($factory)
    {
        $this->factory = $factory;
    }

    /**
     * Retrieve the attribute information array.
     *
     * @return IFilterSettingFactory
     */
    public function getFactory()
    {
        return $this->factory;
    }
}
