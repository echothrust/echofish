<?php

/*
 * $Id: _Language.php,v 1.7 2011/09/01 18:16:45 proditis Exp $ 
 */
class LanguageGUI
{
    var $def= '';
    var $mod= '';
    var $tpl= NULL;
    var $SEARCH= array (
        'name',
        'code'
    );
    var $category= 'SYSTEM';
    var $description= 'A simple management for languages this site can translate into.';
    var $fallbackurl= array (
        'container' => '',
        'module' => '',
        'action' => ''
    );
    var $_SEARCH= array ();
    var $actionmenu= array (
        'list' => array (
            'name' => 'List Languages',
            'title' => 'List Languages',
            'description' => 'List the Languages currently supported by the system',
            'weight' => 100,
            'container' => 'admin',
            'module' => '%',
            'action' => '%',
            'tcontainer' => 'admin',
            'tmodule' => 'language',
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

    function add()
    {
        if (!has_perms('admin'))
            gen_uri('home', 'home', 'index', true);
        $RT= Doctrine_Core :: getTable(ucfirst($this->mod));
        if (!empty ($_POST))
        {
            if (isset ($_POST['name']) && !empty ($_POST['name']))
            {
                $object= $RT->findbyName($_POST['name']);
                if ($object->toArray() == NULL)
                {
                    unset ($object);
                    $object= new $this->mod();
                    $object->name= $_POST['name'];
                    $object->code= $_POST['code'];
                    $object->save();
                    gen_uri($this->def, $this->mod, 'index', true);
                }
            }
            $this->tpl->assign('FORM', $_POST);
        } else
        {
            $g= new $this->mod();
            $g= $g->toArray();
            $this->tpl->assign('FORM', $g);
        }

    }

    function edit()
    {
        if (!has_perms('admin'))
            gen_uri('home', 'home', 'index', true);
        $RT= Doctrine_Core :: getTable(ucfirst($this->mod));
        if (empty ($_POST))
        {
            $object= $RT->find(intval($_GET['ID']));
            if ($object->toArray() !== NULL)
            {
                $_POST= $object->toArray();
                $this->tpl->assign('FORM', $_POST);
            }
        } else
        {
            if (@ $_POST['name'] != '')
            {
                $object= $RT->find(intval($_POST['id']));
                $object->name= $_POST['name'];
                $object->code= $_POST['name'];
                $object->save();
                gen_uri($this->def, $this->mod, 'index', true);
            } else
                $this->tpl->assign('FORM', $_POST);
        }

    }

    function getSite()
    {
        if (has_perms('admin'))
            return array (
                'title' => 'Language Title'
            );
    }

    function getActionMenu()
    {
        if (has_perms('admin'))
            return array (
                'List Languages' => gen_uri($this->def, $this->mod, 'index')
            );
    }

    function index()
    {
        $page= isset ($_GET['page']) ? intval($_GET['page']) : 0;
        $pager= new Doctrine_Pager(Doctrine_Query :: create()->from(ucfirst($this->mod)), $page);
        $content= $pager->execute();
        $this->tpl->assign('PAGER', $pager);
        $this->tpl->assign('CONTENTLIST', $content);
        $this->tpl->assign('INLINE_BUTTON', 'Add new Language');
        $this->tpl->assign('INLINE_ACTION', 'add');
        $this->tpl->assign('FORM', $this->_SEARCH);
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
        $i= 0;
        foreach ($menus as $action => $category)
        {
            $module= new Pageaction();
            $module->name= ucfirst($action) . ' ' . $this->mod;
            $module->description= ucfirst($action) . ' ' . $this->mod;
            $module->title= ucfirst($action) . ' ' . $this->mod;
            $module->tcontainer= $this->def;
            $module->tmodule= $this->mod;
            $module->taction= $action;
            $module->weight= $i++;
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
    function export()
    {
        if (!has_perms('admin'))
            gen_uri('home', 'home', 'index', true);
        $pager= Doctrine_Query :: create()->from(ucfirst($this->mod));
        $object= $pager->execute();
        $this->tpl->assign_by_ref('CONTENTLIST', $object);
    }

    function search()
    {
        if (!has_perms('admin'))
            gen_uri('home', 'home', 'index', true);
        $page= isset ($_GET['page']) ? intval($_GET['page']) : 0;
        if (!empty ($_POST))
            $_SESSION['SEARCH']= $_p= _g($_POST, $this->SEARCH);
        else
            if (!empty ($_SESSION['SEARCH']))
                $_p= $_SESSION['SEARCH'];

        $q= Doctrine_Query :: create()->from(ucfirst($this->mod));
        foreach ($_p as $key => $val)
            $q->andWhere("$key LIKE ?");
        $pager= new Doctrine_Pager($q, $page);
        $results= $pager->execute(array_values($_p));
        $this->tpl->assign('PAGER', $pager);
        $this->tpl->assign('CONTENTLIST', $results);
        $this->tpl->assign('INLINE_BUTTON', 'Button Action');
        $this->tpl->assign('INLINE_ACTION', 'button_action');
        $this->tpl->assign('FORM', $_p);
    }

    function delete()
    {
        if (!has_perms('admin'))
            gen_uri('home', 'home', 'index', true);
        $ID= isset ($_GET['ID']) ? intval($_GET['ID']) : 0;
        $objTable= Doctrine_Core :: getTable(ucfirst($this->mod));
        $object= $objTable->find(intval($_GET['ID']));
        if ($object !== false)
            $object->delete();
        goback($this->fallbackurl);
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
                    $user= new Language();
                    $user->id= $xml_item->id;
                    $user->name= $xml_item->name;
                    $user->code= $xml_item->code;
                    $user->replace();
                }
            }
        }
    }

}