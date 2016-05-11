# Hubspot custom ID property sync script
 [![Travis](https://img.shields.io/travis/ryanwinchester/hubspot-custom-id.svg?maxAge=2592000?style=flat-square)](https://travis-ci.org/ryanwinchester/hubspot-custom-id)
 [![Code Climate](https://img.shields.io/codeclimate/coverage/github/ryanwinchester/hubspot-custom-id?maxAge=2592000?style=flat-square)](https://codeclimate.com/github/ryanwinchester/hubspot-custom-id/coverage)
 [![Code Climate](https://img.shields.io/codeclimate/github/ryanwinchester/hubspot-custom-id.svg?maxAge=2592000?style=flat-square)](https://codeclimate.com/github/ryanwinchester/hubspot-custom-id)


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

##### 2. Create a new smart list
You will need to create a new *smart list* that contains only contacts that `custom_id` property is `not known`.
Remember the ID of that list.

#### 3. Use it

```php
use Fungku\HubSpot\HubSpotService;
use Winchester\HubspotId\SyncService;

$hubspot_api_key = 'demo';
$hubspot_list_id = '12345';
$hubspot_property = 'custom_id';

$hubspot = HubSpotService::make($hubspot_api_key);
$sync = new SyncService($hubspot, $hubspot_property);

$sync->sync_list($hubspot_list_id);
```

#### If using a Service Provider for your framework
The Hubspot service class will look for the environment variables: `HUBSPOT_API_KEY` and
`HUBSPOT_PROPERTY`.
