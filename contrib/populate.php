<?php
require_once('../www/includes/config.php');
require_once('../www/includes/init.php');
spl_autoload_register(array('Doctrine', 'autoload'));
$manager = Doctrine_Manager::getInstance();
$conn = Doctrine_Manager::connection($DSN);
$conn->setCharset('utf8');

$manager->setAttribute(Doctrine_Core::ATTR_AUTO_ACCESSOR_OVERRIDE, true);
$manager->setAttribute(Doctrine_Core::ATTR_AUTOLOAD_TABLE_CLASSES, true);
Doctrine::loadModels(MODULES_DIR.'models/generated'); 
Doctrine::loadModels(MODULES_DIR.'models'); 
Doctrine::loadModels(CORE_DIR); 
Doctrine::loadModels(INTERFACE_DIR);

/*
 * Initial modules that are part of the SYSTEM. These modules are always persent
 * and they cannot be enabled/disabled. These modules dont carry an 
 * installation/uninstallation procedure either.
 */ 
$mymodules['home']=array('Home');
$mymodules['admin']=array('Language','Configuration','Group','Translation','User','Tag','Searchbookmark','Ip','Query');
$mymodules['reporting']=array('Report');
$mymodules['logs']=array('Errorlog','Syslog','Archive','Whitelist');
foreach ($mymodules as $container => $modules)
{
	foreach($modules as $module)
	{
		$mname=$module.'Gui';
		$mod=new $mname;
		$mod->install();
		unset($mod); 
		Doctrine::loadModels(MODULES_DIR.'models/generated'); 
		Doctrine::loadModels(MODULES_DIR.'models');
		
	}
}

$user= new User();

$GRZ=array('viewer','writer','guest','admin');
foreach($GRZ as $gr)
{
	$group= new Group();
	$group->name=$gr;
	$group->perm=$gr;
	$group->save();
}

$user->username="sysadmin";
$user->password="sysadmin";
$user->firstname="Systems";
$user->lastname="Administrator";
$user->save();
$user->UserGroup[0]->group_id=$group->id;
$user->UserGroup[0]->user_id=$user->id;
$user->save();
$user->free(true);

$config=new Configuration();
$config->name='Default Config';
$config->is_default=true;
$config->records_per_page=100;
$config->theme='myoxygen';
$config->save();

$language=new Language();
$language->name='English';
$language->code='en_GB';
$language->save();

$language=new Language();
$language->name='Greek';
$language->code='el_GR';
$language->save();

// Avoid left over smarty compilations with wrong permissions. 
system('rm -rf '.SMARTY_COMPILE_DIR. ' 2>&1 >/dev/null');