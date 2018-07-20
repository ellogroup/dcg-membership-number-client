<?php

use Dcg\Client\MembershipNumber\Client;
use GuzzleHttp\Message\Response;
use GuzzleHttp\Stream\Stream;
use GuzzleHttp\Subscriber\Mock;
use PHPUnit\Framework\TestCase;

class StoreTest extends TestCase
{

    protected $config = null;

    public function setUp()
    {
        parent::setUp(); // TODO: Change the autogenerated stub

        $this->config = \Dcg\Client\MembershipNumber\Config::getInstance(__DIR__.'/../../config.php');
    }

    /**
     * @test
     */
    public function does_client_return_success()
    {
        $mock = new Mock([
            new Response(200, [], Stream::factory(json_encode([['message' => 'Success']])))
        ]);

        $client = new Client([], $this->config);

        $client->getEmitter()->attach($mock);

        $toCreate = [
            ['membership_number' => '888888', 'brand' => 'TC'],
            ['membership_number' => '777777', 'brand' => 'GS']
        ];

        $this->assertEquals(true, $client->store($toCreate));
    }

    /**
     * @test
     */
    public function does_client_throw_exception_for_invalid_input()
    {
        $this->setExpectedException('\\Dcg\\Client\\MembershipNumber\\Exception\\MembershipNumberException', 'Invalid data passed into store');
        $client = new Client([], $this->config);

        $toCreate = [
            ['membership_number' => '888888', 'brand' => 'TC'],
            ['membership_number' => '777777', 'InvalidKey' => 'GS']
        ];

        $this->assertEquals(true, $client->store($toCreate));
    }

    /**
     * @test
     */
    public function does_client_handle_500_error()
    {
        $mock = new Mock([
            new Response(404, [], Stream::factory(json_encode(['error' => 'Unable to store membership numbers'])))
        ]);

        $client = new Client([], $this->config);

        $client->getEmitter()->attach($mock);

        $this->setExpectedException('\\Dcg\\Client\\MembershipNumber\\Exception\\MembershipNumberException', 'Unable to store membership numbers');

        $toCreate = [
            ['membership_number' => '888888', 'brand' => 'TC'],
            ['membership_number' => '777777', 'brand' => 'GS']
        ];

        $client->store($toCreate);
    }
}