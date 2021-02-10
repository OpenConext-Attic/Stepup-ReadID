<?php

declare(strict_types=1);

namespace StepupReadId\Infrastructure\Controller;

use Psr\Log\LoggerInterface;
use StepupReadId\Application\PendingSession\ConfirmPendingSessionCommand;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Routing\Annotation\Route;
use function array_key_exists;
use function hash_equals;
use function json_decode;
use function sprintf;

/**
 * @Route("/webhook/{token}", name="readid_webhook", methods={"POST"}, format="json")
 */
final class WebhookController extends AbstractController
{
    private const SESSION_ID    = 'sessionId';
    private const GENERATED_KEY = 'generatedKey';

    /** @var LoggerInterface */
    private $logger;
    /** @var string */
    private $readIdWebhookToken;

    public function __construct(
        LoggerInterface $logger,
        string $readIdWebhookToken
    ) {
        $this->logger             = $logger;
        $this->readIdWebhookToken = $readIdWebhookToken;
    }

    public function __invoke(Request $request, string $token): Response
    {
        // Get RAW HTTP body content
        $content=$request->getContent();
        $this->logger->info( sprintf(
            'Received ReadID webhook request with content: "%s"',
            $content )
        );
        syslog( LOG_INFO, sprintf(
            'Received ReadID webhook request with content: "%s"',
            $content )
        );
        $this->checkValidWebhookController($token);
        syslog( LOG_INFO, 'Webhook controller token is valid, continuing' );

        $parameters = $this->getSessionParametersFromRequest($request);

        try {
            $this->dispatchMessage(
                new ConfirmPendingSessionCommand(
                    $parameters[self::GENERATED_KEY],
                    $parameters[self::SESSION_ID]
                )
            );

            $this->logger->notice(sprintf(
                'Confirmed session %s from ready session %s',
                $parameters[self::SESSION_ID],
                $parameters[self::GENERATED_KEY]
            ));
        } catch (HandlerFailedException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        return new JsonResponse(['success' => true]);
    }

    private function checkValidWebhookController(string $token): void
    {
        if (!hash_equals($this->readIdWebhookToken, $token)) {
            $this->logger->critical('Invalid token received from ReadID backend');

            throw new BadRequestHttpException('Invalid token');
        }
    }

    /**
     * @return string[]
     */
    private function getSessionParametersFromRequest(Request $request): array
    {
        $parameters = [];
        $content    = $request->getContent();

        $this->logger->info( sprintf(
            'Webhook received content: %s',
             $content) );

        if ($content) {
            $parameters = json_decode($content, true);
        }

        if (!array_key_exists(self::SESSION_ID, $parameters)) {
            $this->logger->critical('Invalid payload. Missing sessionId parameter');

            throw new BadRequestHttpException('Invalid message payload');
        }

        if (!array_key_exists(self::GENERATED_KEY, $parameters)) {
            $this->logger->critical('Invalid payload. Missing generatedKey parameter');

            throw new BadRequestHttpException('Invalid message payload');
        }

        return $parameters;
    }
}
