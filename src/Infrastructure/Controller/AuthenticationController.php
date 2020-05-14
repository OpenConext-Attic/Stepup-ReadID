<?php

declare(strict_types=1);

namespace StepupReadId\Infrastructure\Controller;

use Psr\Log\LoggerInterface;
use StepupReadId\Application\ReadySession\GetStoredReadySessionQuery;
use StepupReadId\Infrastructure\Exception\NoActiveAuthnRequestException;
use Surfnet\GsspBundle\Service\AuthenticationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @Route("/authentication", name="readid_authentication")
 */
class AuthenticationController extends AbstractController
{
    use HandleTrait;

    /** @var AuthenticationService */
    private $authenticationService;
    /** @var LoggerInterface */
    private $logger;

    public function __construct(
        AuthenticationService $authenticationService,
        LoggerInterface $logger,
        MessageBusInterface $messageBus
    ) {
        $this->authenticationService = $authenticationService;
        $this->logger                = $logger;
        $this->messageBus            = $messageBus;
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

        $readySession = $this->handle(new GetStoredReadySessionQuery());

        return $this->render('default/authentication.html.twig', [
            'readySession' => $readySession,
            'verifyUrl' => $this->generateUrl('verify-ready-session', [], UrlGeneratorInterface::ABSOLUTE_URL),
            'returnUrl' => $this->generateUrl('readid_saml_sso_return', [], UrlGeneratorInterface::ABSOLUTE_URL),
        ]);
    }
}
