# Introduction
--------------

This PHP library get the share count for URI in Social Networks, like 
Facebook, Twitter, Linkedin, Google+, ...

# Requirements
--------------

This library require:
    - PHP 5.3.3 or higher
    - sgmendez/json 1.0 or higher

# Installation
--------------

You can use [Composer](https://getcomposer.org) to use this library in 
your application.

If you don't have Composer yet, download it following the instructions on
http://getcomposer.org/ or just run the following command:

```
curl -s http://getcomposer.org/installer | php
```
And then execute this command to add libary to your project:

```
$ composer require sgmendez/socialcount
```
Or require [`sgmendez/socialcount`](http://)
into your `composer.json` file:


``` 
json
{
    "require": {
        "sgmendez/socialcount": "1.0.*"
    }
}
```

# Tests
-------
You can run the PHPUnit tests, in directory tests execute:

    $ php phpunit.phar .

In this directory there are a copy of phpunit.phar for execution test

# Examples
----------

``` 
php

$socialCount = new SocialCount();

$url = 'http://www.sgmendez.com/';

$fb = $socialCount->getCountFacebook($url);
$tw = $socialCount->getCountTwitter($url);
$li = $socialCount->getCountLinkedin($url);
$go = $socialCount->getCountGoogle($url);

echo 'FB: '.$fb.' | TW: '.$tw.' | LI: '.$li.' | GO: '.$go;
```

# License
---------
Licensed under the BSD License:

   http://opensource.org/licenses/bsd-license.php

