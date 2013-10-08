<?php


/** 
 * @file
 * Commonly used functions, loaded long before any actions are taken.
 * 
 * @author Pantelis Roditis
 * @version $Revision: 1.7 $
 * @date $Date: 2011/08/31 16:06:28 $
 * $Id: functions.php,v 1.7 2011/08/31 16:06:28 proditis Exp $
 */

/*
 * Simple wrapper for die(),var_dump() and <pre>.
 */
function dye($var)
{
    echo "<pre>";
    var_dump($var);
    echo "</pre>";
    die();
}

/*
 * Redirect the user back based on referer when this is applicable.
 * If referer is not present then go back to a fallback url.
 */
function goback($fallback= NULL)
{
    $ref= "";
    if (trim($_SERVER['HTTP_REFERER']) == '' && $fallback == NULL)
        trigger_error('Attempt to goback() without referer and fallback.');

    $url= parse_url($_SERVER['HTTP_REFERER']);

    parse_str($url['query'], $ref);
    if (is_array($ref) && $ref !== null)
        redir($ref);
    else
        redir($fallback);

}
/*
 * Generate and return a URL for a specific container -> module -> action. 
 * If follow is set to true we will try to perform a HTTP redirect. 
 */
function gen_uri($container= NULL, $module= NULL, $action= NULL, $follow= FALSE)
{
    if ($follow)
        forward('?' . http_build_query(array (
            'container' => $container,
            'module' => $module,
            'action' => $action
        )));
    return '?' . http_build_query(array (
        'container' => $container,
        'module' => $module,
        'action' => $action
    ));
}

function forward($URI)
{
    if ($_SERVER["SERVER_PORT"] == '80')
        $MODULE_URL= "http://";
    elseif ($_SERVER["SERVER_PORT"] == '443') $MODULE_URL= "https://";

    @ $MODULE_URL .= @ $_SERVER["HTTP_HOST"] . $_SERVER["SCRIPT_NAME"];
    header("Location: $MODULE_URL$URI");
    exit ();
}

/**
 * build a query based on $params $key=>$value pairs and follow it
 */
function redir($params= NULL)
{
    forward('?' . http_build_query($params));
}

/**
 * Check if the method requested exists
 */
function __method_exists($obj, $method)
{
    if ($obj == NULL || $method == NULL)
        return false;
    $methods= get_class_methods($obj);
    if (array_search($method, $methods) === false)
        return false;
    return true;
}

/* 
 * Log errors produced by PHP
 * Unfortunately php cant support direct module assignement
 * for the trigger_error function so here it goes in here.
 */
function elog($errno, $errstr, $errfile, $errline)
{
    // mysql connect etc here...
    $logexec= new Errorlog();
    $logexec->errno= $errno;
    $logexec->errstr= $errstr;
    $logexec->errfile= $errfile;
    $logexec->errline= $errline;
    $logexec->request= serialize($_GET);
    $logexec->save();
    return true;
}

/*
 * Wrapper to htmlentities with predefined constants
 * ENT_QUOTES, UTF-8 encoding
 */
function _eh($str)
{
    return htmlentities($str, ENT_QUOTES, 'UTF-8');
}

/*
 * Return the permission status of the user
 */
function get_perm($sess)
{
    // if session is not set or the user hasn't logged in yet return false;
    if (!isset ($sess['FULLUSER']) || !is_array($sess['FULLUSER']))
        return false;
    $users= array_values($sess['FULLUSER']);
    $user= $users[0];
    unset ($users);
    return $user['Group']['perm'];
}

/*
 * Return the permission id status of the user
 */
function get_perm_id($sess)
{
	global $PERMS;
    // if session is not set or the user hasn't logged in yet return false;
    if (!isset ($sess['FULLUSER']) || !is_array($sess['FULLUSER']))
        return 1;
    $users= array_values($sess['FULLUSER']);
    $user= $users[0];
    unset ($users);
    return $PERMS[$user['Group']['perm']];
}


/*
 * Return the permission status of the user
 */
function has_perms($needed)
{
    global $PERMS;
    $perm= get_perm($_SESSION);
    if ($perm === false)
        return false;
    return  ($PERMS[$needed] <= $PERMS[$perm]);
}

/*
 * Build the URL for the pagination (this will save us a lot of trouble) 
 */
function build_page_url($page= 1)
{
    $_GET['page']= $page;
    return '?' . http_build_query($_GET);
}

/**
 * Get the provided keys from the $from variable.
 * If a key is not set populate the predefined value 
 */
function _g($from, $keys, $value= '%%')
{
    $ret= null;
    foreach ($keys as $key)
        if (isset ($from[$key]) && !empty ($from[$key]))
            $ret[$key]= $from[$key];
        else
            $ret[$key]= $value;
    return $ret;
}

/*
 * Dummy wrapper for the gettext and localization modules l8r on.
 */
function _d($var, $lang_id= DEFAULT_LANGUAGE_ID)
{
    $object= Doctrine_Query :: create()->from('Translation')->where('msgid = ? and language_id=?',array($var,$lang_id))->limit(1)->execute();
    if ($object->toArray() == NULL && TDEBUG)
    {
                unset ($object);
                $object= new Translation();
                $object->msgid= $var;
                $object->msgstr= $var;
                $object->language_id= $lang_id;
                $object->save();
   } 
   else
   {
   	$var=$object[0]->msgstr;
   	unset($object);
   }
   return $var;
}
/*
 * Load the modules from a specified folder in order to grab their menus
 */
function _load_modules($DIR)
{
    $MODULES= array ();
    if ($dh= opendir($DIR))
    {
        while (($file= readdir($dh)) !== false)
            if ($file[0] == '_')
            {
                require_once ($DIR . '/' . $file);
                $MODULES[]= substr(substr($file, 1), 0, -4) . 'GUI';
            }
        closedir($dh);
    }
    return $MODULES;
}

/*
 * Get the first module in the specified folder
 */
function _get_first($DIR)
{
    $MODULE= NULL;
    $files= glob($DIR . "/_*.php");
    $MODULE= substr(substr(basename($files[0]), 1), 0, -4);
    return $MODULE;

}

/**
 * Forcibly reload modules on doctrine
 */
function force_modules_reload()
{
	Doctrine::loadModels(MODULES_DIR.'models/generated'); 
	Doctrine::loadModels(MODULES_DIR.'models'); 
}