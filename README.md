GUS API Client
==============

# 1. Installation

`composer require mwojtowicz/gusclient`

# 2. Usage

```
$client = new MWojtowicz\GusClient\Client(GUS_API_USER_KEY, DEATHBYCAPTCHA_USERNAME, DEATHBYCAPTCHA_PASSWORD);

$company   = $client->findByNip('1234567890');
$companies = $client->findByNip(array('1234567890', '9876054321'));

$company   = $client->findByRegon('1234567890');
$companies = $client->findByRegon(array('1234567890', '9876054321'));

$company   = $client->findByKrs('1234567890');
$companies = $client->findByKrs(array('1234567890', '9876054321'));
```