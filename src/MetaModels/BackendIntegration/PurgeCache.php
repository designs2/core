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

namespace MetaModels\BackendIntegration;

use ContaoCommunityAlliance\Contao\Bindings\ContaoEvents;
use ContaoCommunityAlliance\Contao\Bindings\Events\System\LogEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Purge the MetaModels cache.
 */
class PurgeCache
{
    /**
     * Purge the page cache.
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     * @SuppressWarnings(PHPMD.CamelCaseVariableName)
     */
    public function purge()
    {
        foreach ($GLOBALS['TL_PURGE']['folders']['metamodels']['affected'] as $folderName) {
            // Purge the folder
            $folder = new \Folder($folderName);
            $folder->purge();
        }

        /** @var EventDispatcherInterface $dispatcher */
        $dispatcher = $GLOBALS['container']['event-dispatcher'];
        $dispatcher->dispatch(
            ContaoEvents::SYSTEM_LOG,
            new LogEvent('Purged the MetaModels cache', __METHOD__, TL_CRON)
        );
    }
}
