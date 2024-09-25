<?php
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Translation\FileLoader;
use Illuminate\Translation\Translator;
use Illuminate\Validation\Factory as ValidatorFactory;

$capsule = new Capsule;
$capsule->addConnection($settings['db_empleabilidad']);
$capsule->addConnection($settings['db_seg'], 'db_seg');
//$capsule->addConnection($settings['db_prog'], 'db_prog');
$capsule->addConnection($settings['db_trans'], 'db_trans');
$capsule->addConnection($settings['db_empleabilidad'], 'db_empleabilidad');

// Make this Capsule instance available globally via static methods... (optional)
$capsule->setAsGlobal();

// Setup the Eloquent ORM... (optional; unless you've used setEventDispatcher())
$capsule->bootEloquent();

// Configuración del traductor y el validador
$filesystem = new Filesystem();
$loader = new FileLoader($filesystem, __DIR__ . '/../src/Root/lang');
$translator = new Translator($loader, 'es');
$validatorFactory = new ValidatorFactory($translator);
$presenceVerifier = new \Illuminate\Validation\DatabasePresenceVerifier($capsule->getDatabaseManager());
$validatorFactory->setPresenceVerifier($presenceVerifier);
$container->set('validator', $validatorFactory);