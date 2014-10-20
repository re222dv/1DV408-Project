<?php

ini_set('display_errors', '1');

require_once('src/imports.php');

use controller\MasterController;
use Di\Injector;
use Template\ViewSettings;

$injector = new Injector();
$injector->bindToInstance(Injector::class, $injector);
$injector->get(ViewSettings::class)->templatePath = 'src/templates/';

$masterController = $injector->get(MasterController::class);

echo $masterController->render();
