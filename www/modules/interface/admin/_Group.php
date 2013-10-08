<?php

/*
 * $Header: /cvs/echolib/echolib/www/modules/interface/admin/_Group.php,v 1.9 2011/09/01 18:16:45 proditis Exp $ 
 */
class GroupGUI
{
    var $def= "admin";
    var $mod= 'group';
    var $tpl= NULL;
    var $SEARCH= array (
        'name'
    );
    var $category= 'SYSTEM';
    var $description= 'Group management and permissions module';
    var $fallbackurl= array (
        'container' => '',
        'module' => '',
        'action' => ''
    );
    var $actionmenu= array (
        'list' => array (
            'name' => 'List Groups',
            'title' => 'List Groups',
            'description' => 'List the groups and permissions that exibit',
            'weight' => 100,
            'container' => 'admin',
            'module' => '%',
            'action' => '%',
            'tcontainer' => 'admin',
            'tmodule' => 'group',
            'taction' => 'index',
            'perm' => 'admin'
        )
    );

    var $_SEARCH= array ();

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

    function add()
    {
        if (!has_perms('admin'))
            gen_uri('home', 'home', 'index', true);
        global $PERMS;
        $this->tpl->assign('PERMS', $PERMS);

        $RT= Doctrine_Core :: getTable('Group');
        if (!empty ($_POST))
        {
            if (isset ($_POST['name']) && !empty ($_POST['name']))
            {
                $groups= $RT->findbyName($_POST['name']);
                if ($groups->toArray() == NULL)
                {
                    unset ($groups);
                    $groups= new group();
                    $groups->name= $_POST['name'];
                    $groups->save();
                    gen_uri($this->def, $this->mod, 'index', true);
                }

            }
            $this->tpl->assign('FORM', $_POST);
        } else
        {
            $g= new Group();
            $g= $g->toArray();
            $this->tpl->assign('FORM', $g);
        }

    }

    function delete()
    {
        if (!has_perms('admin'))
            gen_uri('home', 'home', 'index', true);
        $ID= isset ($_GET['ID']) ? intval($_GET['ID']) : 0;
        $groupTable= Doctrine_Core :: getTable('Group');
        $groups= $groupTable->find(intval($_GET['ID']));
        if ($groups !== false)
            $groups->delete();
        goback($this->fallbackurl);
    }

    function edit()
    {
        if (!has_perms('admin'))
            gen_uri('home', 'home', 'index', true);
        $PREFIX= "F";
        $RT= Doctrine_Core :: getTable('Group');
        if (empty ($_POST))
        {
            $user= $RT->find(intval($_GET['ID']));
            if ($user->toArray() !== NULL)
            {
                $_POST['id']= $user->id;
                $_POST['name']= $user->name;
                $_POST['perm']= $user->perm;
                $this->tpl->assign('FORM', $_POST);
            }
        } else
        {
            if (@ $_POST['name'] != '')
            {
                $user= $RT->find(intval($_POST['id']));
                $user->name= $_POST['name'];
                $user->perm= $_POST['perm'];
                $user->save();
                gen_uri($this->def, $this->mod, 'index', true);
            } else
                $this->tpl->assign('FORM', $_POST);
        }

        global $PERMS;
        $this->tpl->assign('PERMS', $PERMS);
    }

    function export()
    {
        if (!has_perms('admin'))
            gen_uri('home', 'home', 'index', true);
        $pager= Doctrine_Query :: create()->from(ucfirst($this->mod));
        $users= $pager->execute();
        $this->tpl->assign_by_ref('CONTENTLIST', $users);
    }

    function getActionMenu()
    {
        if (has_perms('admin'))
            return array (
                '01_List groups' => gen_uri($this->def, 'group', 'index')
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

    function getSite()
    {
        if (has_perms('admin'))
            return array (
                'title' => 'Groups Management'
            );
    }

    function index()
    {
        if (!has_perms('admin'))
            gen_uri('home', 'home', 'index', true);
        $page= isset ($_GET['page']) ? intval($_GET['page']) : 0;
        $pager= new Doctrine_Pager(Doctrine_Query :: create()->from('Group r'), $page);
        //$pager = new Doctrine_Pager(Doctrine_Core::getTable('User')->findAll(),1,1);
        $groups= $pager->execute();
        $this->tpl->assign('PAGER', $pager);
        $this->tpl->assign('CONTENTLIST', $groups);
        $this->tpl->assign('INLINE_BUTTON', 'New Group');
        $this->tpl->assign('INLINE_ACTION', 'add');
        $this->tpl->assign('FORM', $this->_SEARCH);
    }

    function search()
    {
        if (!has_perms('admin'))
            gen_uri('home', 'home', 'index', true);
        $page= isset ($_GET['page']) ? intval($_GET['page']) : 0;
        if (!empty ($_POST))
            $_SESSION['SEARCH']= $_p= _g($_POST, array (
                'name'
            ));
        else
            if (!empty ($_SESSION['SEARCH']))
                $_p= $_SESSION['SEARCH'];

        $q= Doctrine_Query :: create()->from('Group g');
        foreach ($_p as $key => $val)
            $q->andWhere("g.$key LIKE ?");
        $pager= new Doctrine_Pager($q, $page);
        $results= $pager->execute(array_values($_p));
        $this->tpl->assign('PAGER', $pager);
        $this->tpl->assign('CONTENTLIST', $results);
        $this->tpl->assign('INLINE_BUTTON', 'New Group');
        $this->tpl->assign('INLINE_ACTION', 'add');
        $this->tpl->assign('FORM', $_p);
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
            $module->weight=$i++;
            $module->taction= $action;
            $module->category= $category;
            $module->container= $this->def;
            $module->module= $this->mod;
            $module->action= '%';
            $module->save();
            unset ($module);
        }
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
                foreach ($xml as $xml_item)
                {
                    $user= new Group();
                    $user->id= $xml_item->id;
                    $user->name= $xml_item->name;
                    $user->perm= $xml_item->perm;
                    $user->replace();
                }
            }
        }
    }

}