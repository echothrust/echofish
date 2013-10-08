<?php
//include("../includes/timerstart.php");
/** 
 * @file
 * File that initializes the required parameters 
 * for the activation of the modules and features.
 *
 * @author Pantelis Roditis 
 * @version $Revision: 1.2 $
 * @date 22/05/2006
 */
ob_start();
/**
 * Set the Cookie Expiration, 100 days into the future.
 */
$expireTime = 60*60*24*100; 
session_set_cookie_params($expireTime);
session_start();
include("../includes/config.php");
include('../includes/init.php');
session_name(DBNAME);
include("../includes/bootstrap.php");
include("../includes/functions.php");
include("Echolib.php");
set_error_handler("elog",E_ALL);
/**
 * The project project handler
 */
$Project=new Echolib($_GET,$_POST);
