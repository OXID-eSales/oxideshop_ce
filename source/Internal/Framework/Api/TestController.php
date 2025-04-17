<?php

namespace OxidEsales\EshopCommunity\Internal\Framework\Api;

use OxidEsales\Eshop\Application\Model\Article;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\ShopIdCalculator;
use OxidEsales\Eshop\Core\UtilsServer;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao\ModuleConfigurationDaoInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

readonly class TestController
{
    public function __construct(private ModuleConfigurationDaoInterface $moduleConfigurationDao)
    {
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/api/wing/{name}/{num}/{class}/', methods: ['GET'])]
    public function fly(string $name, int $num, ?string $class = null): Response
    {
        $dir = Registry::getConfig()->getConfigParam('aCachableClasses');
        $article = oxNew(Article::class);
        return new JsonResponse(
            [
                sprintf("Hello %s, %d, %s", $name, $num, $class),
                $dir,
                $this->moduleConfigurationDao->getAll(1),
                $article,
                (new ShopIdCalculator(new UtilsServer()))->getShopId()
            ]
        );
    }
}
