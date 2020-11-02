<?php

declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
define('OX_IS_ADMIN', true);
define('OX_ADMIN_DIR', basename(__DIR__));

// Includes main index.php file
require_once __DIR__ . '/../index.php';
