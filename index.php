<?php

$app = require('vendor/bcosca/fatfree/lib/base.php');

// App config
$app->config('config/config.ini');

// Define routes
$app->config('config/router.ini');

// Execute application
$app->run();
