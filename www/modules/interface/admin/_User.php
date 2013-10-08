<?php

/*
 * $Header: /cvs/echolib/echolib/www/modules/interface/admin/_User.php,v 1.11 2011/09/01 18:16:45 proditis Exp $ 
 */
class UserGUI
{
    var $def= "admin";
    var $mod= 'user';
    var $tpl= NULL;
    var $SEARCH= array (
        'username',
        'email',
        'firstname',
        'lastname'
    );
    var $category= 'SYSTEM';
    var $description= 'User administration and management module';
    var $fallbackurl= array (
        'container' => '',
        'module' => '',
        'action' => ''
    );
    var $_SEARCH= array ();
    var $actionmenu= array (
        'list' => array (
            'name' => 'List Users',
            'title' => 'List Users',
            'description' => 'List the Users',
            'weight' => 0,
            'container' => 'admin',
            'module' => '%',
            'action' => '%',
            'tcontainer' => 'admin',
            'tmodule' => 'user',
            'taction' => 'index',
            'perm' => 'admin'
        )
    );
    function __construct($smarty= NULL)
    {
        $this->tpl= $smarty;
        foreach ($this->SEARCH as $val)
            $this->_SEARCH[$val]= "";
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
            'title' => 'Users Management'
        );
    }

    function index()
    {
        if (!has_perms('admin'))
            gen_uri('home', 'home', 'index', true);
        $page= isset ($_GET['page']) ? intval($_GET['page']) : 0;
        $this->tpl->assign('INLINE_BUTTON', 'New User');
        $pager= new Doctrine_Pager(Doctrine_Query :: create()->from('User u'), $page);
        $users= $pager->execute();
        $this->tpl->assign('CONTENTLIST', $users);
        $this->tpl->assign('PAGER', $pager);
        $this->tpl->assign('EDIT_ACTION', 'edit');
        $this->tpl->assign('DELETE_ACTION', 'delete');
        $this->tpl->assign('INLINE_BUTTON', 'New User');
        $this->tpl->assign('INLINE_ACTION', 'add');
        $this->tpl->assign('FORM', $this->_SEARCH);
    }

    function edit()
    {
        if (!has_perms('admin'))
            gen_uri('home', 'home', 'index', true);
        $userTable= Doctrine_Core :: getTable('User');
        $grs= Doctrine_Core :: getTable('Group')->createQuery('u')->execute();
        $this->tpl->assign('Groups', $grs);
        if (empty ($_POST))
        {
            $user= $userTable->find(intval($_GET['ID']));
            if ($user->toArray() !== null)
            {
                $_POST['id']= $user->id;
                $_POST['username']= $user->username;
                $_POST['password']= '';
                $_POST['firstname']= $user->firstname;
                $_POST['lastname']= $user->lastname;
                $_POST['vpassword']= '';
                $_POST['email']= $user->email;
                $_POST['Groups']= $user->Groups->toArray();
                $this->tpl->assign('FORM', $_POST);
            }
        } else
        {
            $Flastname= $Fusername= $Fid= $Fpassword= NULL;
            $Ffirstname= $Femail= $Fgroup_id= NULL;
            foreach ($_POST as $key => $val)
                ${ 'F' .
                $key }= $val;

            if ($Fusername != '' && intval($Fid) > 0)
            {
                $user= $userTable->find($Fid);
                if (trim($Fpassword) != '')
                    $user->password= $Fpassword;
                $user->username= $Fusername;
                $user->firstname= $Ffirstname;
                $user->lastname= $Flastname;
                $user->email= $Femail;
                $user->UserGroup[0]->group_id= $Fgroup_id;
                $user->save();
                gen_uri($this->def, $this->mod, 'index', true);
            } else
                $this->tpl->assign('FORM', $_POST);
        }
    }

    function delete()
    {
        if (!has_perms('admin'))
            gen_uri('home', 'home', 'index', true);
        $ID= isset ($_GET['ID']) ? intval($_GET['ID']) : 0;
        $groupTable= Doctrine_Core :: getTable('User');
        $groups= $groupTable->find($ID);
        if ($groups !== false)
            $groups->delete();
        redir(array (
            'container' => $this->def,
            'module' => 'user',
            'action' => 'index'
        ));
    }

    function logout()
    {
        $ID= isset ($_GET['ID']) ? intval($_GET['ID']) : 0;
        $groupTable= Doctrine_Core :: getTable('Online');
        $groups= $groupTable->findByUser_id($ID);
        foreach ($groups as $online)
            $online->delete();
        redir(array (
            'container' => $this->def,
            'module' => 'user',
            'action' => 'index'
        ));
    }
    function add()
    {
        if (!has_perms('admin'))
            gen_uri('home', 'home', 'index', true);
        $userTable= Doctrine_Core :: getTable('User');
        $grs= Doctrine_Core :: getTable('Group')->createQuery('u')->execute();
        $this->tpl->assign('Groups', $grs);
        if (!empty ($_POST))
        {
            $username= isset ($_POST['username']) ? @ $_POST['username'] : NULL;
            $user= $userTable->findByUsername($username);
            if ($user != false)
            {
                $u= new User();
                if (trim($_POST['password']) != '')
                    $u->password= @ $_POST['password'];
                $u->username= @ $_POST['username'];
                $u->firstname= @ $_POST['firstname'];
                $u->lastname= @ $_POST['lastname'];
                $u->email= @ $_POST['email'];
                $u->save();
                if (intval($_POST['group_id']) > 0)
                {
                    $u->UserGroup[0]->group_id= intval(@ $_POST['group_id']);
                    $u->UserGroup[0]->user_id= intval($u->id);
                    $u->save();
                }
                gen_uri($this->def, $this->mod, 'index', true);
            } else
                $this->tpl->assign('FORM', $_POST);
        }
        $u= new User();
        $u= $u->toArray();
        $u['vpassword']= '';
        $this->tpl->assign('FORM', $u);
    }

    function online_details()
    {
        if (!has_perms('admin'))
            gen_uri('home', 'home', 'index', true);
        $user_id= intval(@ $_GET['ID']);
        $User= Doctrine_Core :: getTable('User');
        $user= $User->find($user_id);
        $this->tpl->assign('CONTENT', $user);
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

    function export()
    {
        if (!has_perms('admin'))
            gen_uri('home', 'home', 'index', true);
        $pager= Doctrine_Query :: create()->from(ucfirst($this->mod));
        $users= $pager->execute();
        $this->tpl->assign_by_ref('CONTENTLIST', $users);
    }

    function search()
    {
        if (!has_perms('admin'))
            gen_uri('home', 'home', 'index', true);
        $page= isset ($_GET['page']) ? intval($_GET['page']) : 0;
        if (!empty ($_POST))
            $_SESSION['SEARCH']= $_p= _g($_POST, array (
                'username',
                'firstname',
                'lastname'
            ));
        else
            if (!empty ($_SESSION['SEARCH']))
                $_p= $_SESSION['SEARCH'];

        $q= Doctrine_Query :: create()->from('User u');
        foreach ($_p as $key => $val)
            $q->andWhere("u.$key LIKE ?");
        $pager= new Doctrine_Pager($q, $page);
        $results= $pager->execute(array_values($_p));
        $this->tpl->assign('CONTENTLIST', $results);
        $this->tpl->assign('PAGER', $pager);
        $this->tpl->assign('EDIT_ACTION', 'edit');
        $this->tpl->assign('DELETE_ACTION', 'delete');
        $this->tpl->assign('INLINE_BUTTON', 'New User');
        $this->tpl->assign('INLINE_ACTION', 'add');
        $this->tpl->assign('FORM', $_p);
    }

    function getActionMenu()
    {
        if (has_perms('admin'))
            return array (
                '00_List users' => gen_uri($this->def, 'user', 'index')
            );
    }

    function getMenu()
    {
        if (has_perms('admin'))
            return array (
                $this->def => array (
                    'title' => $this->def,
                    'URL' => gen_uri($this->def, $this->mod)
                )
            );
    }
    function install()
    {
        $this->uninstall();
        $menus= array (
            'delete' => 'list',
            'edit' => 'list',
            'import' => 'thead',
            'export' => 'thead',
            'search' => 'thead',
        );
       $i=0;
        foreach ($menus as $action => $category)
        {
            $module= new Pageaction();
            $module->name= ucfirst($action) . ' ' . $this->mod;
            $module->description= ucfirst($action) . ' ' . $this->mod;
            $module->title= ucfirst($action) . ' ' . $this->mod;
            $module->tcontainer= $this->def;
            $module->tmodule= $this->mod;
            $module->taction= $action;
            $module->weight=$i++;
            $module->category= $category;
            $module->container= $this->def;
            $module->module= $this->mod;
            $module->action= '%';
            $module->save();
            unset ($module);
        }
        $menu= new Menu();
        $menu->name= 'Admin';
        $menu->title= 'Administrators Panel';
        $menu->description= 'Administration related panel.';
        $menu->weight= 100;
        $menu->container= $this->def;
        $menu->module= $this->mod;
        $menu->action= 'index';
        $menu->perm= 'admin';
        $menu->replace();
        foreach ($this->actionmenu as $key => $menuarr)
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
            ucfirst($this->mod)
        ))->execute();
        $conn->execute('DELETE FROM pageaction where module=? or tmodule=?', array (
            $this->mod,
            $this->mod
        ));

    }

}