<?php

declare(strict_types=1);

namespace App\Qualification\Infrastructure\Ui\Http\Controller;

use App\Qualification\Domain\Repository\QualificationRepositoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final readonly class ListQualificationsController
{
    public function __construct(
        private QualificationRepositoryInterface $repository,
    ) {}

    #[Route('/api/qualifications', methods: ['GET'])]
    public function __invoke(): JsonResponse
    {
        $qualifications = $this->repository->findAll();

        $data = array_map(static fn ($q) => [
            'id'         => $q->id()->value(),
            'supplierId' => $q->supplierId()->value(),
            'auditorId'  => $q->auditorId()->value(),
            'score'      => $q->score()->value(),
            'status'     => $q->status()->value,
            'comments'   => $q->comments(),
            'createdAt'  => $q->createdAt()->format(\DateTimeInterface::ATOM),
            'expiresAt'  => $q->expiresAt()->format(\DateTimeInterface::ATOM),
        ], $qualifications);

        return new JsonResponse($data, Response::HTTP_OK);
    }
}
