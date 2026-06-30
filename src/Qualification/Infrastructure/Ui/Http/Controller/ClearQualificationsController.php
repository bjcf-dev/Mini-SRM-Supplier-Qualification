<?php

declare(strict_types=1);

namespace App\Qualification\Infrastructure\Ui\Http\Controller;

use App\Qualification\Domain\Repository\QualificationRepositoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final readonly class ClearQualificationsController
{
    public function __construct(
        private QualificationRepositoryInterface $repository,
    ) {}

    #[Route('/api/qualifications', methods: ['DELETE'])]
    public function __invoke(): JsonResponse
    {
        $this->repository->deleteAll();

        return new JsonResponse(['message' => 'All qualifications deleted.'], Response::HTTP_OK);
    }
}
