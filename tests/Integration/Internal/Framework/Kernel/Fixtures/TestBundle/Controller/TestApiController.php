<?php

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Kernel\Fixtures\TestBundle\Controller;

use OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Kernel\Fixtures\TestBundle\Service\GreetingServiceInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Twig\Environment;

#[Route('/api/test-bundle')]
readonly class TestApiController
{
    public function __construct(
        private GreetingServiceInterface $greetingService,
        private Environment $twig,
    ) {
    }

    #[Route('/hello/{name}', name: 'test_hello', methods: ['GET'])]
    public function hello(string $name): JsonResponse
    {
        return new JsonResponse(['message' => $this->greetingService->greet($name)]);
    }

    #[Route('/page/{name}', name: 'test_page', methods: ['GET'])]
    public function page(string $name): Response
    {
        return new Response($this->twig->render('@Test/hello.html.twig', [
            'name' => $name,
            'bundle_name' => 'TestBundle',
        ]));
    }

    #[Route('/inherited/{name}', name: 'test_inherited', methods: ['GET'])]
    public function inherited(string $name): Response
    {
        return new Response($this->twig->render('@Test/child.html.twig', [
            'name' => $name,
        ]));
    }

    #[Route('/echo', name: 'test_echo', methods: ['POST'])]
    public function echo(Request $request): JsonResponse
    {
        return new JsonResponse(['body' => $request->getContent()]);
    }

    #[Route('/error', name: 'test_error', methods: ['GET'])]
    public function error(): Response
    {
        throw new \RuntimeException('Test error from bundle');
    }
}
