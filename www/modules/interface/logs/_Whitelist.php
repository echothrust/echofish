<?php


/*
 * $Id: _Whitelist.php,v 1.1.6.1 2011/09/02 12:03:29 proditis Exp $
 */
class WhitelistGUI
{
    var $tpl= NULL;
    var $def= 'logs';
    var $mod= 'whitelist';
    var $category= 'USER';
    var $description= 'Whitelist functionality';
    var $fallbackurl= array (
        'container' => '',
        'module' => '',
        'action' => 'index'
    );
    var $actionmenu= array (
        'list' => array (
            'name' => 'List Whitelist',
            'title' => 'List Whitelist',
            'description' => 'List whitelist patterns',
            'weight' => 0,
            'container' => 'logs',
            'module' => '%',
            'action' => '%',
            'tcontainer' => 'logs',
            'tmodule' => 'whitelist',
            'taction' => 'index',
            'perm' => 'admin'
        ),
        'optimize' => array (
            'name' => 'Optimize Whitelist',
            'title' => 'List Whitelist',
            'description' => 'List whitelist patterns',
            'weight' => 1000,
            'container' => 'logs',
            'module' => '%',
            'action' => '%',
            'tcontainer' => 'logs',
            'tmodule' => 'whitelist',
            'taction' => 'optimise',
            'perm' => 'admin'
        ),
    );

    // Same as $SEARCH but with keys
    var $_SEARCH= null;
    var $SEARCH= array (
        'pattern',
        'program',
        'name',
        'description',
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

    function index()
    {
        if (!has_perms('admin'))
            gen_uri('home', 'home', 'index', true);
        $page= intval(isset ($_GET['page']) ? $_GET['page'] : 0);
        $pager= new Doctrine_Pager(Doctrine_Query :: create()->from('Whitelist s')->orderBy('s.pattern ASC'), $page);
        $t= $pager->execute();
        $this->tpl->assign('PAGER', $pager);
        $this->tpl->assign('INLINE_BUTTON', 'Add new Whitelist');
        $this->tpl->assign('INLINE_ACTION', 'add');
        $this->tpl->assign('CONTENTLIST', $t);
        $this->tpl->assign('FORM', $this->_SEARCH);
    }

    function edit()
    {
        if (!has_perms('admin'))
            gen_uri('home', 'home', 'index', true);
        $RT= Doctrine_Core :: getTable('Whitelist');
        if (empty ($_POST))
        {
            $user= $RT->find(intval($_GET['ID']));
            if ($user->toArray() !== NULL)
            {
                $_POST['id']= $user->id;
                $_POST['pattern']= $user->pattern;
                $_POST['program']= $user->program;
                $_POST['name']= $user->name;
                $_POST['description']= $user->description;
                $this->tpl->assign('FORM', $_POST);
            }
        } else
        {
            if (@ $_POST['pattern'] != '')
            {
                $user= $RT->find(intval($_POST['id']));
                $user->pattern= $_POST['pattern'];
                $user->name= $_POST['name'];
                $user->description= $_POST['description'];
                $user->program= $_POST['program'];
                $user->save();
                $conn= Doctrine_Manager :: connection();
                $QUERY= "DELETE t1 FROM syslog AS t1 WHERE msg LIKE ?";
                $pdo= $conn->execute($QUERY, array (
                    $user->pattern
                ));

            }
            $this->tpl->assign('FORM', $_POST);
        }

    }

    function add()
    {
        if (!has_perms('admin'))
            gen_uri('home', 'home', 'index', true);
        $RT= Doctrine_Core :: getTable('Whitelist');
        if (!empty ($_POST))
        {
            if (isset ($_POST['pattern']) && !empty ($_POST['pattern']))
            {
                $rarps= $RT->findbyName($_POST['pattern']);
                if ($rarps->toArray() == NULL)
                {
                    unset ($rarps);
                    $rarps= new Whitelist();
                    $rarps->name= $_POST['name'];
                    $rarps->description= $_POST['description'];
                    $rarps->pattern= $_POST['pattern'];
                    $rarps->program= $_POST['program'];
                    $rarps->save();
                    $conn= Doctrine_Manager :: connection();
                    $QUERY= "DELETE t1 FROM syslog AS t1 WHERE msg LIKE ?";
                    $pdo= $conn->execute($QUERY, array (
                        $rarps->pattern
                    ));
                    gen_uri($this->def, $this->mod, 'index', true);
                }

            }
        }
        $this->tpl->assign('FORM', $_POST);

    }

    function delete()
    {
        if (!has_perms('admin'))
            gen_uri('home', 'home', 'index', true);
        $ID= isset ($_GET['ID']) ? intval($_GET['ID']) : 0;
        $roleTable= Doctrine_Core :: getTable('Whitelist');
        $rarps= $roleTable->find(intval($_GET['ID']));
        if ($rarps !== false)
            $rarps->delete();
        gen_uri($this->def, $this->mod, 'index', true);
    }

    function optimise()
    {
        if (!has_perms('admin'))
            gen_uri('home', 'home', 'index', true);
        $pager= Doctrine_Query :: create()->from('Whitelist s')->orderBy('s.program ASC,s.pattern ASC');
        $t= $pager->execute(array (), Doctrine_Core :: HYDRATE_ARRAY);
        $whitelist= Doctrine_Query :: create()->from('Whitelist s2');
        foreach ($t as $key => $obj)
        {
            $rowz= $whitelist->where('pattern LIKE ? AND s2.id!=?', array (
                $obj['pattern'],
                $obj['id']
            ))->execute();
            if ($rowz->count() > 0)
            {
                foreach ($rowz as $dup)
                    $dup->delete();
            }
        }
        goback($this->fallbackurl);
    }

    function search()
    {
        if (!has_perms('admin'))
            gen_uri('home', 'home', 'index', true);
        $page= isset ($_GET['page']) ? intval($_GET['page']) : 0;
        if (!empty ($_POST))
            $_SESSION['SEARCH']= $_p= _g($_POST, $this->SEARCH);
        elseif (!empty ($_SESSION['SEARCH'])) $_p= $_SESSION['SEARCH'];

        $q= Doctrine_Query :: create()->from('Whitelist s');

        foreach ($_p as $key => $val)
            $q->andWhere("s.$key LIKE ?");
        $pager= new Doctrine_Pager($q, $page);
        $results= $pager->execute(array_values($_p));
        $this->tpl->assign('CONTENTLIST', $results);
        $this->tpl->assign('PAGER', $pager);
        $this->tpl->assign('EDIT_ACTION', 'edit');
        $this->tpl->assign('DELETE_ACTION', 'delete');
        $this->tpl->assign('FORM', $_POST);

    }

    function getSite()
    {
        if (has_perms('admin'))
            return array (
                'title' => 'Whitelist Management'
            );
    }

    function getActionMenu()
    {
    }

    function getMenu()
    {
    }

    function install()
    {
        Doctrine :: createTablesFromArray(array (
            ucfirst($this->mod)
        ));
        $conn= Doctrine_Manager :: connection();
        $conn->execute('DROP TRIGGER IF EXISTS auto_archive_syslog');
        $TQUERY= 'CREATE TRIGGER auto_syslog_archive AFTER INSERT ON archive FOR EACH ROW BEGIN 
                    INSERT DELAYED INTO syslog SELECT t1.* FROM archive t1 LEFT JOIN whitelist t2 ON t1.msg LIKE t2.pattern and t1.program like t2.program and t1.host like t2.host WHERE t1.id=NEW.id AND t2.pattern<=>NULL;
                    END;';
        $conn->execute($TQUERY);
        $menus= array (
            'edit' => 'list',
            'delete' => 'list',
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
            $module->category= $category;
            $module->weight=$i++;
            $module->container= $this->def;
            $module->module= $this->mod;
            $module->action= '%';
            $module->save();
            unset ($module);
        }
        $module= new Pageaction();
        $module->name= 'Whitelist';
        $module->description= 'Whitelist';
        $module->title= 'Whitelist';
        $module->tcontainer= $this->def;
        $module->tmodule= $this->mod;
        $module->weight=$i++;
        $module->taction= 'whitelist';
        $module->category= 'list';
        $module->container= 'logs';
        $module->module= 'syslog';
        $module->action= '%';
        $module->save();
        unset ($module);

        foreach ($this->actionmenu as $key => $menuarr)
        {

            $menu= new Actionmenu();
            $menu->fromArray($menuarr);
            $menu->replace();
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
            $this->mod
        ))->execute();
        $conn->execute('DROP TRIGGER IF EXISTS auto_syslog_archive');
        $conn->execute('DROP TABLE IF EXISTS ' . $this->mod);
        $conn->execute('DELETE FROM pageaction where module=? or tmodule=?', array (
            $this->mod,
            $this->mod
        ));
        $conn->execute('DELETE FROM actionmenu where module=? or tmodule=?', array (
            $this->mod,
            $this->mod
        ));
        $conn->execute('DELETE FROM menu where module=?', array (
            $this->mod
        ));

    }

    function whitelist()
    {
        if (!has_perms('admin'))
            gen_uri('home', 'home', 'index', true);
        $ref= "";
        $RT= Doctrine_Core :: getTable('Syslog');
        if (!empty ($_GET['ID']))
        {
            $user= $RT->find(intval($_GET['ID']));
            if ($user->toArray() !== NULL)
            {
                $wl= new Whitelist();
                $wl->name= 'AUTOGEN ' . rand(0, 1000000);
                $wl->description= $user->msg;
                $wl->program= $user->program;
                $wl->pattern= $user->msg;
                $wl->save();
                $conn= Doctrine_Manager :: connection();
                $QUERY= "DELETE t1 FROM syslog AS t1 WHERE msg LIKE ?";
                $pdo= $conn->execute($QUERY, array (
                    $wl->pattern
                ));
            }
        }
        goback($this->fallbackurl);
    }
    function export()
    {
        if (!has_perms('admin'))
            gen_uri('home', 'home', 'index', true);
        $pager= Doctrine_Query :: create()->from(ucfirst($this->mod));
        $object= $pager->execute();
        $this->tpl->assign_by_ref('CONTENTLIST', $object);
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
                  $user=new Whitelist();
                  $user->id=$xml_item->id;
                  $user->name=$xml_item->name;
                  $user->description=$xml_item->description;
                  $user->host=$xml_item->host;
                  $user->facility=$xml_item->facility;
                  $user->level=$xml_item->level;
                  $user->program=$xml_item->program;
                  $user->pattern=$xml_item->pattern;
                  $user->replace();
                }
            }
        }
    }
}