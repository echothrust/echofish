<?php


/*
 * $Id: _Report.php,v 1.1.2.1 2011/09/02 12:03:31 proditis Exp $ 
 */
class ReportGUI
{
    var $def= '';
    var $mod= '';
    var $tpl= NULL;
    var $SEARCH= array (
        'name'
    );
    var $category= 'USER';
    var $description= 'A simple reporting module. Group and show the queries into a single page.';
    var $fallbackurl= array (
        'container' => '',
        'module' => '',
        'action' => ''
    );
    var $_SEARCH= array ();
    var $menu= array (
        'report' => array (
            'name' => 'Reports',
            'title' => 'Reports',
            'description' => 'Reports menu',
            'weight' => 1000,
            'perm' => 'writer',
            'container' => 'reporting',
            'module' => 'report',
            'action' => 'index'
        )
    );

    var $actionmenu= array (
        'list' => array (
            'name' => 'List Reports',
            'title' => 'List Reports',
            'description' => 'List Reports',
            'weight' => 0,
            'container' => 'reporting',
            'module' => '%',
            'action' => '%',
            'tcontainer' => 'reporting',
            'tmodule' => 'report',
            'taction' => 'index',
            'perm' => 'guest'
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
    function getMenu()
    {
    }
    function getActionMenu()
    {
    }

    function getSite()
    {
        if (has_perms('admin'))
            return array (
                'title' => 'Reports Management'
            );
    }

    function process()
    {
        $RT= Doctrine_Core :: getTable(ucfirst($this->mod));
        $object= $RT->find(intval(@ $_GET['ID']));
        if ($object->toArray() == null)
            goback($this->fallbackurl);
        $conn= Doctrine_Manager :: connection();
        $xml= new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><report></report>');
        $xml->addAttribute('title', $object->name);
        foreach ($object->ReportQuery as $q)
        {
            if (trim($q->Query->stmt) != '')
            {
                $section= $xml->addChild('section');

                $res= $conn->execute($q->Query->stmt);
                $rows= $res->fetchAll(Doctrine_Core :: FETCH_NUM);
                if (count($rows) > 1)
                {
                    $section->addAttribute('title', $q->Query->name);
                    foreach ($rows as $row)
                    {
                        $s= $section->addChild('result');
                        $s->addAttribute('title', $row[0]);
                        $s->addAttribute('data', $row[1]);
                    }
                } else
                {
                    $row= $rows[0];
                    $section->addAttribute('title', $q->Query->name);
                    $s= $section->addChild('result');
                    $s->addAttribute('title', $q->Query->name);
                    $s->addAttribute('data', $row[0]);
                }

            }
        }
        ob_end_clean();
        ob_start();
        header("Content-Type:text/xml; charset=utf-8");
        print $xml->asXML();
        ob_end_flush();
        exit;
    }
    function index()
    {
        if (!has_perms('admin'))
            gen_uri('home', 'home', 'index', true);
        $page= isset ($_GET['page']) ? intval($_GET['page']) : 0;
        $pager= new Doctrine_Pager(Doctrine_Query :: create()->from(ucfirst($this->mod)), $page);
        $content= $pager->execute();
        $this->tpl->assign('PAGER', $pager);
        $this->tpl->assign('CONTENTLIST', $content);
        $this->tpl->assign('INLINE_BUTTON', 'Add new Report');
        $this->tpl->assign('INLINE_ACTION', 'add');
        $this->tpl->assign('FORM', $this->_SEARCH);
    }

    function install()
    {
        Doctrine :: createTablesFromArray(array (
            ucfirst($this->mod),
            'ReportQuery'
        ));
        $menus= array (
            'delete' => 'list',
            'edit' => 'list',
            'process' => 'list',
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
        foreach ($this->menu as $key => $menuarr)
        {

            $menu= new Menu();
            $menu->fromArray($menuarr);
            $menu->save();
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
        $conn->execute('DROP TABLE IF EXISTS report_query');
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
        $this->tpl->assign('INLINE_BUTTON', 'Add new Report');
        $this->tpl->assign('INLINE_ACTION', 'add');
        $this->tpl->assign('FORM', $_p);
    }
    function add()
    {
        if (!has_perms('admin'))
            gen_uri('home', 'home', 'index', true);
        $RT= Doctrine_Core :: getTable(ucfirst($this->mod));
        $QUERIES= Doctrine_Core :: getTable('Query')->createQuery('q');
        if (!empty ($_POST))
        {
            if (isset ($_POST['name']) && !empty ($_POST['name']))
            {
                $object= $RT->findbyName($_POST['name']);
                if ($object->toArray() == NULL)
                {
                    unset ($object);
                    $object= new $this->mod;
                    $object->name= $_POST['name'];
                    $object->description= $_POST['description'];
                    $object->save();
                    $this->process_linked($_POST['queries'], $object->id);
                    gen_uri($this->def, $this->mod, 'index', true);
                }
            }
            $qresults= $QUERIES->execute();
            $this->tpl->assign('FORM', $_POST);
            $this->tpl->assign_by_ref('QUERIES', $qresults);
        } else
        {
            $g= new $this->mod();
            $qresults= $QUERIES->execute();
            $g= $g->toArray();
            $g['queries']=array();
            $this->tpl->assign('FORM', $g);
            $this->tpl->assign_by_ref('QUERIES', $qresults);
        }

    }

    function edit()
    {
        if (!has_perms('admin'))
            gen_uri('home', 'home', 'index', true);
        $RT= Doctrine_Core :: getTable(ucfirst($this->mod));
        $QUERIES= Doctrine_Core :: getTable('Query')->createQuery('q');
        $qresults= $QUERIES->execute();
        if (empty ($_POST))
        {
            $object= $RT->find(intval($_GET['ID']));
            if ($object->toArray() !== NULL)
            {
                $qs= Doctrine_Core :: getTable('ReportQuery')->createQuery('q')->where('report_id=?', array (
                    intval($_GET['ID'])
                ))->execute();
                $_POST= $object->toArray();
                foreach ($qs->toArray() as $recs)
                    $qids[]= $recs['query_id'];
                $_POST['queries']= $qids;
                $this->tpl->assign('FORM', $_POST);
                $this->tpl->assign_by_ref('QUERIES', $qresults);
            }
        } else
        {
            if (@ $_POST['name'] != '')
            {
                $object= $RT->find(intval($_POST['id']));
                $object->name= $_POST['name'];
                $object->description= $_POST['description'];
                $object->save();
                $this->process_linked($_POST['queries'], $object->id);
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
                    $user= new $this->mod();
                    $user->id= $xml_item->id;
                    $user->name= $xml_item->name;
                    $user->replace();
                }
            }
        }
    }

    private function process_linked($queries= array (), $id= 0)
    {
        if ($queries == NULL || $id == 0)
            return false;

        $cleaner= Doctrine_Query :: create()->delete("ReportQuery")->where('report_id=?', $id)->execute();
        foreach ($queries as $qid)
        {
            $mo= new ReportQuery();
            $mo->report_id= $id;
            $mo->query_id= $qid;
            $mo->replace();
            unset ($mo);
        }
    }
}