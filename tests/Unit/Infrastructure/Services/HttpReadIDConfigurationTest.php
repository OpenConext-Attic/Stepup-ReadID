<?php

declare(strict_types=1);

namespace StepupReadId\Tests\Unit\Infrastructure\Services;

use PHPUnit\Framework\TestCase;
use StepupReadId\Infrastructure\Services\HttpReadIDConfiguration;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

class HttpReadIDConfigurationTest extends TestCase
{
    public function testGetAuthorizationToken(): void
    {
        $params = $this->getMockBuilder(ContainerBagInterface::class)->getMock();
        $params
            ->expects($this->exactly(1))
            ->method('get')
            ->with('app.authorization_token')
            ->willReturn('authorization_token');

        $configuration = new HttpReadIDConfiguration($params);
        $value         = $configuration->authorizationToken();
        $this->assertSame('authorization_token', $value);
    }

    public function testEndpoint(): void
    {
        $params = $this->getMockBuilder(ContainerBagInterface::class)->getMock();
        $params
            ->expects($this->exactly(1))
            ->method('get')
            ->with('app.readid_endpoint')
            ->willReturn('http://site.com');

        $configuration = new HttpReadIDConfiguration($params);
        $value         = $configuration->endpoint();
        $this->assertSame('http://site.com', $value);
    }

    public function testCleanEndpoint(): void
    {
        $params = $this->getMockBuilder(ContainerBagInterface::class)->getMock();
        $params
            ->expects($this->exactly(1))
            ->method('get')
            ->with('app.readid_endpoint')
            ->willReturn('http://site.com/');

        $configuration = new HttpReadIDConfiguration($params);
        $value         = $configuration->endpoint();
        $this->assertSame('http://site.com', $value);
    }
}
