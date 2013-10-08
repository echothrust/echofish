<?php


/** 
 * @class Project "modules/core/Project.php"
 * @brief Basic Project Interface
 * 
 * This is our dynamic project manager. It loads dynamicaly
 * modules and features required by our products and at the same time allows our
 * projects to maintain independance with each other and allow co-existance into
 * a single system.
 *
 * @author Pantelis Roditis
 * @version $Revision: 1.10 $
 * @date $Date: 2011/09/02 10:29:43 $
 * $Id: Echolib.php,v 1.10 2011/09/02 10:29:43 proditis Exp $
 */
class Echolib extends Smarty
{
    var $exclude= array (
        'getSite',
        'getMenu',
        'getActionMenu',
        '__construct',
        'install',
        'uninstall'
    );
    /** error messages */
    var $error= null;
    /** Module Object */
    var $module= null;
    /** Action Name */
    var $action= NULL;
    var $container= NULL;
    /** Menus Variable */
    var $MENUS= NULL;
    var $smarty= NULL;
    /** Locale for the Project (aka Language) */
    var $LOCALE= NULL;
    var $RETVAL= NULL;
    /** Module Name */
    var $MODULE_NAME= NULL;
    /** Current Session Permission level   */
    var $PERM=0;
    var $INSTALLED_MODULES=array();

    /**
     * Project Constructor
     * @param $get The GET part of the server request
     * @param $post The POST part of the server request
     */
    function __construct($_GET, $_POST)
    {
        global $OPERATION_MESSAGES, $CONFIG, $MODULE_URL, $DSN, $CACHE_ID;
        global $manager, $conn;


        $this->smarty= new Smarty();
        $this->smarty->template_dir= THEMES_DIR . "/myoxygen";
        $this->smarty->compile_dir= SMARTY_COMPILE_DIR;
        $this->smarty->config_dir= THEMES_DIR . "myoxygen/" . 'configs';
        $this->smarty->cache_dir= SMARTY_COMPILE_DIR . '/cache';
        $this->smarty->caching= FALSE;
        $this->smarty->debugging= SMARTY_DEBUG;
        //Avoid error with smarty deprecated {php} tags
        $this->smarty->allow_php_tag= true;
        $this->session_setup();
        $this->PERM=get_perm_id($_SESSION);
        $this->INSTALLED_MODULES=$this->get_installed_modules();
        $this->getMENUS();
        $this->getModule();
        $this->assignMENUS();
        $this->assignFooter();
        $this->assignBookmarks();
        /*
         * XXX Remove once resolved
         * needed for smarty silencing
         */
        $this->smarty->assign('FORM_TITLE', 'FORM_TITLE');
        $this->smarty->assign('FORM_HEADING', 'FORM_HEADING');
        $this->smarty->assign('INLINE_BUTTON', null);
        $this->smarty->assign('MODULE_ACTION', $this->action);
        $this->smarty->assign('LEFT_TAB_TITLE', null);
        $this->smarty->assign('RIGHT_TAB_TITLE', null);
        $this->smarty->assign('LEFT_TAB_MSG', null);
        $this->smarty->assign('RIGHT_TAB_MSG', null);
        $this->RETVAL= $this->module->{$this->action }();
        if (is_array($this->RETVAL) && isset ($this->RETVAL['REDIR']))
            redir($this->RETVAL['REDIR']);

        $this->smarty->assign('MODULE_NAME', strtolower($this->module->modname));
        $this->smarty->assign('CONTAINER', $this->container);
        $this->assignSite();
        $this->smarty->assign('APP_NAME', APP_NAME);
        $this->smarty->assign('CONFIG', $CONFIG);
        $this->smarty->assign("DATE_FORMAT", _d("DATE_FORMAT"));
        $_SESSION['OPERATION_MESSAGES']= array ();
        $this->smarty->display('index.tpl');

    }

    /**
     * Get the requested module and load it.
     */
    function getModule()
    {
        $container= isset ($_GET['container']) ? trim($_GET['container']) : 'home';
        $module= isset ($_GET['module']) ? trim($_GET['module']) : '';
        $action= isset ($_GET['action']) ? trim($_GET['action']) : 'index';
        if ($container == 'home' && $module == '')
            $module= 'home';

        if (array_search($action, $this->exclude) !== false)
            redir(array (
                'container' => $container,
                'module' => $module,
                'action' => 'index'
            ));

        $this->action= @ $action;
        if ($this->valid_interface($container, $module))
        {
            $this->MODULE_NAME= $module;
            $this->container= $container;
            @ include_once (INTERFACE_DIR . "/module.php");
        } else
        {
            trigger_error('is this what provides us with protection?');
            redir(array (
                'container' => 'home',
                'module' => 'home',
                'action' => 'index'
            ));
        }
        $this->module= new modwrapper($container, $this->smarty, $module);
    }

    /**
     * Get the top menus from all modules.
     * @return array of the menu items.
     */
    function getMENUS()
    {
    		
        $menuso= Doctrine_Query :: create()->from('Menu')->where('perm<=?',array($this->PERM));
        $menuso->andWhere('module IN ?',array(array_values($this->INSTALLED_MODULES) ));
        $menuso=$menuso->orderBy('weight,name')->execute();
        $amenus=array();
        if ($menuso->toArray() != null)
        {
            foreach ($menuso->toArray() as $key => $menuitem)
            {
                $amenus[$menuitem['title']]= array('title'=>$menuitem['container'],'URL'=>gen_uri($menuitem['container'], $menuitem['module'], $menuitem['action']));
            }
        }
        $this->MENUS=$amenus;
    }

    /**
     * Assign MAINMENUS and ACTIONMENUS smarty variables
     * with content retrieved from the current
     * active module.
     */
    function assignMENUS()
    {
        $this->smarty->assign('MAINMENUS', $this->MENUS);
        if ($this->module != NULL)
        {
            $amenus= array();
            $menuso= Doctrine_Query :: create()->from('Actionmenu')->where('(container=? OR container="%") and (module=? or module="%") and (action=? or action="%")', array (
                $this->container,
                $this->MODULE_NAME,
                $this->action
            ))->andwhere('perm<=?',array($this->PERM))->orderBy('weight ASC,name ASC');
            $menuso->andWhere('module IN ? OR tmodule IN ?',array(array_values($this->INSTALLED_MODULES),array_values($this->INSTALLED_MODULES) ));
            $menuso=$menuso->orderBy('weight,name')->execute();
            if ($menuso->toArray() != null)
            {
                foreach ($menuso->toArray() as $key => $menuitem)
                {
                    $amenus[$menuitem['name']]= gen_uri($menuitem['tcontainer'], $menuitem['tmodule'], $menuitem['taction']);
                }
            }
            $this->smarty->assign('ACTIONMENUS', $amenus);
            $pageactions= Doctrine_Query :: create()->from('Pageaction')->where('(container=? OR container="%") and (module=? or module="%") and (action=? or action="%") and category=?', array (
                $this->container,
                $this->MODULE_NAME,
                $this->action,
                'list'
            ))->andwhere('perm<=?',array($this->PERM));
            $pageactions->andWhere('module IN ? OR tmodule IN ?',array(array_values($this->INSTALLED_MODULES),array_values($this->INSTALLED_MODULES) ));
            $pageactions=$pageactions->orderBy('weight,name')->execute();

            $pageactionsh= Doctrine_Query :: create()->from('Pageaction')->where('(container=? OR container="%") and (module=? or module="%") and (action=? or action="%") and category=?', array (
                $this->container,
                $this->MODULE_NAME,
                $this->action,
                'thead'
            ))->andwhere('perm<=?',array($this->PERM));
            $pageactionsh->andWhere('module IN ? OR tmodule IN ?',array(array_values($this->INSTALLED_MODULES),array_values($this->INSTALLED_MODULES) ));
            $pageactionsh=$pageactionsh->orderBy('weight,name')->execute();
            
            $this->smarty->assign_by_ref('PAGE_ACTIONS', $pageactions);
            $this->smarty->assign_by_ref('PAGE_THEAD_ACTIONS', $pageactionsh);
        }

    }

    /**
     * Assign the final information and display the full page.
     */
    function display()
    {
        global $CACHE_ID;
    }

    /**
     * Assign site array smarty variable.
     */
    function assignSite()
    {
        global $ADMIN, $LOGGED, $MODERATOR, $time_start;
        $site= $this->module->getSite();
        $site['charset']= 'utf-8';
        $site['username']= empty ($_SESSION['username']) ? '' : $_SESSION['username'];
        $site['ADMIN']= $ADMIN;
        $site['LOGGED']= $LOGGED;
        $site['MODERATOR']= $MODERATOR;
        $site['time_start']= $time_start;
        $this->smarty->assign('site', $site);
    }

    /**
     * Assign footer smarty variable.
     */
    function assignFooter()
    {
        $footer= array (
            'copyright' => '&copy; Copyright 2006-11 Echothrust Solutions Ltd.',
            'poweredby' => 'Powered by EchoLib lab edition'
        );
        $this->smarty->assign('footer', $footer);
    }

    function valid_interface($interface= NULL, $module= NULL)
    {
        if (preg_replace("/[A-Za-z\_]/", "", $interface) !== "")
            return false;
        if (preg_replace("/[A-Za-z\_]/", "", $module) !== "" && $module != '')
            return false;
        if (!is_dir(INTERFACE_DIR . "/$interface/"))
            return false;
        return true;
    }

    function session_reset()
    {
        unset ($_SESSION['UID']);
        unset ($_SESSION['username']);
        session_destroy();
        session_start();
    }

    function session_setup()
    {
        if (empty ($_SESSION['UID']) && empty ($_SESSION['username']))
            return;

        if (@ $_SESSION['UID'] == '' || $_SESSION['username'] == '')
        {
            $this->session_reset();
            return;
        }
        $UserTable= Doctrine_Core :: getTable('User');
        $user= $UserTable->find($_SESSION['UID']);
        if ($user != NULL)
        {
            if (!$user->Online->exists())
                $this->session_reset();
            else
            {
                $user->Online->updated_at= NULL;
                $user->Online->save();
            }
        } else
            $this->session_reset();
        unset ($UserTable);
        unset ($user);
    }
    function assignBookmarks() {
      $sb= Doctrine_Core :: getTable('Searchbookmark');
      $BOOKMARKS=$sb->findByContainerAndModule($this->container,$this->MODULE_NAME);
      if($BOOKMARKS->toArray()==null)
        $BOOKMARKS=null;
      $this->smarty->assign_by_ref('BOOKMARKS',$BOOKMARKS);
      unset($BOOKMARKS);
    }
    function get_installed_modules(){
    	$modules= Doctrine_Core :: getTable('Module');
    	$m=array();
    	foreach($modules->findAll()->toArray() as $module)
    		$m[$module['name']]=$module['name'];
    	unset($modules);
    	return $m;
    }
}