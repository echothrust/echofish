<?php
// $Id
global $DSN;
spl_autoload_register(array('Doctrine', 'autoload'));
$manager = Doctrine_Manager::getInstance();
date_default_timezone_set('Europe/Athens');
$conn = Doctrine_Manager::connection($DSN);
$conn->setCharset('utf8');
// Force usage of Internal DBMS enum datatype
$conn->setAttribute(Doctrine_Core::ATTR_USE_NATIVE_ENUM, true);
$conn->setAttribute(Doctrine_Core::ATTR_QUOTE_IDENTIFIER, true);
// Needed for password override
$manager->setAttribute(Doctrine_Core::ATTR_AUTO_ACCESSOR_OVERRIDE, true);
$manager->setAttribute(Doctrine_Core::ATTR_AUTOLOAD_TABLE_CLASSES, true);

$manager->setAttribute(Doctrine_Core::ATTR_USE_DQL_CALLBACKS, true);
$manager->setAttribute(Doctrine_Core::ATTR_EXPORT, Doctrine_Core::EXPORT_ALL);
Doctrine::loadModels(MODULES_DIR.'models/generated'); 
Doctrine::loadModels(MODULES_DIR.'models'); 
