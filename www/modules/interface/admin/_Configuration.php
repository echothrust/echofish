<?php

/*
 * $Id: _Configuration.php,v 1.8 2011/08/31 17:10:17 proditis Exp $ 
 */
class ConfigurationGUI
{
    var $def= "admin";
    var $mod= 'configuration';
    var $description='This configuration module';
    var $tpl= NULL;
	var $fallbackurl=array('container' => '','module'=>'','action'=>'');
    
    var $SEARCH= array (
        'name'
    );
    var $category= 'SYSTEM';

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

    function edit()
    {
		if(!has_perms('admin')) gen_uri('home','home','index',true);
        $RT= Doctrine_Core :: getTable('Configuration');
        if (empty ($_POST))
        {
            $conf= $RT->find(1);
            if ($conf !== false)
            {
                $this->tpl->assign('FORM', $conf->toArray());
            }
        } else
        {
            if (@ $_POST['name'] != '' && $_POST['theme']!='')
            {
                $user= $RT->find(1);
                if ($user == false) $user= new Configuration();
                $user->is_default= true;
                $user->name= $_POST['name'];
                $user->records_per_page= $_POST['records_per_page'];
                $user->theme= $_POST['theme'];
                $user->save();
                $this->process_modules();
                gen_uri($this->def, $this->mod, 'index', true);
            } else
                $this->tpl->assign('FORM', $_POST);
        }
        $this->tpl->assign_by_ref('Installer', new Installer());
        $this->tpl->assign('MODULES', $this->fetch_all_modules());

    }

    private function fetch_all_modules()
    {
        if ($dh= opendir(INTERFACE_DIR))
        {
            while (($entry= readdir($dh)) !== false)
                if ($entry != '.' && $entry != '..' && $entry != 'CVS' && is_dir(INTERFACE_DIR . "/$entry"))
                    $MODULES[$entry]= _load_modules(INTERFACE_DIR ."/$entry");
            closedir($dh);
        }
        $FSMODS= null;
        foreach ($MODULES as $container => $cmods)
            foreach ($cmods as $key => $modname)
            {
                $mod= new $modname ();
                $FSMODS[]= array (
                    'name' => $mod->mod,
                    'container' => $container,
                    'category' => $mod->category,
                    'description' => $mod->description
                );
            }
        return $FSMODS;
    }

	private function process_modules()
	{
		if(!isset($_POST['module'])) return; 
		$modules=$_POST['module'];
		if($modules==null) return;
		$installer=new Installer();
		foreach($modules as $container => $modarr)
		foreach($modarr as $name => $val)
		{
			require_once(INTERFACE_DIR."/$container/_".ucfirst($name).'.php');
			switch($val)
			{
			case 'No':
				if($installer->installed_by_name($name))
				{
					$modname=$name.'GUI';
					$mod=new $modname();
					$mod->uninstall();
				}

				break;
			default:
				if(!$installer->installed_by_name($name))
				{
					$modname=$name.'GUI';
					$mod=new $modname();
					$mod->install();
				}
			}
		}
	}
	
    function getActionMenu()
    {
    }

    function getMenu()
    {
    }

    function getSite()
    {
		if(has_perms('admin'))
        return array (
            'title' => 'Configurations Management'
        );
    }

    function index()
    {
        gen_uri($this->def, $this->mod, 'edit', true);

    }

    function search()
    {
		if(!has_perms('admin')) gen_uri('home','home','index',true);
        $page= isset ($_GET['page']) ? intval($_GET['page']) : 0;
        if (!empty ($_POST))
            $_SESSION['SEARCH']= $_p= _g($_POST, array (
                'name'
            ));
        else
            if (!empty ($_SESSION['SEARCH']))
                $_p= $_SESSION['SEARCH'];

        $q= Doctrine_Query :: create()->from('Configuration g');
        foreach ($_p as $key => $val)
            $q->andWhere("g.$key LIKE ?");
        $pager= new Doctrine_Pager($q, $page);
        $results= $pager->execute(array_values($_p));
        $this->tpl->assign('PAGER', $pager);
        $this->tpl->assign('CONTENTLIST', $results);
        $this->tpl->assign('INLINE_BUTTON', 'New Configuration');
        $this->tpl->assign('INLINE_ACTION', 'add');
        $this->tpl->assign('FORM', $_p);
    }
	function install()
	{
		$this->uninstall();
		$action=new Actionmenu();
		$action->name='Configure';
		$action->title='Configure the system';
		$action->description='Edit the system configuration parameters';
		$action->weight=1000;
		$action->container=$this->def;
		$action->module='%';
		$action->action='%';
		$action->tcontainer=$this->def;
		$action->tmodule=$this->mod;
		$action->taction='edit';
		$action->save();
		$module=new Module();
		$module->name=$this->mod;
		$module->container=$this->def;
		$module->category=$this->category;
		$module->save();
		$menu=new Menu();
		$menu->name='Admin';
		$menu->title='Administrators Panel';
		$menu->description='Administration related panel.';
		$menu->weight=100;
		$menu->container=$this->def;
		$menu->module=$this->mod;
		$menu->action='edit';
		$menu->perm='admin';
		$menu->save();
	}
	
	function uninstall()
	{
        $conn= Doctrine_Manager :: connection();
		$q = Doctrine_Query::create()->delete('Module')->where('name = ?',array(ucfirst($this->mod)))->execute();
        $conn->execute('DELETE FROM pageaction where module=? or tmodule=?', array($this->mod,$this->mod));
        
	}
    function import()
    {
        if (!has_perms('admin'))
            goback($this->fallback);
        if (!empty ($_FILES) && !empty ($_POST))
        {
            if ($_FILES["xml_file"]["error"] <= 0)
                $str_xml= file_get_contents($_FILES['xml_file']['tmp_name']);
            else
                $str_xml= null;
            if ($str_xml != null)
            {
                $xml= simplexml_load_string($str_xml);
                foreach($xml as $xml_item)
                {
                	$user=new User();
                	$user->id=$xml_item->id;
                	$user->username=$xml_item->username;
                	$user->rawpassword=$xml_item->password;
                	$user->email=$xml_item->email;
                	$user->telephone=$xml_item->telephone;
                	$user->firstname=$xml_item->firstname;
                	$user->lastname=$xml_item->lastname;
                	$user->created_at=$xml_item->created_at;
                	$user->updated_at=$xml_item->updated_at;
                	$user->replace();
                }
            }
        }
    }

}