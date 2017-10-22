GUS API Client
==============

Travis build on master branch ![Travis build on master](https://travis-ci.org/MWojtowicz/gusclient.svg?branch=master)

# 1. Installation

`composer require mwojtowicz/gusclient`

# 2. Usage

```php
<?php
$client = new MWojtowicz\GusClient\Client(<GUS_API_USER_KEY>);

$company   = $client->findByNip('1234567890');
$companies = $client->findByNip(array('1234567890', '9876054321'));

$company   = $client->findByRegon('1234567890');
$companies = $client->findByRegon(array('1234567890', '9876054321'));

$company   = $client->findByKrs('1234567890');
$companies = $client->findByKrs(array('1234567890', '9876054321'));
```
