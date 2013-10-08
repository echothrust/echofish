#!/usr/local/bin/php
<?php
function usage()
{
	printf("USAGE: %s -y /path/to/yml/ [-m /path/to/models]\n",basename(__FILE__));
	die();
}
require_once ('../www/includes/config.php');
require_once ('../www/includes/init.php');
$options = getopt("m:y:");
if($options==NULL || !isset($options['y']))
	usage();

$YPATH=$options['y'];

if(!is_dir($YPATH))
{
	echo "$YPATH: is not a directory\n";
	usage();
}
if(isset($options['m']) && is_dir($options['m']))
	$MODELS=$options['m'];
else
	$MODELS=MODULES_DIR . 'models';
spl_autoload_register(array ('Doctrine','autoload'));
$manager= Doctrine_Manager :: getInstance();
$conn= Doctrine_Manager :: connection($DSN);
$conn->setAttribute(Doctrine_Core::ATTR_USE_NATIVE_ENUM, true);
$conn->setCharset('utf8');

echo "Generate models/generated from YAML.\n";
Doctrine_Core :: generateModelsFromYaml($YPATH, $MODELS);
