<?php

declare(strict_types=1);

namespace App\Qualification\Infrastructure\Persistence\Doctrine\Repository;

use App\Qualification\Domain\Model\Qualification;
use App\Qualification\Domain\Model\QualificationId;
use App\Qualification\Domain\Repository\QualificationRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

final readonly class DoctrineQualificationRepository implements QualificationRepositoryInterface
{
    private EntityRepository $repository;

    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
        $this->repository = $entityManager->getRepository(Qualification::class);
    }

    public function save(Qualification $qualification): void
    {
        $this->entityManager->persist($qualification);
        $this->entityManager->flush();
    }

    /** @return Qualification[] */
    public function findAll(): array
    {
        return $this->repository->findAll();
    }

    public function deleteById(QualificationId $id): void
    {
        $qualification = $this->repository->find($id->value());
        if ($qualification !== null) {
            $this->entityManager->remove($qualification);
            $this->entityManager->flush();
        }
    }

    public function deleteAll(): void
    {
        $this->entityManager->createQueryBuilder()
            ->delete(Qualification::class, 'q')
            ->getQuery()
            ->execute();
    }
}
