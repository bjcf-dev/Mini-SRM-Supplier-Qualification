<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class QualificationCreationTest extends WebTestCase
{
    public function test_create_approved_qualification_returns_201(): void
    {
        $client = self::createClient();

        $client->request('POST', '/api/qualifications', server: [
            'CONTENT_TYPE' => 'application/json',
        ], content: json_encode([
            'supplierId' => '550e8400-e29b-41d4-a716-446655440001',
            'auditorId'  => '550e8400-e29b-41d4-a716-446655440002',
            'score'      => 85,
            'comments'   => 'Auditoría superada',
        ]));

        $this->assertResponseStatusCodeSame(201);

        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('id', $data);
        $this->assertMatchesRegularExpression(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i',
            $data['id'],
        );
    }

    public function test_create_rejected_qualification(): void
    {
        $client = self::createClient();

        $client->request('POST', '/api/qualifications', server: [
            'CONTENT_TYPE' => 'application/json',
        ], content: json_encode([
            'supplierId' => '550e8400-e29b-41d4-a716-446655440003',
            'auditorId'  => '550e8400-e29b-41d4-a716-446655440004',
            'score'      => 30,
        ]));

        $this->assertResponseStatusCodeSame(201);

        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('id', $data);
    }

    public function test_invalid_score_returns_400(): void
    {
        $client = self::createClient();

        $client->request('POST', '/api/qualifications', server: [
            'CONTENT_TYPE' => 'application/json',
        ], content: json_encode([
            'supplierId' => '550e8400-e29b-41d4-a716-446655440001',
            'auditorId'  => '550e8400-e29b-41d4-a716-446655440002',
            'score'      => 150,
        ]));

        $this->assertResponseStatusCodeSame(400);

        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertSame(['error' => 'QualificationScore must be between 0 and 100.'], $data);
    }

    public function test_invalid_uuid_returns_400(): void
    {
        $client = self::createClient();

        $client->request('POST', '/api/qualifications', server: [
            'CONTENT_TYPE' => 'application/json',
        ], content: json_encode([
            'supplierId' => 'not-a-uuid',
            'auditorId'  => '550e8400-e29b-41d4-a716-446655440002',
            'score'      => 75,
        ]));

        $this->assertResponseStatusCodeSame(400);
    }

    public function test_invalid_json_returns_400(): void
    {
        $client = self::createClient();

        $client->request('POST', '/api/qualifications', server: [
            'CONTENT_TYPE' => 'application/json',
        ], content: 'not-json');

        $this->assertResponseStatusCodeSame(400);

        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertSame(['error' => 'Invalid JSON body.'], $data);
    }

    public function test_score_boundary_59_is_accepted(): void
    {
        $client = self::createClient();

        $client->request('POST', '/api/qualifications', server: [
            'CONTENT_TYPE' => 'application/json',
        ], content: json_encode([
            'supplierId' => '550e8400-e29b-41d4-a716-446655440005',
            'auditorId'  => '550e8400-e29b-41d4-a716-446655440006',
            'score'      => 59,
        ]));

        $this->assertResponseStatusCodeSame(201);
    }

    public function test_score_boundary_60_is_accepted(): void
    {
        $client = self::createClient();

        $client->request('POST', '/api/qualifications', server: [
            'CONTENT_TYPE' => 'application/json',
        ], content: json_encode([
            'supplierId' => '550e8400-e29b-41d4-a716-446655440007',
            'auditorId'  => '550e8400-e29b-41d4-a716-446655440008',
            'score'      => 60,
        ]));

        $this->assertResponseStatusCodeSame(201);
    }
}
