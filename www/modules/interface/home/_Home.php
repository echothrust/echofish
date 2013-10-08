<?php


/*
 * $Header: /cvs/echolib/echolib/www/modules/interface/home/_Home.php,v 1.6 2011/08/30 16:30:47 proditis Exp $ 
 */
class HomeGUI
{
    var $tpl= NULL;
    var $def= "home";
    var $mod= 'home';
    var $modname= NULL;
    var $category= 'SYSTEM';
    var $fallbackurl= array (
        'container' => '',
        'module' => '',
        'action' => ''
    );
    var $description= 'Home page module';
    var $menu= array (
        'home' => array (
            'name' => 'Home',
            'title' => 'Homepage',
            'description' => 'Default homepage icon',
            'weight' => -1000,
            'perm' => 'guest', 
            'container' => 'home', //AUTOFILL
            'module' => 'home', //AUTOFILL
            'action' => 'index'
        )
    );
    var $actionmenu= array (
        'logout' => array (
            'name' => 'Logout',
            'title' => 'Logout',
            'description' => 'Logout',
            'weight' => 1000,
            'container' => 'home',
            'module' => '%',
            'action' => '%',
            'tcontainer' => 'home',
            'tmodule' => 'home',
            'taction' => 'logout',
            'perm' => 'viewer'
        ),

        'changepass' => array (
            'name' => 'Change Password',
            'title' => 'Change Password',
            'description' => 'Change your Password',
            'weight' => 999,
            'container' => 'home',
            'module' => '%',
            'action' => '%',
            'tcontainer' => 'home',
            'tmodule' => 'home',
            'taction' => 'change_password',
            'perm' => 'viewer'
        )
    );

    function __construct($smarty= NULL)
    {
        $this->tpl= $smarty;
        $OURDIR= dirname(__FILE__);
        $CONTAINER= basename($OURDIR);
        $MODULE= strtolower(substr(substr(basename(__FILE__), 1), 0, -4));
        $this->def= $CONTAINER;
        $this->mod= $MODULE;
        $this->fallbackurl['container']= $this->def;
        $this->fallbackurl['module']= $this->mod;
        $this->fallbackurl['action']= 'index';

    }
    function getSite()
    {
        return array (
            'title' => 'Home'
        );
    }

    function getMenu()
    {
    }

    /**
     * Get the module's actions.
     * 
     * @return array of the menu.
     */

    function getActionMenu()
    {
    }

    function change_password()
    {
        if (!isset ($_SESSION['UID']))
            gen_uri('home', 'home', 'index', true);
        $_FORM= array (
            'password' => '',
            'vpassword' => ''
        );
        if (!empty ($_POST))
        {
            $userTable= Doctrine_Core :: getTable('User');
            $user= $userTable->find($_SESSION['UID']);
            $user->password= $_POST['password'];
            $user->save();
            redir(array (
                'container' => $this->def,
                'module' => $this->def
            ));
        }
        $this->tpl->assign('FORM', $_FORM);
    }

    function index()
    {
        $this->tpl->assign('CONTENTLIST', null);
    }

    function login()
    {
        $username= isset ($_POST['username']) ? $_POST['username'] : NULL;
        $password= isset ($_POST['password']) ? $_POST['password'] : NULL;
        $userTable= Doctrine_Core :: getTable('User');
        $user= $userTable->findOneByUsernameAndPassword(trim($username), sha1($password));
        // User exists with correct password
        if ($user != false)
        {
            $user->Online->session= session_id();
            $user->Online->ip= ip2long($_SERVER['REMOTE_ADDR']);
            $user->save();
            $_SESSION['username']= $username;
            $_SESSION['UID']= $user->id;
            $_SESSION['FULLUSER'][$user->id]= $user->toArray();
            $_SESSION['FULLUSER'][$user->id]['Group']= $user->Groups[0]->toArray();
			trigger_error('User '.$user->username.' logged in succesfuly');
            $success= true;
        } else
            $success= false;
        return array (
            'success' => $success,
            'REDIR' => array (
                'container' => $this->def,
                'module' => $this->def,
                'action' => 'index'
            )
        );

    }

    function logout()
    {
        $userTable= Doctrine_Core :: getTable('Online');
        $online= $userTable->findOneBySession(session_id());

        // User is online
        if ($online !== false)
        {
            $online->delete();
            $success= true;
        } else
            $success= false;
        session_destroy();
        unset ($_SESSION['username']);
        unset ($_SESSION['UID']);

        return array (
            'success' => $success,
            'REDIR' => array (
                'container' => $this->def,
                'module' => $this->def,
                'action' => 'index'
            )
        );
    }
    function install()
    {

        $this->uninstall();
        foreach($this->menu as $key => $menuarr)
        {
        	
        	$menu= new Menu();
        	$menu->fromArray($menuarr);
        	$menu->save();
        }

        foreach($this->actionmenu as $key => $menuarr)
        {
        	
        	$menu= new Actionmenu();
        	$menu->fromArray($menuarr);
        	$menu->save();
        }
        
        $module= new Module();
        $module->name= $this->mod;
        $module->container= $this->def;
        $module->category= $this->category;
        $module->save();
    }

    function uninstall()
    {
        $conn= Doctrine_Manager :: connection();
        $q= Doctrine_Query :: create()->delete('Module')->where('name = ?', array (
            ucfirst($this->mod
        )))->execute();

    }

}