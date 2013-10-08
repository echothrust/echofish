<?php
/*
 * @file config.php
 * This is the basic configuration file.
 * Although most of the values can be changed manualy
 * the configuration file has a self "configuration" meaning
 * Most of the values are configured relatively to the config.php
 * location into the filesystem.
 * 
 * If the default locations are kept then most of the values can be
 * self configured without a problem.
 *
 * @author Pantelis Roditis
 * @version $Revision: 1.6.2.1 $
 * @date $Date: 2011/09/02 12:03:31 $
 * $Id: config.php,v 1.6.2.1 2011/09/02 12:03:31 proditis Exp $
 */
define('DBHOST','localhost');
define('DBUSER','root');
define('DBPASS','');
define('DBNAME','echofish');
define('APP_NAME','Echofish ($Revision: 1.6.2.1 $)');
define('SMARTY_COMPILE_DIR','/tmp/'.DBNAME.'_c');
define('DEFAULT_LANGUAGE','en_GB');
define('DEFAULT_LANGUAGE_ID',1);

define('DEFAULT_THEME',"myoxygen");
// General DEBUGING for echolib
define('DEBUG',true);
// Debug SMARTY ?
define('SMARTY_DEBUG',false);
// Enable TDEBUG to debug translations 
define('TDEBUG',true);
