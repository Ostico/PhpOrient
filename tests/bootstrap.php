<?php
error_reporting( -1 );
date_default_timezone_set( 'UTC' );
echo "PHP Version: " . PHP_VERSION . PHP_EOL;
$loader = require __DIR__ . "/../vendor/autoload.php";
$loader->addPsr4( 'PhpOrient\\', __DIR__ . '/PhpOrient' );
$loader->addPsr4( 'PhpOrient\\', __DIR__ . "/../src/PhpOrient" );
