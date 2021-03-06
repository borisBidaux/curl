<?php
/**
 * This file is part of the jyggen/curl library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright Copyright (c) Jonas Stendahl <jonas.stendahl@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link https://jyggen.com/projects/jyggen-curl Documentation
 * @link https://packagist.org/packages/jyggen/curl Packagist
 * @link https://github.com/jyggen/curl GitHub
 */


namespace Jyggen\Curl;

use Jyggen\Curl\Curl;
use Mockery as m;

class CurlTest extends \PHPUnit_Framework_TestCase
{

    public function teardown()
    {
        m::close();
    }

    public function testDelete()
    {
        $responses = Curl::delete('http://httpbin.org/delete');
        $this->assertInstanceOf('Jyggen\\Curl\\Response', $responses[0]);
        $content = json_decode($responses[0]->getContent());
        $this->assertSame(JSON_ERROR_NONE, json_last_error());
        $this->assertSame('http://httpbin.org/delete', $content->url);
    }

    public function testGet()
    {
        $responses = Curl::get('http://httpbin.org/get');
        $this->assertInstanceOf('Jyggen\\Curl\\Response', $responses[0]);
        $content = json_decode($responses[0]->getContent());
        $this->assertSame(JSON_ERROR_NONE, json_last_error());
        $this->assertSame('http://httpbin.org/get', $content->url);
    }

    public function testPost()
    {
        $responses = Curl::post('http://httpbin.org/post');
        $this->assertInstanceOf('Jyggen\\Curl\\Response', $responses[0]);
        $content = json_decode($responses[0]->getContent());
        $this->assertSame(JSON_ERROR_NONE, json_last_error());
        $this->assertSame('http://httpbin.org/post', $content->url);
    }

    public function testPut()
    {
        $responses = Curl::put('http://httpbin.org/put');
        $this->assertInstanceOf('Jyggen\\Curl\\Response', $responses[0]);
        $content = json_decode($responses[0]->getContent());
        $this->assertSame(JSON_ERROR_NONE, json_last_error());
        $this->assertSame('http://httpbin.org/put', $content->url);
    }

    public function testMultipleUrls()
    {
        $responses = Curl::get(array('http://httpbin.org/get?bar=foo', 'http://httpbin.org/get?foo=bar'));
        $this->assertInstanceOf('Jyggen\\Curl\\Response', $responses[0]);
        $content = json_decode($responses[0]->getContent());
        $this->assertSame(JSON_ERROR_NONE, json_last_error());
        $this->assertSame('foo', $content->args->bar);
        $this->assertInstanceOf('Jyggen\\Curl\\Response', $responses[1]);
        $content = json_decode($responses[1]->getContent());
        $this->assertSame(JSON_ERROR_NONE, json_last_error());
        $this->assertSame('bar', $content->args->foo);
    }

    public function testPostWithData()
    {
        $responses = Curl::post('http://httpbin.org/post', array('foo' => 'bar', 'bar' => 'foo'));
        $this->assertInstanceOf('Jyggen\\Curl\\Response', $responses[0]);
        $content = json_decode($responses[0]->getContent());
        $this->assertSame(JSON_ERROR_NONE, json_last_error());
        $this->assertSame('bar', $content->form->foo);
        $this->assertSame('foo', $content->form->bar);
    }

    public function testPutWithData()
    {
        $responses = Curl::put('http://httpbin.org/put', array('foo' => 'bar', 'bar' => 'foo'));
        $this->assertInstanceOf('Jyggen\\Curl\\Response', $responses[0]);
        $content = json_decode($responses[0]->getContent());
        $this->assertSame(JSON_ERROR_NONE, json_last_error());
        $this->assertSame('bar', $content->form->foo);
        $this->assertSame('foo', $content->form->bar);
    }

    public function testExecuteWithCallback()
    {
        $check     = false;
        $responses = Curl::get('http://httpbin.org/get', null, function () use (&$check) {
            $check = true;
        });

        $this->assertTrue($check);
    }
}
