<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use OxidEsales\Eshop\Application\Controller\Admin\AdminDetailsController;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Domain\Locale\DataObject\Locale;
use OxidEsales\EshopCommunity\Internal\Domain\Locale\Exception\LocaleAlreadyExistsException;
use OxidEsales\EshopCommunity\Internal\Domain\Locale\Service\LocaleServiceInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use Symfony\Component\HttpFoundation\Request;

class Locales extends AdminDetailsController
{
    public function __construct(
        private readonly LocaleServiceInterface $localeService,
        private readonly ContextInterface $context,
        private readonly Request $request,
    ) {
        parent::__construct();
    }

    public function render(): string
    {
        parent::render();

        $assignedCodes = $this->getAssignedLocaleCodes();

        $this->_aViewData['locales'] = array_map(
            fn(Locale $locale): array => [
                'code' => $locale->getCode(),
                'name' => $locale->getName(),
                'fallback' => $locale->getFallbackCode(),
                'active' => in_array($locale->getCode(), $assignedCodes, true),
            ],
            $this->localeService->getAll()
        );

        return 'locales';
    }

    public function save(): void
    {
        $shopId = $this->context->getCurrentShopId();
        $assignedCodes = $this->getAssignedLocaleCodes();

        $this->updateExistingLocales($assignedCodes, $shopId);
        $this->addNewLocales($assignedCodes, $shopId);
    }

    public function delete(): void
    {
        $this->localeService->delete($this->request->request->get('localeCode'));
    }

    private function updateExistingLocales(array $assignedCodes, int $shopId): void
    {
        foreach ($this->request->request->all('locales') as $code => $data) {
            $code = (string) $code;
            $this->localeService->update(new Locale($code, $data['name'], $data['fallback']));
            $this->updateShopAssignment($code, !empty($data['active']), $assignedCodes, $shopId);
        }
    }

    private function addNewLocales(array $assignedCodes, int $shopId): void
    {
        foreach ($this->request->request->all('newLocales') as $data) {
            if (empty($data['code'])) {
                continue;
            }
            try {
                $this->localeService->add(new Locale($data['code'], $data['name'], $data['fallback']));
            } catch (LocaleAlreadyExistsException) {
                Registry::getUtilsView()->addErrorToDisplay('LOCALE_CODE_ALREADY_EXISTS');
                continue;
            }
            $this->updateShopAssignment($data['code'], !empty($data['active']), $assignedCodes, $shopId);
        }
    }

    private function getAssignedLocaleCodes(): array
    {
        return array_map(
            fn(Locale $locale): string => $locale->getCode(),
            $this->localeService->getForShop($this->context->getCurrentShopId())
        );
    }

    private function updateShopAssignment(string $code, bool $active, array $assignedCodes, int $shopId): void
    {
        $isAssigned = in_array($code, $assignedCodes, true);

        if ($active && !$isAssigned) {
            $this->localeService->addToShop($code, $shopId);
        } elseif (!$active && $isAssigned) {
            $this->localeService->removeFromShop($code, $shopId);
        }
    }
}
