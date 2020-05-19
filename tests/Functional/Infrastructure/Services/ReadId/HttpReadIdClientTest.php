<?php

declare(strict_types=1);

namespace StepupReadId\Tests\Functional\Infrastructure\Services\ReadId;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * @SuppressWarnings(PHPMD.UnusedLocalVariable)
 */
class HttpReadIdClientTest extends KernelTestCase
{
    public function testRequestToSubmitterEndpoint(): void
    {
        self::bootKernel();

        $httpClient = new MockHttpClient(
            function ($method, $url, $options) {
                $this->assertContains('X-Innovalor-Authorization: submitter_token', $options['headers']);

                return new MockResponse();
            }
        );
        self::$container->set('test.' . HttpClientInterface::class, $httpClient);
        self::$container->get('test.http.read_id.submitter.client')
            ->get('/endpoint');
    }

    public function testRequestToViewerEndpoint(): void
    {
        self::bootKernel();

        $httpClient = new MockHttpClient(
            function ($method, $url, $options) {
                $this->assertContains('X-Innovalor-Authorization: viewer_token', $options['headers']);

                return new MockResponse();
            }
        );
        self::$container->set('test.' . HttpClientInterface::class, $httpClient);

        self::$container->get('test.http.read_id.viewer.client')
            ->get('/endpoint');
    }

    public function testGetRequest(): void
    {
        self::bootKernel();

        $httpClient = new MockHttpClient(
            function ($method, $url, $options) {
                $this->assertEquals('GET', $method);
                $this->assertEquals('https://readid.server.com/endpoint', $url);

                return new MockResponse();
            }
        );
        self::$container->set('test.' . HttpClientInterface::class, $httpClient);

        self::$container->get('test.http.read_id.submitter.client')
            ->get('endpoint');
    }

    public function testGetRequestWithArgs(): void
    {
        self::bootKernel();

        $httpClient = new MockHttpClient(
            function ($method, $url, $options) {
                $this->assertEquals('GET', $method);
                $this->assertEquals('https://readid.server.com/endpoint(\'session_id\')', $url);

                return new MockResponse();
            }
        );
        self::$container->set('test.' . HttpClientInterface::class, $httpClient);

        self::$container->get('test.http.read_id.submitter.client')
            ->get('endpoint(\'%s\')', ['session_id']);
    }

    public function testPostRequest(): void
    {
        self::bootKernel();

        $httpClient = new MockHttpClient(
            function ($method, $url, $options) {
                $this->assertEquals('POST', $method);
                $this->assertEquals('https://readid.server.com/endpoint', $url);
                $this->assertJson('{"data":"value"}', $options['body']);

                return new MockResponse();
            }
        );
        self::$container->set('test.' . HttpClientInterface::class, $httpClient);

        self::$container->get('test.http.read_id.submitter.client')
            ->post('endpoint', ['data' => 'value']);
    }

    public function testPostRequestWithArgs(): void
    {
        self::bootKernel();

        $httpClient = new MockHttpClient(
            function ($method, $url, $options) {
                $this->assertEquals('POST', $method);
                $this->assertEquals('https://readid.server.com/endpoint(\'session_id\')', $url);
                $this->assertJson('{"data":"value"}', $options['body']);

                return new MockResponse();
            }
        );
        self::$container->set('test.' . HttpClientInterface::class, $httpClient);

        self::$container->get('test.http.read_id.submitter.client')
            ->post('endpoint(\'%s\')', ['data' => 'value'], ['session_id']);
    }
}
