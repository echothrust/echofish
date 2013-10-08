<?php
@mkdir(SMARTY_COMPILE_DIR);
@mkdir(SMARTY_COMPILE_DIR."/cache");
$os = strtolower(PHP_OS);
if(strpos($os, "win") === false)
  define('DELIMITER',':');
else
  define('DELIMITER',';');

define('INCLUDE_DIR',dirname(__FILE__));
define('BASE_DIR',dirname(INCLUDE_DIR));
define('MODULES_DIR',BASE_DIR.'/modules/');
define('THEMES_DIR',BASE_DIR.'/htdocs/themes/');
define('LOCALE_DIR',BASE_DIR.'/locale');
define('INTERFACE_DIR',MODULES_DIR.'/interface/');
define('CORE_DIR',MODULES_DIR.'/core/');
include_once 'Smarty.class.php';

$CONFIG=NULL;
$MODULE_URL=NULL;
$LOGGED=FALSE;
$ADMIN=FALSE;
$MODERATOR=FALSE;
$CACHE_ID=NULL;
require_once(CORE_DIR. '/doctrine/Doctrine.php');
require_once(CORE_DIR.'/Installer.php');
$PATH=sprintf("%s:%s:%s:%s",INCLUDE_DIR,BASE_DIR,MODULES_DIR,CORE_DIR);
global $DSN,$manager,$conn,$PERMS;
$DSN  = sprintf('mysql://%s:%s@%s/%s',DBUSER,DBPASS,DBHOST,DBNAME);
$PERMS=array('admin'=>4,'writer'=>3,'viewer'=>2,'guest'=>1); 
