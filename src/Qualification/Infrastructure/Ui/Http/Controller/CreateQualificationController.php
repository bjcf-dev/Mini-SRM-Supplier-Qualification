<?php

declare(strict_types=1);

namespace App\Qualification\Infrastructure\Ui\Http\Controller;

use App\Qualification\Application\Create\CreateQualificationCommand;
use App\Qualification\Application\Create\CreateQualificationCommandHandler;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final readonly class CreateQualificationController
{
    public function __construct(
        private CreateQualificationCommandHandler $handler,
    ) {}

    #[Route('/api/qualifications', methods: ['POST'])]
    public function __invoke(Request $request): JsonResponse
    {
        $payload = json_decode($request->getContent(), true);

        if (!is_array($payload)) {
            return new JsonResponse(
                ['error' => 'Invalid JSON body.'],
                Response::HTTP_BAD_REQUEST,
            );
        }

        try {
            $command = new CreateQualificationCommand(
                supplierId: (string) ($payload['supplierId'] ?? ''),
                auditorId: (string) ($payload['auditorId'] ?? ''),
                score: (int) ($payload['score'] ?? -1),
                comments: (string) ($payload['comments'] ?? ''),
            );

            $qualificationId = ($this->handler)($command);
        } catch (\InvalidArgumentException $e) {
            return new JsonResponse(
                ['error' => $e->getMessage()],
                Response::HTTP_BAD_REQUEST,
            );
        }

        return new JsonResponse(
            ['id' => $qualificationId->value()],
            Response::HTTP_CREATED,
        );
    }
}
