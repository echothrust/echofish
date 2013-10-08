<?php
class modwrapper
{
    var $tpl= NULL;
    var $modname= NULL;
    var $mod= NULL;
    var $container= NULL;
    function __construct($container, $smarty= NULL, $module= NULL)
    {
        $this->tpl= $smarty;
        $this->modname= $module;
        $this->container= $container;
    }
    public function __call($method, $args)
    {

        if ($this->modname == NULL)
        {
            $this->modname=strtolower(_get_first(dirname(__FILE__) . "/{$this->container}/"));
            gen_uri($this->container,$this->modname,'index',true);
            
        }
        $this->modname= ucfirst($this->modname);
        $module= $this->modname;
        if (file_exists(dirname(__FILE__) . "/{$this->container}/_$module.php") === FALSE)
        {
        	trigger_error("Requested module or container does not exist");
            gen_uri('home', 'home', 'index', TRUE);
        }

        require_once ("{$this->container}/_$module.php");
        $module .= 'GUI';
        $this->mod= & new $module ($this->tpl);
        if (__method_exists($this->mod, $method))
            return $this->mod-> $method (isset ($args[0]) ? $args[0] : '');
        else
        {
        	trigger_error("Requested method does not exist");
            gen_uri($this->container, $this->modname, 'index', TRUE);
        }
    }

    function getSite()
    {
        if(method_exists($this->mod,'getSite'))
        	return $this->mod->getSite();
    }


    /**
     * Get the module's actions.
     * 
     * @return array of the menu.
     */

    function getActionMenu()
    {
        $MENU= array ();
        if (isset ($_SESSION['UID']))
        {
            foreach (_load_modules(dirname(__FILE__)."/{$this->container}") as $module)
            {
                $m= new $module ();
                $MENU= array_merge($MENU, $m->getActionMenu());
                unset ($m);
            }
        }
        return $MENU;
    }

}