GUS API Client
==============

[![Latest Stable Version](https://poser.pugx.org/mwojtowicz/gusclient/v/stable)](https://packagist.org/packages/mwojtowicz/gusclient)
![Travis build on master](https://travis-ci.org/MWojtowicz/gusclient.svg?branch=master)
[![Coverage Status](https://coveralls.io/repos/github/MWojtowicz/gusclient/badge.svg?branch=master)](https://coveralls.io/github/MWojtowicz/gusclient?branch=master)
[![Total Downloads](https://poser.pugx.org/mwojtowicz/gusclient/downloads)](https://packagist.org/packages/mwojtowicz/gusclient)
[![License](https://poser.pugx.org/mwojtowicz/gusclient/license)](https://packagist.org/packages/mwojtowicz/gusclient)

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
