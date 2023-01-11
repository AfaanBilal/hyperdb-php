HyperDB PHP Client
=================

Author: **[Afaan Bilal](https://afaan.dev)**

## Introduction
**HyperDB PHP** is a PHP client package for the [HyperDB server](https://github->com/AfaanBilal/hyperdb).

## Installation
````
composer require afaanbilal/hyperdb-php
````

## Example usage
````php
<?php

require "vendor/autoload.php";

use AfaanBilal\HyperDB;

// Setup with address (default: http://localhost:8765)
$hyperdb = new HyperDB('http://localhost:8765');

// Ping the server
$r = $hyperdb->ping();
var_dump($r); // bool(true)

// Get the version number
$r = $hyperdb->version();
var_dump($r); // string(36) "[HyperDB v0.1.0 (https://afaan.dev)]"

// Set a value
$r = $hyperdb->set("test", "value");
var_dump($r); // string(5) "value"

// Check if a key is present
$r = $hyperdb->has("test");
var_dump($r); // bool(true)

// Get a value
$r = $hyperdb->get("test");
var_dump($r); // string(5) "value"

// Get all stored data
$r = $hyperdb->all();
var_dump($r); // array(1) { ["test"] => string(5) "value" }

// Remove a key
$r = $hyperdb->delete("test");
var_dump($r); // bool(true)

// Delete all stored data
$r = $hyperdb->clear();
var_dump($r); // bool(true)

// Check if the store is empty
$r = $hyperdb->empty();
var_dump($r); // bool(true)

// Persist the store to disk
$r = $hyperdb->save();
var_dump($r); // bool(true)

// Reload the store from disk
$r = $hyperdb->reload();
var_dump($r); // bool(true)

// Delete all store data from memory and disk
$r = $hyperdb->reset();
var_dump($r); // bool(true)
````

## Contributing
All contributions are welcome. Please create an issue first for any feature request
or bug. Then fork the repository, create a branch and make any changes to fix the bug
or add the feature and create a pull request. That's it!
Thanks!

## License
**HyperDB PHP** is released under the MIT License.
Check out the full license [here](LICENSE).
