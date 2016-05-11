<?php

namespace Tests;

use Fungku\HubSpot\HubSpotService;
use Mockery as m;

/**
 * Class HubspotSpy
 * yeah... named constructor...
 */
class HubspotSpy extends HubSpotService
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
