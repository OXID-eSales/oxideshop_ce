<?php

declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core\Contract;

/**
 * The implementation of this class maps className to classId and vice versa.
 */
interface ClassNameResolverInterface
{
    /**
     * Map argument classId to related className.
     *
     * @param string $classId class id
     *
     * @return string|null
     */
    public function getClassNameById($classId);

    /**
     * Map argument className to related classId.
     *
     * @param string $className class name
     *
     * @return string|null
     */
    public function getIdByClassName($className);
}
