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
}
?>