<?php

declare(strict_types=1);

namespace StepupReadId\Tests\Functional\Infrastructure\Controller;

use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use function json_encode;

class WebhookControllerTest extends WebTestCase
{
    public function testInvalidToken(): void
    {
        $client = static::createClient();

        $client->request('POST', '/webhook/invalid_webhook_token');

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $client->getResponse()->getStatusCode());
        $this->assertStringContainsStringIgnoringCase('Invalid token', $client->getResponse()->getContent());
    }

    public function testInvalidPayload(): void
    {
        $client = static::createClient();

        $client->request('POST', '/webhook/webhook_token');

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $client->getResponse()->getStatusCode());
        $this->assertStringContainsStringIgnoringCase('Invalid message payload', $client->getResponse()->getContent());
    }

    public function testNotFoundPendingSession(): void
    {
        $client = static::createClient();

        $sessionId      = (string) Uuid::uuid4();
        $readySessionId = (string) Uuid::uuid4();
        $client->request(
            'POST',
            '/webhook/webhook_token',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['sessionId' => $sessionId, 'generatedKey' => $readySessionId])
        );

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $client->getResponse()->getStatusCode());
        $this->assertStringContainsStringIgnoringCase(
            'No pending session found with ReadySession id: ' . $readySessionId,
            $client->getResponse()->getContent()
        );
    }
}
