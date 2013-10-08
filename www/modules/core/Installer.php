<?php
class Installer {
	var $modules;
	
	function __construct()
	{
		$this->modules=Doctrine_Core::getTable('Module');
	}
	
	public function installed($details)
	{
		if($details['category']=='SYSTEM') return true;
		$name=$details['name'];
		$res=$this->modules->findByName($name);
		if($res->toArray()==null)
			return false;
		return true;
	}


	public function installed_by_name($name)
	{
		$res=$this->modules->findByName($name);
		if($res->toArray()==null)
			return false;
		return true;
	}
}
