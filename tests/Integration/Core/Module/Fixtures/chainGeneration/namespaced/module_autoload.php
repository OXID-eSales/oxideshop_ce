<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

spl_autoload_register(static function ($className) use ($modulesPath) {
    if (str_contains($className, 'chainGeneration\namespaced')) {
        require "{$modulesPath}/chainGeneration/namespaced/Model/Product.php";
    }
});
