# Hubspot custom ID property sync script
 [![Travis](https://travis-ci.org/ryanwinchester/hubspot-custom-id.svg?branch=master)](https://travis-ci.org/ryanwinchester/hubspot-custom-id)
 [![Test Coverage](https://codeclimate.com/github/ryanwinchester/hubspot-custom-id/badges/coverage.svg)](https://codeclimate.com/github/ryanwinchester/hubspot-custom-id/coverage)
 [![Code Climate](https://codeclimate.com/github/ryanwinchester/hubspot-custom-id/badges/gpa.svg)](https://codeclimate.com/github/ryanwinchester/hubspot-custom-id)


You find you need to do anything fancy but there is no way to uniquely identify users apart from their
email address? Well here is a hokey-pokey work-around.

What this does is just ***base64 encodes*** the `vid` property to a custom property that we can actually use.
I don't like the idea of directly exposing the vid. So this obfuscates it slightly. However, it is still easy
to decode in pretty much any programming language as far as I am aware of.

***One example of a use case*** is adding it as query string in a url from a link in an email.
We can then use it to identify the user that clicked the email on the target page.

### Install

```
composer require ryanwinchester/hubspot-custom-id
```

### Usage

##### 1. Create a new contact property for the custom ID
Name it something like `custom_id`, and make it a **single line text** field.

![Custom property](https://s3-us-west-2.amazonaws.com/ryanwinchester/code/hubspot-custom-id/custom-property.png)

##### 2. Create a new smart list
You will need to create a new *smart list* that contains only contacts where the `custom_id` property is `unknown`.
Remember the ID of that list. (*It should be the number as the last component of the url when editing the list*)

![list url](https://s3-us-west-2.amazonaws.com/ryanwinchester/code/hubspot-custom-id/list-url.png)

##### 3. Use it

```php
use Fungku\HubSpot\HubSpotService;
use Winchester\HubspotId\SyncService;

$hubspot_api_key = 'demo';
$hubspot_list_id = '12345';
$hubspot_property = 'custom_id';

$hubspot = HubSpotService::make($hubspot_api_key);
$sync = new SyncService($hubspot, $hubspot_property);

$sync->syncList($hubspot_list_id);
```

##### 4. Make a workflow (optional)
If you don't want it to run just as something like a cron job, or manually. Then you can deploy
it to a server somewhere and create a workflow to access your script as a webhook.

### Credits

- I got the idea from the instructions at http://hubhacker.com/use-hubspot-vid-as-a-contact-property/ although I did not watch the video or download the script.
- The [original idea thread](http://ideas.hubspot.com/forums/76407-general-hubspot-ideas/suggestions/6243558-assign-unique-contact-id-or-use-the-vid-that-alre) on the Hubspot forums.
