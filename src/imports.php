<?php

require_once('src/lib/Di/di.php');
require_once('src/lib/Template/template.php');

require_once('src/controller/ClassDiagramController.php');
require_once('src/controller/InputController.php');
require_once('src/controller/MasterController.php');
require_once('src/model/entities/umls/ClassDiagram.php');
require_once('src/model/entities/umls/Association.php');
require_once('src/model/entities/umls/ClassObject.php');
require_once('src/model/entities/umls/Method.php');
require_once('src/model/entities/umls/Variable.php');
require_once('src/view/InputView.php');
require_once('src/view/MasterView.php');
require_once('src/view/umls/ClassDiagramView.php');
require_once('src/view/umls/AssociationView.php');
require_once('src/view/umls/ClassObjectView.php');
require_once('src/view/umls/MethodView.php');
require_once('src/view/umls/VariableView.php');
require_once('src/view/services/Router.php');
