<?php

ini_set('display_errors', '1');

require_once('src/imports.php');

use controller\MasterController;
use Template\ViewSettings;

$injector = new \Di\Injector();
$injector->bindToInstance('Di\Injector', $injector);
$injector->get(ViewSettings::class)->templatePath = 'src/templates/';

$masterController = $injector->get(MasterController::class);

echo $masterController->render();
