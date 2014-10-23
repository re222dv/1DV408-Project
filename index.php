<?php

ini_set('display_errors', '1');

require_once('config.php');
require_once('src/imports.php');

use controller\MasterController;
use Di\Injector;
use Template\ViewSettings;

try {
    $injector = new Injector();
    $injector->bindToInstance(Injector::class, $injector);
    $injector->get(ViewSettings::class)->templatePath = 'src/templates/';

    $masterController = $injector->get(MasterController::class);

    echo $masterController->render();
} catch (\Exception $e) {
    include('error.php');

    $type = get_class($e);
    $message = $e->getMessage();
    $trace = $e->getTraceAsString();

    error_log(<<<ERROR

====================================================================================
Uncaught Exception: $type
$message

StackTrace:
$trace
====================================================================================

ERROR
    );
}
