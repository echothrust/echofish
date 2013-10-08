<?php


/*
 * $Id: _Ip.php,v 1.1.4.1 2011/09/02 12:03:31 proditis Exp $
 */

class IpGui
{
    var $tpl= NULL;
    var $category= 'USER';
    var $description= 'Ip Management module. Very simple.';
    var $def= 'admin';
    var $mod= 'ip';
    var $SEARCH= array (
        'address',
        'fqdn',
        'description',
        'tags',

        
    );
    var $_SEARCH= array ();

    var $actionmenu= array (
        'list' => array (
            'name' => 'List IP',
            'title' => 'List IP',
            'description' => 'List IP',
            'weight' => 100,
            'container' => 'admin',
            'module' => '%',
            'action' => '%',
            'tcontainer' => 'admin',
            'tmodule' => 'ip',
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

    function delete()
    {
        if (!has_perms('admin'))
            gen_uri('home', 'home', 'index', true);
        $ID= isset ($_GET['ID']) ? intval($_GET['ID']) : 0;
        $groupTable= Doctrine_Core :: getTable('Ip');
        $groups= $groupTable->find(intval($_GET['ID']));
        if ($groups !== false)
            $groups->delete();
        gen_uri($this->def, $this->mod, 'index', true);
    }

    function add()
    {
        $RT= Doctrine_Core :: getTable('Ip');
        $this->tpl->assign('FORM', $this->_SEARCH);
        if (!empty ($_POST))
        {
            if (isset ($_POST['address']) && !empty ($_POST['address']))
            {
                $ips= $RT->findbyAddress($_POST['address']);
                if ($ips->toArray() == NULL)
                {
                    unset ($ips);
                    $ip= new Ip();
                    $ip->address= $_POST['address'];
                    $ip->description= $_POST['description'];
                    $ip->fqdn= $_POST['fqdn'];
                    $ip->save();
                    $this->process_tags($ip->id);
                    gen_uri($this->def, $this->mod, 'index', true);
                }

            }
        }

    }

    function edit()
    {
        $RT= Doctrine_Core :: getTable('Ip');
        if (empty ($_POST))
        {
            $ip= $RT->find(intval($_GET['ID']));
            if ($ip->toArray() !== NULL)
            {
                $_POST= $ip->toArray();
                $tags= array ();
                foreach ($ip->IpTag as $iptag)
                    $tags[]= $iptag->Tag->name;
                $_POST['tags']= implode(', ', $tags);
                $this->tpl->assign('FORM', $_POST);
            }
        } else
        {
            if (@ $_POST['address'] != '' && intval($_POST['id']) > 0)
            {
                $ip= $RT->find(intval($_POST['id']));
                if ($ip->toArray() != null)
                {
                    $ip->address= $_POST['address'];
                    $ip->fqdn= $_POST['fqdn'];
                    $ip->description= $_POST['description'];
                    $ip->save();

                    $this->process_tags($ip->id);
                }

            }
            $this->tpl->assign('FORM', $_POST);
        }

    }

    function index()
    {
        $page= isset ($_GET['page']) ? intval($_GET['page']) : 0;
        $q= Doctrine_Query :: create()->from('Ip')->orderBy('address');
        $pager= new Doctrine_Pager($q, $page, 100);
        $t= $pager->execute();
        $this->tpl->assign('PAGER', $pager);
        $this->tpl->assign('CONTENTLIST', $t);
        $this->tpl->assign('FORM', $this->_SEARCH);
        $this->tpl->assign('INLINE_BUTTON', 'Add New IP');
        $this->tpl->assign('INLINE_ACTION', 'add');
    }

    function search()
    {
        $page= isset ($_GET['page']) ? intval($_GET['page']) : 0;
        if (!empty ($_POST))
            $_SESSION['SEARCH']= $_p= _g($_POST, $this->SEARCH);
        elseif (!empty ($_SESSION['SEARCH'])) $_p= $_SESSION['SEARCH'];
        if ($_p['address'] == '' || $_p['address'] == '%%')
        {
        } else
            $_p['address']= ip2long($_p['address']);

        $q= Doctrine_Query :: create()->from('Ip s');

        foreach ($_p as $key => $val)
            $q->andWhere("$key LIKE ?");

        $q->orderBy('s.address');
        $pager= new Doctrine_Pager($q, $page, 100);
        $results= $pager->execute(array_values($_p));
        $this->tpl->assign('CONTENTLIST', $results);
        $this->tpl->assign('PAGER', $pager);
        $this->tpl->assign('EDIT_ACTION', 'edit');
        $this->tpl->assign('DELETE_ACTION', 'delete');
        $this->tpl->assign('FORM', $_SESSION['SEARCH']);

    }

    function getActionMenu()
    {
        if (has_perms('admin'))
            return array (
                'List IP\'s' => gen_uri($this->def, $this->mod, 'index')
            );
    }

    function getSite()
    {
        if (has_perms('admin'))
            return array (
                'title' => 'IP Management'
            );
    }

    function install()
    {
        $this->uninstall();
        Doctrine :: createTablesFromArray(array (
            ucfirst($this->mod),
            'IpTag'
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
            $module->name= ucfirst($action) . ' ' . $this->mod;
            $module->description= ucfirst($action) . ' ' . $this->mod;
            $module->title= ucfirst($action) . ' ' . $this->mod;
            $module->tcontainer= $this->def;
            $module->weight= $i++;
            $module->tmodule= $this->mod;
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

    function export()
    {
        if (!has_perms('admin'))
            goback($this->fallback());
        if (!has_perms('admin'))
            gen_uri('home', 'home', 'index', true);
        $pager= Doctrine_Query :: create()->from(ucfirst($this->mod));
        $object= $pager->execute();
        $this->tpl->assign_by_ref('CONTENTLIST', $object);
    }

    function uninstall()
    {
        $conn= Doctrine_Manager :: connection();
        $q= Doctrine_Query :: create()->delete('Module')->where('name = ?', array (
            ucfirst($this->mod)
        ))->execute();
        $conn->execute('DROP TABLE IF EXISTS ip_tag');
        $conn->execute('DROP TABLE IF EXISTS ' . $this->mod);
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
                    $user= new Ip();
                    $user->id= $xml_item->id;
                    $user->rawaddress= $xml_item->address;
                    $user->fqdn= $xml_item->fqdn;
                    $user->description= $xml_item->description;
                    $user->replace();
                }
            }
        }
    }
    private function process_tags($id)
    {
        if (isset ($_POST['tags']) && !empty ($_POST['tags']) && !empty ($id))
        {
            $tags= explode(',', $_POST['tags']);
            $cleaner= Doctrine_Query :: create()->delete("IpTag")->where('ip_id=?', $id)->execute();
            foreach ($tags as $key => $val)
            {
                $tag= Doctrine_Core :: getTable('Tag')->findByName(trim($val))->toArray();
                if ($tag == null)
                {
                    $tagi=new Tag();
                    $tagi->name=trim($val);
                    $tagi->save();
                    $tag_id= $tagi->id;
                } else
                    $tag_id= $tag[0]['id'];
                
                $iptag= new IpTag();
                $iptag->ip_id= $id;
                $iptag->tag_id= $tag_id;
                $iptag->save();
                unset ($iptag);
            }

        }
    }
}