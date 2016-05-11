<?php

namespace Winchester\HubspotId;

use Fungku\HubSpot\HubSpotService;

class SyncService
{
    private $hubspot;
    private $property;

    /**
     * @param HubSpotService $hubspot
     * @param string $property The name of the custom ID property
     */
    function __construct(HubSpotService $hubspot, $property)
    {
        $this->hubspot = $hubspot;
        $this->property = $property;
    }

    /**
     * Sync the IDs of contacts from a list.
     *
     * @param int $list_id The Hubspot list id
     * @param int $count The number of contacts to sync at a time
     * @param int $vid_offset The offset to start with
     * @return int The number of contacts affected
     */
    function syncList($list_id, $count = 100, $vid_offset = 0)
    {
        $updated = 0;
        $has_more = true;

        while ($has_more) {
            $list = $this->getList($list_id, $count, $vid_offset);
            $has_more = $list->{'has-more'};
            $vid_offset = $list->{'vid-offset'};
            $updated += $this->syncContacts($list->contacts);
        }

        return $updated;
    }

    /**
     * Sync the IDs of contacts.
     *
     * @param array $contacts The contacts to sync
     * @return int The number of contacts affected
     */
    function syncContacts($contacts = [])
    {
        $contacts = array_map(function ($contact) {
            return [
                "vid" => $contact->vid,
                "properties" => [
                    [
                        "property" => $this->property,
                        "value" => base64_encode($contact->vid),
                    ],
                ],
            ];
        }, $contacts);

        $this->updateContacts($contacts);

        return count($contacts);
    }

    /**
     * Get the list from Hubspot.
     *
     * @param int $list_id
     * @param int $count
     * @param int $vid_offset
     * @return \Fungku\HubSpot\Http\Response
     */
    function getList($list_id, $count = 100, $vid_offset = 0)
    {
        return $this->hubspot->contactLists()->contacts($list_id, [
            "count" => $count,
            "vidOffset" => $vid_offset,
        ]);
    }

    /**
     * Update the contacts in Hubspot.
     *
     * @param array $contacts
     */
    function updateContacts($contacts = [])
    {
        $this->hubspot->contacts()->createOrUpdateBatch($contacts);
    }
}
