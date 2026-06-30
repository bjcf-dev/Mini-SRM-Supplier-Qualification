<?php

declare(strict_types=1);

namespace App\Qualification\Infrastructure\Ui\Http\Controller;

use App\Qualification\Domain\Model\QualificationId;
use App\Qualification\Domain\Repository\QualificationRepositoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final readonly class DeleteQualificationController
{
    public function __construct(
        private QualificationRepositoryInterface $repository,
    ) {}

    #[Route('/api/qualifications/{id}', methods: ['DELETE'])]
    public function __invoke(string $id): JsonResponse
    {
        try {
            $qualificationId = new QualificationId($id);
        } catch (\InvalidArgumentException) {
            return new JsonResponse(
                ['error' => 'Invalid UUID.'],
                Response::HTTP_BAD_REQUEST,
            );
        }

        $this->repository->deleteById($qualificationId);

        return new JsonResponse(['message' => 'Deleted.'], Response::HTTP_OK);
    }
}
