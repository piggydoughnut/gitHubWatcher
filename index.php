<?php

$app = require('vendor/bcosca/fatfree/lib/base.php');

// App config
$app->config('config/config.ini');

// Define routes
$app->config('config/router.ini');


$app->set('ONERROR',function(){
	echo \Template::instance()->render('error.htm');
});

// Execute application
$app->run();
