<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Setup;

/** Responsible dor returning edition specific messages. */
abstract class Messages
{
    /**
     * Returns messages data which is used as translations.
     *
     * @return array
     */
    abstract public function getMessages();
}
