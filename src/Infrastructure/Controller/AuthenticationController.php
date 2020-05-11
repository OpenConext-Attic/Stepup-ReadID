<?php

declare(strict_types=1);

namespace StepupReadId\Infrastructure\Controller;

use Psr\Log\LoggerInterface;
use StepupReadId\Infrastructure\Exception\NoActiveAuthnRequestException;
use StepupReadId\Infrastructure\Services\ReadySession\ReadySessionStateHandlerInterface;
use Surfnet\GsspBundle\Service\AuthenticationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/authentication", name="readid_authentication")
 */
class AuthenticationController extends AbstractController
{
    /** @var AuthenticationService */
    private $authenticationService;
    /** @var LoggerInterface */
    private $logger;
    /** @var ReadySessionStateHandlerInterface */
    private $readySessionStateHandler;

    public function __construct(
        AuthenticationService $authenticationService,
        LoggerInterface $logger,
        ReadySessionStateHandlerInterface $readySessionStateHandler
    ) {
        $this->authenticationService    = $authenticationService;
        $this->logger                   = $logger;
        $this->readySessionStateHandler = $readySessionStateHandler;
    }

    public function __invoke(): Response
    {
        $this->logger->info('Verifying if there is a pending authentication request from SP');

        if (!$this->authenticationService->authenticationRequired()) {
            $this->logger->error('There is no pending authentication request from SP');

            throw new NoActiveAuthnRequestException();
        }

        $this->logger->info('Verifying if authentication is finalized');

        if ($this->authenticationService->isAuthenticated()) {
            $this->logger->info('Authentication is finalized returning to service provider');

            return $this->authenticationService->replyToServiceProvider();
        }

        $readySession = $this->readySessionStateHandler->getReadySession();

        return $this->render('default/authentication.html.twig', ['readySession' => $readySession]);
    }
}
