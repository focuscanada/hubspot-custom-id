<?php

namespace Winchester\HubspotId;

use Fungku\HubSpot\HubSpotService;

class SyncService
{
    private $hubspot;
    private $id_property;

    /**
     * @param HubSpotService $hubspot
     * @param string $id_property The name of the custom ID property
     */
    function __construct(HubSpotService $hubspot, $id_property)
    {
        $this->hubspot = $hubspot;
        $this->id_property;
    }

    /**
     * Sync the IDs of contacts from a list.
     *
     * @param int $list_id The Hubspot list id
     * @return int The number of contacts affected
     */
    function sync_list($list_id)
    {
        $updated = 0;
        $count = 100;
        $vid_offset = 0;
        $has_more = true;

        while ($has_more) {
            $list = $this->get_list($list_id, $count, $vid_offset);
            $has_more = $list->{'has-more'};
            $vid_offset = $list->{'vid-offset'};
            $updated += $this->sync_contacts($list->contacts);
        }

        return $updated;
    }

    /**
     * Sync the IDs of contacts.
     *
     * @param array $contacts The contacts to sync
     * @return int The number of contacts affected
     */
    function sync_contacts($contacts = [])
    {
        $contacts = array_map(function ($contact) {
            return [
                "vid" => $contact->vid,
                "properties" => [
                    [
                        "property" => $this->id_property,
                        "value" => base64_encode($contact->vid),
                    ],
                ],
            ];
        }, $contacts);

        $this->update_contacts($contacts);

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
    function get_list($list_id, $count = 100, $vid_offset = 0)
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
    function update_contacts($contacts = [])
    {
        $this->hubspot->contacts()->createOrUpdateBatch($contacts);
    }
}
