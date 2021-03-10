<?php

declare(strict_types=1);

namespace StepupReadId\Infrastructure\Controller;

use Psr\Log\LoggerInterface;
use RuntimeException;
use SAML2\Response as SAMLResponse;
use StepupReadId\Infrastructure\Services\SAML\SamlResponseFactory;
use Surfnet\GsspBundle\Exception\UnrecoverableErrorException;
use Surfnet\GsspBundle\Saml\ResponseContextInterface;
use Surfnet\GsspBundle\Service\StateHandlerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use function base64_encode;
use function sprintf;

/**
 * @Route("/saml/sso_return", name="readid_saml_sso_return", methods={"POST", "GET"})
 */
final class SingleSignOnReturnController extends AbstractController
{
    /** @var LoggerInterface */
    private $logger;
    /** @var ResponseContextInterface */
    private $responseContext;
    /** @var StateHandlerInterface */
    private $stateHandler;
    /** @var SamlResponseFactory */
    private $samlResponseFactory;

    public function __construct(
        LoggerInterface $logger,
        ResponseContextInterface $responseContext,
        SamlResponseFactory $samlResponseFactory,
        StateHandlerInterface $stateHandler
    ) {
        $this->logger              = $logger;
        $this->responseContext     = $responseContext;
        $this->samlResponseFactory = $samlResponseFactory;
        $this->stateHandler        = $stateHandler;
    }

    public function __invoke(): Response
    {
        $this->logger->notice('Received sso return request');

        if (!$this->responseContext->hasRequest()) {
            throw new UnrecoverableErrorException('There is no request state present');
        }

        try {
            if ($this->responseContext->inErrorState()) {
                return $this->ssoErrorReturnAction();
            }

            if ($this->stateHandler->isRequestTypeAuthentication()) {
                return $this->ssoAuthenticationReturnAction();
            }
        } catch (RuntimeException $e) {
            $this->logger->alert('Unable to handle return action: ' . $e->getMessage());

            throw $e;
        }

        throw new UnrecoverableErrorException('Application state invalid');
    }

    private function ssoErrorReturnAction(): Response
    {
        $samlResponse = $this->createSamlResponse();

        return $this->renderSamlResponse($samlResponse);
    }

    private function ssoAuthenticationReturnAction(): Response
    {
        if (!$this->stateHandler->isAuthenticated()) {
            $this->logger->warning(
                'User was not authenticated by the application, ' .
                'redirect user back the authentication route'
            );

            return $this->redirectToRoute('readid_authentication');
        }

        $samlResponse = $this->samlResponseFactory->fromValidatedSession();

        return $this->renderSamlResponse($samlResponse);
    }

    private function createSamlResponse(): SAMLResponse
    {
        $this->logger->info('Create sso response');

        $response = $this->samlResponseFactory->create();

        $this->logger->notice(sprintf(
            'Saml response created with id "%s", request ID: "%s"',
            $response->getId(),
            $this->responseContext->getRequestId()
        ));

        return $response;
    }

    private function renderSamlResponse(SAMLResponse $samlResponse): Response
    {
        $acu      = $this->responseContext->getAssertionConsumerUrl();
        $response = $this->render('@SurfnetGssp/StepupGssp/ssoReturn.html.twig', [
            'acu' => $acu,
            'response' => $this->getResponseAsXML($samlResponse),
            'relayState' => $this->responseContext->getRelayState(),
        ]);

        // We clear the state, because we don't need it anymore.
        $this->logger->notice(sprintf(
            'Invalidate current state and redirect user to service provider assertion consumer url "%s"',
            $acu
        ));
        $this->stateHandler->invalidate();

        return $response;
    }

    private function getResponseAsXML(SAMLResponse $response): string
    {
        return base64_encode((string) $response->toSignedXML()->ownerDocument->saveXML());
    }
}
