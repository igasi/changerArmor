<?php

require __DIR__ . '/../bootstrap.php';

use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints as Assert;
use Silex\Provider\DoctrineServiceProvider;

	// Define homepage path
	$app->match('/', function (Request $request) use ($app) {
    // Setting default values for dbs
    $default = [
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
    ];

    //Define form for config databases
    $form = $app['form.factory']->createBuilder('form', $default)
      ->add('origin_db_name')
      ->add('origin_db_user')
      ->add('origin_db_password', 'text', [
				    'required'    => false,
				])
      ->add('origin_db_port')
      ->add('origin_db_host')
      ->add('destination_db_name')
      ->add('destination_db_user')
      ->add('destination_db_password', 'text', [
				    'required'    => false,
				])
      ->add('destination_db_port')
      ->add('destination_db_host')
      ->getForm();

    $form->handleRequest($request);

    // Handler and validator for stablish connection with databases
    if ($form->isValid()) {
      $data = $form->getData();

      $app->register(new DoctrineServiceProvider(), [
	    'dbs.options' => [
        'origin' => [
            'driver'    => 'pdo_mysql',
            'host'      => $data['origin_db_host'],
            'dbname'    => $data['origin_db_name'],
            'user'      => $data['origin_db_user'],
            'password'  => $data['origin_db_password'],
            'port'  	=> $data['origin_db_port'],
            'charset'   => 'utf8mb4',
        ],
        // 'destination' => [
        //     'driver'    => 'pdo_mysql',
        //     'host'      => $data['destination_db_host'],
        //     'dbname'    => $data['destination_db_name'],
        //     'user'      => $data['destination_db_user'],
        //     'password'  => $data['destination_db_password'],
        //     'port'  	=> $data['destination_db_port'],
        //     'charset'   => 'utf8mb4',
        // ],
	    ],
		]);

       // forward to 'tableslisttomigrate'
    $subRequest = Request::create('/tableslisttomigrate', 'GET');

    return $app->handle($subRequest, HttpKernelInterface::SUB_REQUEST);
    //return new Response(var_dump($data), 200);
    }

    // display the form
    return $app['twig']
    	->render('migration_form.html.twig', [
    		'form' => $form->createView()
    	]);
	})
	->bind('homepage');


	$app->match('/tableslisttomigrate', function (Request $request) use ($app) {

		//Load tables from origin db
    $tables = $app['dbs']['origin']
    	->getSchemaManager()
    	->listTables();

    $list = [];
    foreach ($tables as $table) {
	    $list[] = $table->getName();
		}
    //return new Response(var_dump($list), 200);

    $form = $app['form.factory']->createBuilder('form', ['allow_extra_fields' => true])
    	->add('tables', 'choice', [
            'choices' => $list,
            'multiple' => true,
            'attr' => ['size' => '25'],
            'allow_extra_fields'    => true,
        ])
    	->add('submit','submit')
      ->getForm();

    $form->handleRequest($request);

    if ($form->isValid()) {
        $data = $form->getData();

        //return $app->redirect('/');
        return new Response(var_dump($data), 200);
    }

    // display the form
    return $app['twig']
    	->render('tables_form.html.twig', [
    		'form' => $form->createView()
    	]);
	})
	->bind('tableslisttomigrate');

$app->run();

