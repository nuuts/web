<?php

namespace Tests\Integration\CoreService;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use OmniSynapse\CoreService\AbstractJob;
use OmniSynapse\CoreService\CoreService;
use OmniSynapse\CoreService\Failed\Failed;
use OmniSynapse\CoreService\Response\Point;
use Tests\TestCase;
use Faker\Factory as Faker;

class ExceptionsTest extends TestCase
{
    /**
     * @param $response
     */
    private function sendRequestAndTestExceptions(Response $response)
    {
        $errorClientMock = \Mockery::mock(Client::class);
        $errorClientMock->shouldReceive('request')->once()->andReturn($response);

        $requestObject = (new class implements \JsonSerializable {
            function jsonSerialize() {
                return [];
            }
        });

        $coreService = (app()->make(CoreService::class))
            ->setClient($errorClientMock);

        ((new class ($coreService, $requestObject) extends AbstractJob {
            private $requestObject;

            public function __construct(CoreService $coreService, $requestObject)
            {
                parent::__construct($coreService);

                $this->requestObject = $requestObject;
            }
            public function getHttpMethod(): string {
                return 'GET';
            }
            public function getHttpPath(): string {
                return '';
            }
            public function getRequestObject(): \JsonSerializable
            {
                return $this->requestObject;
            }
            public function getResponseClass(): string {
                return Point::class;
            }
            public function getFailedResponseObject(\Exception $exception): Failed
            {
                return new Failed($exception);
            }
        }))->handle();
    }

    /**
     * @expectedException \OmniSynapse\CoreService\Exception\RequestException
     * @expectedExceptionCode 404
     * @expectedExceptionMessage request url not found
     */
    public function test404ServerError()
    {
        $response = new Response(\Illuminate\Http\Response::HTTP_NOT_FOUND, [
            'Content-Type' => 'application/json',
        ], \GuzzleHttp\json_encode([
            'error'   => 'not found',
            'message' => 'request url not found',
        ]));
        $this->sendRequestAndTestExceptions($response);
    }

    /**
     * @expectedException \OmniSynapse\CoreService\Exception\RequestException
     * @expectedExceptionCode 200
     */
    public function testJsonDecodeException()
    {
        $response = new Response(\Illuminate\Http\Response::HTTP_OK, [
            'Content-Type' => 'application/json',
        ]);
        $this->sendRequestAndTestExceptions($response);
    }

    /**
     * @expectedException \OmniSynapse\CoreService\Exception\RequestException
     * @expectedExceptionCode 200
     */
    public function testJsonMapperException()
    {
        $faker    = Faker::create();
        $response = new Response(\Illuminate\Http\Response::HTTP_OK, [
            'Content-Type' => 'application/json',
        ], \GuzzleHttp\json_encode([
            'lat'       => $faker->latitude,
            'longitude' => $faker->longitude, // longitude will call exception
        ]));
        $this->sendRequestAndTestExceptions($response);
    }

    /**
     * @expectedException \OmniSynapse\CoreService\Exception\RequestException
     * @expectedExceptionCode 200
     */
    public function testWrongResponse()
    {
        $response = new Response(\Illuminate\Http\Response::HTTP_OK, [
            'Content-Type' => 'application/json',
        ], \GuzzleHttp\json_encode([
            'error'   => true,
            'message' => 'error message',
        ]));
        $this->sendRequestAndTestExceptions($response);
    }
}
