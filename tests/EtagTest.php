<?php

use Illuminate\Http\Request;
use Matthewbdaly\ETagMiddleware\ETag;
use Mockery as m;

class EtagTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test new request not cached.
     *
     * @return void
     */
    public function testModified()
    {
        // Create mock response
        $response = m::mock('Illuminate\Http\Response')->shouldReceive('getContent')->once()->andReturn('blah')->getMock();
        $response->shouldReceive('setEtag')->with(md5('blah'));

        // Create request
        $request = Request::create('http://example.com/admin', 'GET');

        // Pass it to the middleware
        $middleware = new ETag();
        $middlewareResponse = $middleware->handle($request, function () use ($response) {
            return $response;
        });
    }

    /**
     * Test repeated request not modified.
     *
     * @return void
     */
    public function testNotModified()
    {
        // Create mock response
        $response = m::mock('Illuminate\Http\Response')->shouldReceive('getContent')->once()->andReturn('blah')->getMock();
        $response->shouldReceive('setEtag')->with(md5('blah'));
        $response->shouldReceive('setNotModified');

        // Create request
        $request = Request::create('http://example.com/admin', 'GET', [], [], [], [
            'ETag' => md5('blah'),
        ]);

        // Pass it to the middleware
        $middleware = new ETag();
        $middlewareResponse = $middleware->handle($request, function () use ($response) {
            return $response;
        });
    }

    public function tearDown()
    {
        m::close();
    }
}
