<?php

require __DIR__ . '/vendor/autoload.php';

use Silex\Provider\TwigServiceProvider;
use Silex\Provider\ValidatorServiceProvider;
use Silex\Provider\TranslationServiceProvider;
use Silex\Provider\FormServiceProvider;



$app = new Silex\Application();

// Disable this setting in production
$app['debug'] = true; 
/*
// Define folter for general files
$app['files']=__DIR__ . '/files';

// Define folder for logs of the executions 
$app['files_logs']=__DIR__ . '/files/logs';

// Define folder for data dump from database
// Data dump are alone tables of the database
$app['files_datadump']=__DIR__ . '/files/datadump';
*/

$app->register(new ValidatorServiceProvider());

$app->register(new TranslationServiceProvider(), array(
    'translator.domains' => array(),
));

// Register Form Service provider
$app->register(new FormServiceProvider());


// Register twig service provider
$app->register(new TwigServiceProvider(), array(
    'twig.path' => __DIR__ . '/views',
));
