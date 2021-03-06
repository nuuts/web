<?php

namespace OmniSynapse\CoreService\Job;

use App\Models\NauModels\Account;
use App\Models\NauModels\Offer;
use Carbon\Carbon;
use Faker\Factory as Faker;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use OmniSynapse\CoreService\CoreService;
use OmniSynapse\CoreService\FailedJob;
use OmniSynapse\CoreService\Request\Offer\Geo;
use OmniSynapse\CoreService\Request\Offer\Limits;
use OmniSynapse\CoreService\Request\Offer\Point;
use Tests\TestCase;

class OfferCreatedTest extends TestCase
{
    /**
     * OfferCreated JOB test.
     *
     * @return void
     */
    public function testOfferCreated()
    {
        $faker = Faker::create();

        /*
         * Account
         */
        $account     = [
            'id'       => $faker->uuid,
            'owner_id' => $faker->uuid,
        ];
        $accountMock = \Mockery::mock(Account::class);
        $accountMock->shouldReceive('getId')->andReturn($account['id']);
        $accountMock->shouldReceive('getOwnerId')->once()->andReturn($account['owner_id']);

        /*
         * Offer
         */
        $offerId        = $faker->uuid;
        $offerDateTimes = [
            'startDate' => Carbon::today()->subMonth(),
            'endDate'   => Carbon::today()->addMonth(),
            'startTime' => Carbon::parse($faker->time()),
            'endTime'   => Carbon::parse($faker->time()),
        ];
        $offer          = [
            'id' => $faker->uuid,

            'name'        => $faker->name,
            'description' => $faker->text,
            'categoryId'  => $faker->uuid,
            'reward'      => $faker->randomFloat(),

            'radius'  => $faker->randomDigitNotNull,
            'city'    => $faker->city,
            'country' => $faker->country,
            'lat'     => $faker->latitude,
            'lon'     => $faker->longitude,

            'offers'             => $faker->randomDigitNotNull,
            'perDay'             => $faker->randomDigitNotNull,
            'perUser'            => $faker->randomDigitNotNull,
            'maxForUserPerDay'   => $faker->randomDigit,
            'maxForUserPerWeek'  => $faker->randomDigit,
            'maxForUserPerMonth' => $faker->randomDigit,
            'minLevel'           => $faker->randomDigitNotNull,

            'status'   => 'active',
            'reserved' => $faker->randomDigitNotNull,
        ];

        $offerMock = \Mockery::mock(Offer::class);
        $offerMock->shouldReceive('getId')->once()->andReturn($offer['id']);
        $offerMock->shouldReceive('getLabel')->once()->andReturn($offer['name']);
        $offerMock->shouldReceive('getDescription')->once()->andReturn($offer['description']);
        $offerMock->shouldReceive('getCategoryId')->once()->andReturn($offer['categoryId']);
        $offerMock->shouldReceive('getReward')->once()->andReturn($offer['reward']);
        $offerMock->shouldReceive('getStartDate')->once()->andReturn($offerDateTimes['startDate']);
        $offerMock->shouldReceive('getFinishDate')->once()->andReturn($offerDateTimes['endDate']);
        $offerMock->shouldReceive('getLatitude')->once()->andReturn($offer['lat']);
        $offerMock->shouldReceive('getLongitude')->once()->andReturn($offer['lon']);
        $offerMock->shouldReceive('getRadius')->once()->andReturn($offer['radius']);
        $offerMock->shouldReceive('getCity')->once()->andReturn($offer['city']);
        $offerMock->shouldReceive('getCountry')->once()->andReturn($offer['country']);
        $offerMock->shouldReceive('getMaxCount')->once()->andReturn($offer['offers']);
        $offerMock->shouldReceive('getMaxPerDay')->once()->andReturn($offer['perDay']);
        $offerMock->shouldReceive('getMaxForUser')->once()->andReturn($offer['perUser']);
        $offerMock->shouldReceive('getMaxForUserPerDay')->once()->andReturn($offer['maxForUserPerDay']);
        $offerMock->shouldReceive('getMaxForUserPerWeek')->once()->andReturn($offer['maxForUserPerWeek']);
        $offerMock->shouldReceive('getMaxForUserPerMonth')->once()->andReturn($offer['maxForUserPerMonth']);
        $offerMock->shouldReceive('getUserLevelMin')->once()->andReturn($offer['minLevel']);
        $offerMock->shouldReceive('getAccount')->once()->andReturn($accountMock);
        $offerMock->shouldReceive('getStatus')->once()->andReturn($offer['status']);
        $offerMock->shouldReceive('getReserved')->once()->andReturn($offer['reserved']);

        /*
         * GEO
         */
        $point = new Point($offer['lat'], $offer['lon']);
        $geo   = new Geo($point, $offer['radius'], $offer['city'], $offer['country']);

        /*
         * Limits
         */
        $limits = (new Limits)
            ->setMaxCount($offer['offers'])
            ->setPerDay($offer['perDay'])
            ->setPerUser($offer['perUser'])
            ->setMinLevel($offer['minLevel']);

        $response = new Response(201, [
            'Content-Type' => 'application/json',
        ], \GuzzleHttp\json_encode([
            'id'          => $offerId,
            'owner_id'    => $account['owner_id'],
            'name'        => $offer['name'],
            'description' => $offer['description'],
            'category_id' => $offer['categoryId'],
            'geo'         => $geo->jsonSerialize(),
            'limits'      => $limits->jsonSerialize(),
            'reward'      => $offer['reward'],
            'start_date'  => $offerDateTimes['startDate']->format('Y-m-dTH:i:sO'),
            'end_date'    => $offerDateTimes['endDate']->format('Y-m-dTH:i:sO'),
        ]));

        $clientMock = \Mockery::mock(Client::class);
        $clientMock->shouldReceive('request')->once()->andReturn($response);

        $eventCalled = 0;
        \Event::listen(\OmniSynapse\CoreService\Response\Offer::class, function ($response) use (
            $offerId,
            $account,
            $offer,
            $offerDateTimes,
            $geo,
            $limits,
            &$eventCalled
        ) {
            $this->assertEquals($offerId, $response->getId(), 'Offer id');
            $this->assertEquals($account['owner_id'], $response->getOwnerId(), 'Offer owner_id');
            $this->assertEquals($offer['name'], $response->getName(), 'Offer name');
            $this->assertEquals($offer['description'], $response->getDescription(), 'Offer description');
            $this->assertEquals($offer['categoryId'], $response->getCategoryId(), 'Offer category_id');
            $this->assertEquals($geo->jsonSerialize(), $response->getGeo()->jsonSerialize(), 'Offer GEO');
            $this->assertEquals($limits->jsonSerialize(), $response->getLimits()->jsonSerialize(), 'Offer id');
            $this->assertEquals($offer['reward'], $response->getReward(), 'Offer reward');
            $this->assertEquals($offerDateTimes['startDate'], $response->getStartDate(), 'Offer start_date');
            $this->assertEquals($offerDateTimes['endDate'], $response->getEndDate(), 'Offer end_date');
            $eventCalled++;
        });

        $exceptionEventCalled = 0;
        \Event::listen(FailedJob\OfferCreated::class, function () use (&$exceptionEventCalled) {
            $exceptionEventCalled++;
        });

        $offerCreated = $this->app->make(CoreService::class)
                                  ->setClient($clientMock)
                                  ->offerCreated($offerMock);

        $offerCreated->handle();
        $offerCreated->failed((new \Exception));

        $this->assertEquals(1, $eventCalled, 'Can not listen Offer event.');
        $this->assertEquals(1, $exceptionEventCalled, 'Can not listen OfferCreated failed job.');

        $this->assertEquals([
            'coreService',
            'requestObject',
            'offer',
        ], $offerCreated->__sleep());
    }
}
