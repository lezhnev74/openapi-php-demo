<?php
/**
 * @author Dmitry Lezhnev <lezhnev.work@gmail.com>
 * Date: 30 Apr 2019
 */
declare(strict_types=1);


class APITest extends \PHPUnit\Framework\TestCase
{
    /**
     * Prepare a new instance of App
     *
     * @return \Slim\App
     */
    protected function getApp(): \Slim\App
    {
        // We add a new validation here, which will automatically check the api against the schema
        $middleware = new \OpenAPIValidation\PSR15\SlimAdapter(
            \OpenAPIValidation\PSR15\ValidationMiddleware::fromYamlFile(__DIR__.'/../openapi/spec.yaml')
        );

        /** @var \Slim\App $app */
        $app = include __DIR__ . "/../public/app.php";
        $app->add($middleware);
        return $app;
    }

    function test_it_returns_json_password()
    {
        $psr17Factory = new \Nyholm\Psr7\Factory\Psr17Factory();
        $request      = $psr17Factory->createServerRequest('GET', '/generate');

        $response = $this->getApp()->process($request, new \Nyholm\Psr7\Response());

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals("application/json", $response->getHeader('Content-Type')[0]);

        $decodedResponse = json_decode((string)$response->getBody(), true);
        $this->assertEquals(20, strlen($decodedResponse['password']));
    }

    function test_it_returns_text_password()
    {
        $psr17Factory = new \Nyholm\Psr7\Factory\Psr17Factory();
        $request      = $psr17Factory->createServerRequest('GET', '/generate')->withHeader('Accept', 'text/plain');

        $response = $this->getApp()->process($request, new \Nyholm\Psr7\Response());

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals("text/plain", $response->getHeader('Content-Type')[0]);
        $this->assertEquals(20, strlen((string)$response->getBody()));
    }

    function test_it_can_generate_different_length()
    {
        $psr17Factory = new \Nyholm\Psr7\Factory\Psr17Factory();
        $request      = $psr17Factory->createServerRequest('GET', '/generate')->withHeader('Accept', 'text/plain')->withQueryParams(['length' => 10]);

        $response = $this->getApp()->process($request, new \Nyholm\Psr7\Response());

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(10, strlen((string)$response->getBody()));
    }

}