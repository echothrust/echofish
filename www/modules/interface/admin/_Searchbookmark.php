<?php


/*
 * $Id: _Searchbookmark.php,v 1.1 2011/09/02 10:17:14 proditis Exp $ 
 */
class SearchbookmarkGUI
{
    var $def= '';
    var $mod= '';
    var $tpl= NULL;
    var $SEARCH= array (
        'name'
    );
    var $category= 'USER';
    var $description= 'A simple module that allows for bookmarking your search queries.';
    var $fallbackurl= array (
        'container' => '',
        'module' => '',
        'action' => ''
    );
    var $_SEARCH= array ();

    var $actionmenu= array (
        'list' => array (
            'name' => 'Bookmarked Searches',
            'title' => 'Bookmarked Searches',
            'description' => 'List Bookmarked Searches',
            'weight' => 100,
            'container' => 'admin',
            'module' => '%',
            'action' => '%',
            'tcontainer' => 'admin',
            'tmodule' => 'searchbookmark',
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
                    $object->user_id= $_SESSION['UID'];
                    $object->save();
                    $this->process_tags($object->id);

                    gen_uri($this->def, $this->mod, 'index', true);
                }
            }
            $this->tpl->assign('FORM', $_POST);
        }
        else
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
                $tags= array ();
                foreach ($object->SearchbookmarkTag as $iptag)
                    $tags[]= $iptag->Tag->name;
                $_POST['tags']= implode(', ', $tags);
                $this->tpl->assign('FORM', $_POST);
            }
        }
        else
        {
            if (@ $_POST['name'] != '')
            {
                $object= $RT->find(intval($_POST['id']));
                $object->name= $_POST['name'];
                $object->container= $_POST['container'];
                $object->module= $_POST['module'];
                $object->action= $_POST['action'];
                $object->user_id= $_SESSION['UID'];
                $object->save();
                $this->process_tags($object->id);
                gen_uri($this->def, $this->mod, 'index', true);
            }
            else
                $this->tpl->assign('FORM', $_POST);
        }

    }

    function getSite()
    {
        if (has_perms('admin'))
            return array (
                'title' => 'Search Bookmarks'
            );
    }

    function getActionMenu()
    {
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
        $this->tpl->assign('INLINE_BUTTON', 'Add new Bookmark');
        $this->tpl->assign('INLINE_ACTION', 'add');
        $this->tpl->assign('FORM', $this->_SEARCH);
    }

    function install()
    {
        $this->uninstall();
        Doctrine :: createTablesFromArray(array (
            ucfirst($this->mod),
            'SearchbookmarkTag',
            
        ));

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
            $module->name= ucfirst($action).' '.$this->mod;
            $module->description= ucfirst($action).' '.$this->mod;
            $module->title= ucfirst($action).' '.$this->mod;
            $module->tcontainer= $this->def;
            $module->tmodule= $this->mod;
            $module->weight= $i++;
            $module->taction= $action;
            $module->category= $category;
            $module->container= $this->def;
            $module->module= $this->mod;
            $module->action= '%';
            $module->save();
            unset ($module);
        }

        $module= new Pageaction();
        $module->name= 'Bookmark';
        $module->description= 'Bookmark';
        $module->title= 'Bookmark';
        $module->tcontainer= 'admin';
        $module->weight= -100;
        $module->tmodule= 'searchbookmark';
        $module->taction= 'bookmark';
        $module->category= 'thead';
        $module->container= '%';
        $module->module= '%';
        $module->action= 'search';
        $module->save();
        unset ($module);
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
        $conn->execute('DROP TABLE IF EXISTS searchbookmark_tag');
        $conn->execute('DROP TABLE IF EXISTS '.$this->mod);
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

    function bookmark()
    {
        /**
         * If you are not even logged then i dont wanna logg you budy.
         */
        if (get_perm($_SESSION) == FALSE)
            goback($this->fallbackurl);
        $ref= null;
        $url= parse_url($_SERVER['HTTP_REFERER']);
        if ($url['path'] == '')
            goback($this->fallbackurl);

        parse_str($url['query'], $ref);
        if (is_array($ref) && isset ($ref['container']) && isset ($ref['module']) && isset ($ref['action']))
        {
            $sbookmark= new $this->mod();
            $sbookmark->name= "AUTOGEN ".mt_rand();
            $sbookmark->container= $ref['container'];
            $sbookmark->module= $ref['module'];
            $sbookmark->action= $ref['action'];
            $sbookmark->query= $_SESSION['SEARCH'];
            $sbookmark->user_id= $_SESSION['UID'];
            $sbookmark->save();
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
    private function process_tags($id)
    {
        if (isset ($_POST['tags']) && !empty ($_POST['tags']) && !empty ($id))
        {
            $tags= explode(',', $_POST['tags']);
            $cleaner= Doctrine_Query :: create()->delete("SearchbookmarkTag")->where('searchbookmark_id=?', $id)->execute();
            foreach ($tags as $key => $val)
            {
                $tag= Doctrine_Core :: getTable('Tag')->findByName(trim($val))->toArray();
                if ($tag == null)
                {
                    $tagi= new Tag();
                    $tagi->name= trim($val);
                    $tagi->save();
                    $tag_id= $tagi->id;
                }
                else
                    $tag_id= $tag[0]['id'];

                $iptag= new SearchbookmarkTag();
                $iptag->searchbookmark_id= $id;
                $iptag->tag_id= $tag_id;
                $iptag->save();
                unset ($iptag);
            }

        }
    }

    public function follow()
    {
        if (!has_perms('admin'))
            gen_uri('home', 'home', 'index', true);
        $_v=_g($_GET,array('ID'),'');
        if(intval($_v['ID'])==0)
          goback($this->fallbackurl);
        $rec=Doctrine_Core::getTable('Searchbookmark')->find(intval($_v['ID']));
        if($rec->toArray()==null)
          goback($this->fallbackurl); 
        $_SESSION['SEARCH']=$rec->query;
        gen_uri($rec->container,$rec->module,$rec->action,true);
    }
}