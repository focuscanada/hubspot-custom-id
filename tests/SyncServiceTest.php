<?php

namespace Tests;

use Mockery as m;
use Winchester\HubspotId\SyncService;

class SyncServiceTest extends \PHPUnit_Framework_TestCase
{
    function tearDown()
    {
        m::close();
    }

    /** @test */
    function list_sync()
    {
        $sync = new SyncService(new HubspotSpy(), 'custom_id');

        $this->assertEquals(3, $sync->syncList('123456'));
    }

    /** @test */
    function contacts_sync()
    {
        $hubspot = new HubspotSpy();
        $sync = new SyncService($hubspot, 'custom_id');

        $this->assertEquals(3, $sync->syncContacts($hubspot->contacts));
    }
}

/**
 * Class HubspotSpy
 * yeah... named constructor...
 */
class HubspotSpy extends \Fungku\HubSpot\HubSpotService
{
    public $contact;
    public $contacts;

    function __construct()
    {
        $this->contact = new \stdClass();
        $this->contact->vid = "1234";
        $this->contacts = [$this->contact, $this->contact, $this->contact];
        parent::__construct('demo');
    }

    function contacts()
    {
        $contacts = m::mock('Fungku\HubSpot\Api\Contacts');
        $response = m::mock('Fungku\HubSpot\Http\Response');
        $contacts->shouldReceive('createOrUpdateBatch')->once()->andReturn($response);
        return $contacts;
    }

    function contactLists()
    {
        $lists = m::mock('Fungku\HubSpot\Api\ContactLists');
        $response = m::mock('Fungku\HubSpot\Http\Response');
        $response->contacts = $this->contacts;
        $response->{'has-more'} = false;
        $response->{'vid-offset'} = 1234;
        $lists->shouldReceive('contacts')->once()->andReturn($response);
        return $lists;
    }
}
