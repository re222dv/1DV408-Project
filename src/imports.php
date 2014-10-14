<?php

require_once('src/lib/Di/di.php');
require_once('src/lib/Template/template.php');

require_once('src/controller/ClassDiagramController.php');
require_once('src/controller/InputController.php');
require_once('src/controller/MasterController.php');
require_once('src/model/diagrams/ClassDiagram.php');
require_once('src/model/entities/Association.php');
require_once('src/model/entities/ClassObject.php');
require_once('src/model/entities/Method.php');
require_once('src/model/entities/Variable.php');
require_once('src/model/mesh/Connection.php');
require_once('src/model/mesh/Node.php');
require_once('src/model/mesh/Network.php');
require_once('src/view/InputView.php');
require_once('src/view/MasterView.php');
require_once('src/view/diagrams/ClassDiagramView.php');
require_once('src/view/entities/AssociationView.php');
require_once('src/view/entities/ClassObjectView.php');
require_once('src/view/entities/MethodView.php');
require_once('src/view/entities/VariableView.php');
require_once('src/view/mesh/PartialTreeView.php');
require_once('src/view/mesh/NetworkView.php');
require_once('src/view/services/Router.php');
