<?php

declare(strict_types=1);

namespace StepupReadId\Infrastructure\Services\ArgumentResolver;

use Psr\Log\LoggerInterface;
use RuntimeException;
use Surfnet\GsspBundle\Exception\UnrecoverableErrorException;
use Surfnet\SamlBundle\Http\RedirectBinding;
use Surfnet\SamlBundle\SAML2\ReceivedAuthnRequest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use function sprintf;

final class ReceivedAuthnRequestResolver implements ArgumentValueResolverInterface
{
    /** @var LoggerInterface */
    private $logger;
    /** @var RedirectBinding */
    private $httpBinding;

    public function __construct(
        RedirectBinding $httpBinding,
        LoggerInterface $logger
    ) {
        $this->httpBinding = $httpBinding;
        $this->logger      = $logger;
    }

    /**
     * @inheritDoc
     */
    public function supports(Request $request, ArgumentMetadata $argument)
    {
        return $argument->getType() === ReceivedAuthnRequest::class;
    }

    /**
     * @return iterable<ReceivedAuthnRequest>
     *
     * @inheritDoc
     */
    public function resolve(Request $request, ArgumentMetadata $argument)
    {
        try {
            $this->logger->info('Processing AuthnRequest');
            $authnRequest = $this->httpBinding->receiveSignedAuthnRequestFrom($request);
            $this->logger->notice(sprintf(
                'AuthnRequest processing complete, received AuthnRequest from "%s", request ID: "%s"',
                $authnRequest->getServiceProvider(),
                $authnRequest->getRequestId()
            ));
        } catch (RuntimeException $e) {
            throw new UnrecoverableErrorException(
                sprintf(
                    'Error processing the SAML authentication request: %s',
                    $e->getMessage()
                ),
                0,
                $e
            );
        }

        yield $authnRequest;
    }
}
