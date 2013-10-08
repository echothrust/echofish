<?php
require_once ('../www/includes/config.php');
require_once('../www/includes/init.php');
spl_autoload_register(array (
    'Doctrine',
    'autoload'
));
$manager= Doctrine_Manager :: getInstance();
$conn= Doctrine_Manager :: connection($DSN);
$conn->setAttribute(Doctrine_Core::ATTR_USE_NATIVE_ENUM, true);
$conn->setCharset('utf8');

echo "Droping database " . DBNAME.".\n";
Doctrine_Core::dropDatabases();
echo "Creating database " . DBNAME.".\n";
Doctrine_Core :: createDatabases();
Doctrine :: loadModels(MODULES_DIR . 'models/generated');
Doctrine :: loadModels(MODULES_DIR . 'models');
echo "Create tables from arrays.\n";
$echolib_modules=array('Actionmenu','Configuration','Errorlog','Group','Language','Menu','Module','Online','Pageaction','Translation','User','UserGroup','Tag','Searchbookmark','SearchbookmarkTag');
Doctrine :: createTablesFromArray($echolib_modules);
//Doctrine_Core :: createTablesFromModels(MODULES_DIR . 'models');
echo "Importing SQL files.\n";
foreach (glob("schemas/*.sql") as $filename) {
    echo "$filename \n";
	$QUERY=file_get_contents($filename);
	$conn->execute($QUERY);
}