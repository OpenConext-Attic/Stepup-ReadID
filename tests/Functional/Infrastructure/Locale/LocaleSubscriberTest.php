<?php

declare(strict_types=1);

/**
 * Copyright 2010 SURFnet B.V.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace StepupReadId\Tests\Functional\Infrastructure\Locale;

use Generator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use StepupReadId\Infrastructure\Locale\LocaleSubscriber;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class LocaleSubscriberTest extends TestCase
{
    /** @var MockObject|SessionInterface */
    private $session;
    /** @var MockObject|HttpKernelInterface */
    private $kernel;

    protected function setUp(): void
    {
        $this->kernel  = $this->getMockBuilder(HttpKernelInterface::class)->disableOriginalConstructor()->getMock();
        $this->session = $this->getMockBuilder(SessionInterface::class)->disableOriginalConstructor()->getMock();
    }

    /**
     * @param string[] $supportedLocales
     *
     * @dataProvider dataProviderLocaleInitialization
     */
    public function testLocaleSubscriberInitialization(
        string $acceptLanguage,
        string $defaultLocale,
        array $supportedLocales,
        string $resultLocale
    ): void {
        $server  = ['HTTP_ACCEPT_LANGUAGE' => $acceptLanguage];
        $request = new Request([], [], [], [], [], $server, null);

        $this->session->expects($this->exactly(1))
            ->method('has')
            ->with('_locale')
            ->willReturn(false);

        $this->session->expects($this->exactly(1))
            ->method('set')
            ->with('_locale', $resultLocale);

        $request->setSession($this->session);
        $requestEventMock = new RequestEvent($this->kernel, $request, HttpKernelInterface::MASTER_REQUEST);
        $subscriber       = new LocaleSubscriber($supportedLocales, $defaultLocale);
        $subscriber->onKernelRequest($requestEventMock);

        $this->assertSame($request->getLocale(), $resultLocale);
    }

    /**
     * @dataProvider dataProviderLocaleSession
     */
    public function testLocaleSubscriberSession(string $sessionLocale): void
    {
        $request = new Request([], [], [], [], [], [], null);

        $this->session->expects($this->exactly(1))
            ->method('has')
            ->with('_locale')
            ->willReturn(true);

        $this->session->expects($this->exactly(1))
            ->method('get')
            ->with('_locale')
            ->willReturn($sessionLocale);

        $request->setSession($this->session);
        $requestEventMock = new RequestEvent($this->kernel, $request, HttpKernelInterface::MASTER_REQUEST);
        $subscriber       = new LocaleSubscriber(['nl', 'en'], 'en');
        $subscriber->onKernelRequest($requestEventMock);

        $this->assertSame($request->getLocale(), $sessionLocale);
    }

    /**
     * @return Generator|mixed[]
     */
    public function dataProviderLocaleInitialization()
    {
        yield['en', 'en', [], 'en'];
        yield['en', 'en', ['en','nl'], 'en'];
        yield['nl_NL', 'en', ['en','nl'], 'nl'];
        yield['nl-NL, en;q=0.9, de;q=0.8, be;q=0.7, *;q=0.5', 'en', ['en','nl'], 'nl'];
        yield['en', 'de', ['en','nl'], 'en'];
    }

    /**
     * @return Generator|mixed[]
     */
    public function dataProviderLocaleSession()
    {
        yield['en'];
        yield['en'];
    }
}
