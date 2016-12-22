<?php

use Mockery as m;

class ClientTest extends PHPUnit_Framework_TestCase
{
    public function teatDown()
    {
        m::close();
    }

    public function testRequest()
    {
        $response = new GuzzleHttp\Psr7\Response(
            200,
            ['X-Foo' => 'Bar'],
            GuzzleHttp\Psr7\stream_for('foo'));
        $mockedHandler = new GuzzleHttp\Handler\MockHandler([
            $response,
        ]);
        $handlerStack = \GuzzleHttp\HandlerStack::create($mockedHandler);

        $client = new LeagueWrap\Client();
        $client->baseUrl('http://google.com');
        $client->setTimeout(10);
        $client->addMock($handlerStack);
        $response = $client->request('', []);
        $this->assertEquals('foo', $response);
        $this->assertEquals(200, $response->getCode());
        $this->assertTrue($response->hasHeader('X-Foo'));
        $this->assertFalse($response->hasHeader('Missing-Header'));
        $this->assertEquals('Bar', $response->getHeader('X-Foo'));
        $this->assertNull($response->getHeader('that does not exists'));
        $headers = $response->getHeaders();
        $this->assertArrayHasKey('X-Foo', $headers);
        $this->assertCount(1, $headers);
        $this->assertEquals('Bar', $headers['X-Foo']);
    }

    /**
     * @expectedException LeagueWrap\Exception\BaseUrlException
     */
    public function testRequestNoBaseUrl()
    {
        $client = new LeagueWrap\Client();
        $client->request('', []);
    }

    public function testAsyncGuzzleClient()
    {
        $response = new GuzzleHttp\Psr7\Response(200, ['X-Foo' => 'Bar'], GuzzleHttp\Psr7\stream_for('foo'));
        // Add 2 responses to the mock queue
        $mockedHandler = new GuzzleHttp\Handler\MockHandler([
            $response,
            $response,
        ]);
        $handlerStack = GuzzleHttp\HandlerStack::create($mockedHandler);

        // Build the client
        $client = new LeagueWrap\Client();
        $client->baseUrl('http://google.com');
        $client->setTimeout(5);
        $client->addMock($handlerStack);

        // Test waiting for LeagueWrap Response
        $response = $client->requestAsync('')->wait();
        $this->assertEquals('foo', $response);
        $this->assertEquals(200, $response->getCode());
        $this->assertTrue($response->hasHeader('X-Foo'));
        $this->assertFalse($response->hasHeader('Missing-Header'));

        // Test with promise syntax
        $dummyService = new TestSomeAsyncService();
        $client->requestAsync('')
            ->then([$dummyService, 'onSuccess'])
            ->otherwise(function (\Exception $r) {
                $this->fail($r);
            })
            ->wait();
        $this->assertEquals($response, $dummyService->getState());
    }
}

class TestSomeAsyncService
{
    private $state;

    public function onSuccess(LeagueWrap\Response $response)
    {
        return $this->state = $response;
    }

    public function onFailure(LeagueWrap\Response $response)
    {
        // @TODO
    }

    public function getState()
    {
        return $this->state;
    }
}
