<?php

/*
 * $Id: _Translation.php,v 1.8 2011/09/01 18:16:45 proditis Exp $ 
 */
class TranslationGUI
{
    var $def= '';
    var $mod= '';
    var $tpl= NULL;
    var $SEARCH= array (
        'msgid',
        'msgstr',
        'language_id'
    );
    var $category= 'SYSTEM';
    var $description= 'Translation interface for Echolib. Requires Language module.';
    var $fallbackurl= array (
        'container' => '',
        'module' => '',
        'action' => ''
    );
    var $LANGUAGES= null;
    var $_SEARCH= array ();
    var $actionmenu= array (
        'list' => array (
            'name' => 'List Translations',
            'title' => 'List Translations',
            'description' => 'List the translations currently supported by the system',
            'weight' => 100,
            'container' => 'admin',
            'module' => '%',
            'action' => '%',
            'tcontainer' => 'admin',
            'tmodule' => 'translation',
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
        $this->LANGUAGES= Doctrine_Query :: create()->from('Language')->execute()->toArray();
    }

    function add()
    {
        if (!has_perms('admin'))
            gen_uri('home', 'home', 'index', true);
        $this->tpl->assign('LANGUAGES', $this->LANGUAGES);
        $RT= Doctrine_Core :: getTable(ucfirst($this->mod));
        if (!empty ($_POST))
        {
            if (isset ($_POST['msgid']) && !empty ($_POST['msgid']))
            {
                $object= $RT->findbyMsgidAndLanguageId(trim($_POST['msgid']),$_POST['language_id']);
                if ($object->toArray() == NULL)
                {
                    unset($object);
                    $object= new $this->mod();
                    $object->msgid= $_POST['msgid'];
                    $object->msgstr= $_POST['msgstr'];
                    $object->language_id= $_POST['language_id'];
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
        $this->tpl->assign('LANGUAGES', $this->LANGUAGES);
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
            if (@ $_POST['msgid'] != '')
            {
                $object= $RT->find(intval($_POST['id']));
                $object->msgid= $_POST['msgid'];
                $object->msgstr= $_POST['msgstr'];
                $object->language_id= $_POST['language_id'];
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
                'title' => 'Translation Title'
            );
    }

    function getActionMenu()
    {
        if (has_perms('admin'))
            return array (
                'Translation Action' => gen_uri($this->def, $this->mod, 'index')
            );
    }

    function index()
    {
        if (!has_perms('admin'))
            gen_uri('home', 'home', 'index', true);
        $this->tpl->assign('LANGUAGES', $this->LANGUAGES);
        $page= isset ($_GET['page']) ? intval($_GET['page']) : 0;
        $pager= new Doctrine_Pager(Doctrine_Query :: create()->from(ucfirst($this->mod)), $page);
        $content= $pager->execute();
        $this->tpl->assign('PAGER', $pager);
        $this->tpl->assign('CONTENTLIST', $content);
        $this->tpl->assign('INLINE_BUTTON', 'Add new Translation');
        $this->tpl->assign('INLINE_ACTION', 'add');
        $this->tpl->assign('FORM', $this->_SEARCH);
        $LANGUAGES= Doctrine_Query :: create()->from('Language')->execute()->toArray();
    }

    function install()
    {
        foreach ($this->actionmenu as $key => $menuarr)
        {

            $menu= new Actionmenu();
            $menu->fromArray($menuarr);
            $menu->save();
        }
        $menus= array (
            'translate' => 'list',
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
                foreach($xml as $xml_item)
                {
                	$user=new Translation();
                	$user->id=$xml_item->id;
                	$user->msgid=$xml_item->msgid;
                	$user->msgstr=$xml_item->msgstr;
                	$user->language_id=$xml_item->language_id;
                	$user->replace();
                }
            }
        }
    }

}