<?php
class SyslogGUI
{
    var $tpl= NULL;
    var $category= 'USER';
    var $description= 'Syslog viewer for the masses.';
    var $def= '';
    var $mod= '';
    var $SEARCH= array (
        'host',
        'program',
        'msg',
        'acknowledge'
    );
    var $levels= array (
        'EMERG', /* system is unusable */
        'ALERT', /* action must be taken immediately */
        'CRIT', /* critical conditions */
        'ERR', /* error conditions */
        'WARNING', /* warning conditions */
        'NOTICE', /* normal but significant condition */
        'INFO', /* informational */
        'DEBUG', /* debug-level messages */

        
    );
    var $facilities= array (
        'KERN', /* kernel messages */
        'USER', //(1<<3)  /* random user-level messages */
    'MAIL', //(2<<3)  /* mail system */
    'DAEMON', //(3<<3)  /* system daemons */
    'AUTH', //(4<<3)  /* security/authorization messages */
    'SYSLOG', //(5<<3)  /* messages generated internally by syslogd */
    'LPR', //(6<<3)  /* line printer subsystem */
    'NEWS', //(7<<3)  /* network news subsystem */
    'UUCP', //(8<<3)  /* UUCP subsystem */
    'CRON', //(9<<3)  /* clock daemon */
    'AUTHPRIV', // (10<<3) /* security/authorization messages (private) */
    'FTP', //(11<<3) /* ftp daemon */
    'LOCAL0', //(16<<3) /* reserved for local use */
    'LOCAL1', //(17<<3) /* reserved for local use */
    'LOCAL2', //(18<<3) /* reserved for local use */
    'LOCAL3', //(19<<3) /* reserved for local use */
    'LOCAL4', //(20<<3) /* reserved for local use */
    'LOCAL5', //(21<<3) /* reserved for local use */
    'LOCAL6', //(22<<3) /* reserved for local use */
    'LOCAL7', //(23<<3) /* reserved for local use */

    
    );
    var $_SEARCH= array ();
    var $fallbackurl= array (
        'container' => '',
        'module' => '',
        'action' => ''
    );
    var $actionmenu= array (
        'list' => array (
            'name' => 'List Syslog',
            'title' => 'List Syslog',
            'description' => 'List Syslog table',
            'weight' => 0,
            'container' => 'logs',
            'module' => '%',
            'action' => '%',
            'tcontainer' => 'logs',
            'tmodule' => 'syslog',
            'taction' => 'index',
            'perm' => 'writer'
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
    function index()
    {
        if (!has_perms('admin'))
            gen_uri('home', 'home', 'index', true);
        $page= isset ($_GET['page']) ? intval($_GET['page']) : 0;
        $q= Doctrine_Query :: create()->select('s.id, INET_NTOA(s.host) as host, facility, priority, level,program,pid,tag,msg,received_ts,created_at,updated_at')->from('Syslog s')->orderBy('received_ts DESC');
        $pager= new Doctrine_Pager($q, $page, 100);
        $t= $pager->execute();
        $this->tpl->assign('PAGER', $pager);
        $this->tpl->assign('CONTENTLIST', $t);
        $this->tpl->assign('FORM', $this->_SEARCH);
        $this->tpl->assign('INLINE_BUTTON', null);
        $this->tpl->assign('INLINE_ACTION', null);
        $this->tpl->assign('FACILITIES', $this->facilities);
        $this->tpl->assign('LEVELS', $this->levels);

    }

    function acknowledge()
    {
        if (!has_perms('admin'))
            gen_uri('home', 'home', 'index', true);
        $ref= "";
        $ID= isset ($_GET['ID']) ? intval($_GET['ID']) : 0;
        $roleTable= Doctrine_Core :: getTable('Syslog');
        $r= $roleTable->find(intval($_GET['ID']));
        $q = Doctrine_Query::create()->delete('Syslog')->where('host = ? and program = ? and msg = ?',array($r->host,$r->program,$r->msg))->execute();
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
        if ($_p['host'] == '' || $_p['host'] == '%%')
        {
        }
        elseif (gethostbyname($_p['host']) != $_p['host'])
        {
            $_p['host']= ip2long(gethostbyname($_p['host']));
        } else
            $_p['host']= ip2long($_p['host']);

        if (@ $_p['acknowledge'] == 1)
            $q= Doctrine_Query :: create()->delete('Syslog s');
        else
            $q= Doctrine_Query :: create()->select('id, INET_NTOA(host) host, facility, priority, level,program,pid,tag,msg,received_ts,created_at,updated_at')->from('Syslog s');

        unset ($_p['acknowledge']);
        foreach ($_p as $key => $val)
            $q->andWhere("$key LIKE ?");

        if (isset ($_POST['acknowledge']) && @ $_POST['acknowledge'] == 1 || @ $_SESSION['SEARCH']['acknowledge'] == 1)
        {
            $q->execute(array_values($_p));
            gen_uri($this->def, $this->mod, 'index', true);
        }

        $q->orderBy('s.received_ts DESC');
        $pager= new Doctrine_Pager($q, $page, 100);
        $results= $pager->execute(array_values($_p));
        $this->tpl->assign('CONTENTLIST', $results);
        $this->tpl->assign('PAGER', $pager);
        $this->tpl->assign('EDIT_ACTION', 'edit');
        $this->tpl->assign('DELETE_ACTION', 'delete');
        $this->tpl->assign('FORM', $_SESSION['SEARCH']);
        $this->tpl->assign('FACILITIES', $this->facilities);
        $this->tpl->assign('LEVELS', $this->levels);

    }

    function getActionMenu()
    {
    }
    function getMenu()
    {
    }
    function getSite()
    {
        if (has_perms('admin'))
            return array (
                'title' => 'Syslog Viewer'
            );
    }

    function install()
    {
        Doctrine :: createTablesFromArray(array (
            ucfirst($this->mod)
        ));
        $menus= array (
            'acknowledge' => 'list',
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
            $module->weight= $i++;
            $module->taction= $action;
            $module->category= $category;
            $module->container= $this->def;
            $module->module= $this->mod;
            $module->action= '%';
            $module->save();
            unset ($module);
        }
        $menu= new Menu();
        $menu->name= 'Logs';
        $menu->title= 'Logs Panel';
        $menu->description= 'Logs Managements';
        $menu->weight= 200;
        $menu->container= $this->def;
        $menu->module= $this->mod;
        $menu->action= 'index';
        $menu->perm= 'writer';
        $menu->replace();
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
            ucfirst($this->mod)
        ))->execute();
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

}