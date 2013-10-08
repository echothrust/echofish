<?php
class ErrorlogGUI
{
    var $tpl= NULL;
    var $def= '';
    var $mod= '';
    var $category='SYSTEM';
	var $description='Internal PHP errors and warnings log management';
	var $fallbackurl=array('container' => '','module'=>'','action'=>'');
	var $SEARCH=array('errno','errstr','errline','errfile');
	var $_SEARCH=null;
    var $actionmenu= array (
        'list' => array (
            'name' => 'Application Logs',
            'title' => 'Application Logs',
            'description' => 'List application logs. Usefull for support.',
            'weight' => 1000,
            'container' => 'logs',
            'module' => '%',
            'action' => '%',
            'tcontainer' => 'logs',
            'tmodule' => 'errorlog',
            'taction' => 'index',
            'perm' => 'admin'
        )
    );	
    function __construct($smarty= NULL)
    {
		$this->tpl=$smarty;
        foreach($this->SEARCH as $val)
        	$this->_SEARCH[$val]="";
		$OURDIR=dirname(__FILE__);
		$CONTAINER=basename($OURDIR);
		$MODULE=strtolower(substr(substr(basename(__FILE__),1),0,-4));
		$this->def=$CONTAINER;
		$this->mod=$MODULE;		
        $this->fallbackurl['container']=$this->def;
        $this->fallbackurl['module']=$this->mod;
        $this->fallbackurl['action']='index';

    }

   function getSite() 
   {
 		if(has_perms('admin'))
        return array ('title' => 'PHP Error Logs');
   }
    
    function index()
    {
		if(!has_perms('admin')) gen_uri('home','home','index',true);
        $page= isset($_GET['page']) ? intval( $_GET['page']) : 0;
        $pager= new Doctrine_Pager(Doctrine_Query :: create()->from('Errorlog e')->orderBy('e.created_at ASC'), $page);
        $t= $pager->execute();
        $this->tpl->assign('PAGER', $pager);
        $this->tpl->assign('CONTENTLIST', $t);
        $this->tpl->assign('FORM', $this->_SEARCH);
        $this->tpl->assign('INLINE_BUTTON', 'Clear all error logs');
        $this->tpl->assign('INLINE_ACTION', 'massclear');
    }

    function massclear()
    {
		if(!has_perms('admin')) gen_uri('home','home','index',true);
        $pager= Doctrine_Query :: create()->delete('Errorlog s')->execute();

        goback($this->fallbackurl);

    }

    function delete()
    {
		if(!has_perms('admin')) gen_uri('home','home','index',true);
        $ID= isset ($_GET['ID']) ? intval($_GET['ID']) : 0;
        $roleTable= Doctrine_Core :: getTable('Errorlog');
        $rarps= $roleTable->find(intval($_GET['ID']));
        if ($rarps !== false)
            $rarps->delete();
        goback($this->fallbackurl);
    }
    function getActionMenu()
    {
		if(has_perms('admin'))
		return array('List Error log' => gen_uri($this->def,'errorlog','index'));
    }
    function getMenu()
    {
		if(has_perms('admin'))
    	return  array ($this->def => array('title' => $this->def, 'URL'=>gen_uri($this->def,$this->mod)));
    }

    function search()
    {
		if(!has_perms('admin')) gen_uri('home','home','index',true);
        $page= isset($_GET['page']) ? intval( $_GET['page']) : 0;
    	if(!empty($_POST)) $_SESSION['SEARCH']=$_p=_g($_POST,$this->SEARCH);
    	else if(!empty($_SESSION['SEARCH'])) $_p=$_SESSION['SEARCH'];
    	
    	$q=Doctrine_Query::create()->from( 'Errorlog e' );
    	foreach($_p as $key => $val)
    		$q->andWhere("e.$key LIKE ?");
		$pager = new Doctrine_Pager( $q,$page);
    	$results=$pager->execute(array_values($_p));
    	if($pager->getNumResults()==0)
    		goback($this->fallbackurl);
		$this->tpl->assign('PAGER',$pager);		
		$this->tpl->assign('CONTENTLIST',$results);
        $this->tpl->assign('INLINE_BUTTON', 'Clear all error logs');
        $this->tpl->assign('INLINE_ACTION', 'massclear');
		$this->tpl->assign('FORM',$_p);
    }

    function export()
    {
		if(!has_perms('admin')) gen_uri('home','home','index',true);
        $pager= Doctrine_Query :: create()->from(ucfirst($this->mod));
        $object= $pager->execute();
        $this->tpl->assign_by_ref('CONTENTLIST', $object);
    }
	function install()
	{
		$this->uninstall();
		$m=new Menu();
		$m->name='Logs';
		$m->title='Logs';
		$m->description='System and other related logs';
		$m->weight=999;
		$m->perm='admin';
		$m->container=$this->def;
		$m->module=$this->mod;
		$m->action='index';
		$m->save();
		$menus=array('delete'=>'list',
            'export' => 'thead',
            'search' => 'thead');
		$i=0;
		foreach($menus as $action => $category)
		{
        	$module=new Pageaction();
	        $module->name=ucfirst($action).' '.$this->mod;
	        $module->description=ucfirst($action).' '.$this->mod;
	        $module->title=ucfirst($action).' '.$this->mod;
	        $module->tcontainer=$this->def;
	        $module->weight=$i++;
	        $module->tmodule=$this->mod;
	        $module->taction=$action;
	        $module->category=$category;
	        $module->container=$this->def;
	        $module->module=$this->mod;
	        $module->action='%';
	        $module->save();
	        unset($module);
		}

        foreach($this->actionmenu as $key => $menuarr)
        {
        	
        	$menu= new Actionmenu();
        	$menu->fromArray($menuarr);
        	$menu->save();
        }				
		$module=new Module();
		$module->name=$this->mod;
		$module->container=$this->def;
		$module->category=$this->category;
		$module->save();
	}
	
	function uninstall()
	{
        $conn= Doctrine_Manager :: connection();
		$q = Doctrine_Query::create()->delete('Module')->where('name = ?',array(ucfirst($this->mod)))->execute();
        $conn->execute('DELETE FROM pageaction where module=? or tmodule=?', array($this->mod,$this->mod));
        
	}

}
