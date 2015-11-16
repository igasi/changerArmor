<?php

require __DIR__ . '/../bootstrap.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints as Assert;

// Declare our primary action
/*$app->get( '/', function() use ($app) {
    return "hello";//$app['twig']->render('migration_form.html.twig');
})
	->bind("dbs_form");*/

$app->match('/', function (Request $request) use ($app) {
    // some default data for when the form is displayed the first time
    $default = array(
        'origin_db_name' => 'dbname',
        'origin_db_user' => 'dbuser',
        'origin_db_password' => 'dbpassword',
        'origin_db_port' => '13306',
        'origin_db_host' => 'localhost',
        'destination_db_name' => 'dbname2',
        'destination_db_user' => 'dbuser2',
        'destination_db_password' => 'dbpassword2',
        'destination_db_port' => '13306',
        'destination_db_host' => 'localhost',
    );

    $form = $app['form.factory']->createBuilder('form', $default)
        ->add('origin_db_name')
        ->add('origin_db_user')
        ->add('origin_db_password')
        ->add('origin_db_port')
        ->add('origin_db_host')
        ->add('destination_db_name')
        ->add('destination_db_user')
        ->add('destination_db_password')
        ->add('destination_db_port')
        ->add('destination_db_host')
        ->getForm();

    $form->handleRequest($request);

    if ($form->isValid()) {
        $data = $form->getData();

        // do something with the data

        // redirect somewhere
        //return $app->redirect('/');
        return new Response(var_dump($data), 200);
    }

    // display the form
    return $app['twig']->render('migration_form.html.twig', array('form' => $form->createView()));
});


$app->run();

