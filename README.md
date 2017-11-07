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

Each client is instance of `GusClientInterface`, which has one method `find`.
Find method accepts as input strings or array of strings.

Before using this library you have to register your user key.
After you'll be able to put it as constructor argument.

You can either pass this key as environment variable `GUSAPI_KEY`.
If you won't pass it as constructor argument (it's optional) library will look at environment variable.

Code usage:

```php
<?php
$nipClient = new MWojtowicz\GusClient\NIPClient(<GUS_API_USER_KEY>);
$regonClient = new MWojtowicz\GusClient\RegonClient(<GUS_API_USER_KEY>);
$krsClient = new MWojtowicz\GusClient\KrsClient(<GUS_API_USER_KEY>);

$company   = $nipClient->find('1234567890');
$companies = $nipClient->find(['1234567890', '9876054321']);

$company   = $regonClient->find('1234567890');
$companies = $regonClient->find(['1234567890', '9876054321']);

$company   = $krsClient->find('1234567890');
$companies = $krsClient->find(['1234567890', '9876054321']);
```
