<?php
class WebUser extends CWebUser {
	// Store model to not repeat query.
	private $_model;

	// Load user model.
	function getUser_Model(){
		$user=$this->loadUser($this->id);
		return $user;
	}

	public function isAdmin()
	{
		$user=$this->loadUser($this->id);
		return $user && $user->superuser==1;
	}

	public function getIsAdmin()
	{
		return $this->isAdmin();
	}

	protected function loadUser($id=null)
	{
		if($this->_model===null)
		{
			if($id!==null)
				$this->_model=User::model()->findByPk($id);
			else return null;
		}
		return $this->_model;
	}

	public function addFlash($key,$value) {
		if($this->hasFlash($key)) {
			$actualVal = $this->getFlash($key);
			$array = is_array($actualVal) ? $actualVal : array($actualVal);
			$array[] = $value;
			$this->setFlash($key, $array);
		} else $this->setFlash($key, $value);
	}
}
?>