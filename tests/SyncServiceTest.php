<?php

namespace Tests;

use Focus\HubspotId\SyncService;
use Mockery as m;

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
