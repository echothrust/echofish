<?php
class ArchiveGUI
{
    var $tpl= NULL;
    var $category= 'USER';
    var $description= 'Archive viewer for the masses.';
    var $def= 'logs';
    var $mod= 'archive';
    var $_SEARCH= array ();
    var $SEARCH= array (
        'host',
        'program',
        'msg'
    );
    var $actionmenu= array (
        'list' => array (
            'name' => 'Archived logs',
            'title' => 'Archived logs',
            'description' => 'List Archived syslog messages',
            'weight' => 0,
            'container' => 'logs',
            'module' => '%',
            'action' => '%',
            'tcontainer' => 'logs',
            'tmodule' => 'archive',
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
        require_once (INTERFACE_DIR . '/' . $this->def . '/_Syslog.php');

    }
    function index()
    {
        if (!has_perms('admin'))
            gen_uri('home', 'home', 'index', true);

        $page= isset ($_GET['page']) ? intval($_GET['page']) : 0;
        $q= Doctrine_Query :: create()->select('s.id, INET_NTOA(s.host) as host, facility, priority, level,program,pid,tag,msg,received_ts,created_at,updated_at')->from('Archive s')->orderBy('received_ts DESC');
        $pager= new Doctrine_Pager($q, $page, 100);
        $t= $pager->execute();
        $this->tpl->assign('PAGER', $pager);
        $this->tpl->assign('CONTENTLIST', $t);
        $this->tpl->assign('FORM', $this->_SEARCH);
        $this->tpl->assign('INLINE_BUTTON', null);
        $this->tpl->assign('INLINE_ACTION', null);
        $sGui= new SyslogGui();
        $this->tpl->assign('FACILITIES', $sGui->facilities);
        $this->tpl->assign('LEVELS', $sGui->levels);
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
        elseif (gethostbyname($_p['host']) != $_p['host']) $_p['host']= ip2long(gethostbyname($_p['host']));
else
    $_p['host']= ip2long($_p['host']);

if (@ $_p['acknowledge'] == 1)
    $q= Doctrine_Query :: create()->delete('Archive s');
else
    $q= Doctrine_Query :: create()->select('id, INET_NTOA(host) host, facility, priority, level,program,pid,tag,msg,received_ts,created_at,updated_at')->from('Archive s');

unset ($_p['acknowledge']);
foreach ($_p as $key => $val)
    $q->andWhere("$key LIKE ?");

if (@ $_POST['acknowledge'] == 1 || @ $_SESSION['SEARCH']['acknowledge'] == 1)
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
$sGui= new SyslogGui();
$this->tpl->assign('FACILITIES', $sGui->facilities);
$this->tpl->assign('LEVELS', $sGui->levels);

}

function getActionMenu()
{
    if (has_perms('admin'))
        return array (
            'List Archive' => gen_uri($this->def,
            $this->mod,
            'index'
        ));
}
function getMenu()
{
    if (has_perms('admin'))
        return array (
            $this->def => array (
                'title' => $this->def,
                'URL' => gen_uri($this->def,
                $this->mod
            )
        ));
}
function getSite()
{
    if (has_perms('admin'))
        return array (
            'title' => 'Archive Viewer'
        );
}

function install()
{
    $this->uninstall();
    $conn= Doctrine_Manager :: connection();
    Doctrine :: createTablesFromArray(array (
        ucfirst($this->mod
    )));
    $QUERY= 'CREATE TRIGGER auto_archive_syslog AFTER INSERT ON syslog FOR EACH ROW BEGIN
	INSERT INTO archive (id,host,facility,priority,`level`,`program`,pid, tag,msg,received_ts) VALUES (NEW.id,NEW.host,NEW.facility,NEW.priority,NEW.level,NEW.program,NEW.pid, NEW.tag,NEW.msg,NEW.received_ts);
END;';

    $conn->execute($QUERY);
    $menus= array (
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
        ucfirst($this->mod
    )))->execute();

    $conn->execute('DROP TRIGGER IF EXISTS auto_archive_syslog');
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