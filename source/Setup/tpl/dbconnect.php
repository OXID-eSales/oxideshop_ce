<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
require '_header.php'; ?>
<b><?php $this->getText('STEP_3_1_DB_CONNECT_IS_OK'); ?></b><br>
<?php
if (1 === $this->getViewParam('blCreated')) {
    $aDB = $this->getViewParam('aDB'); ?><b><?php printf($this->getText('STEP_3_1_DB_CREATE_IS_OK', false), $aDB['dbName']); ?></b><br><?php
}
?>
<?php require '_footer.php';
