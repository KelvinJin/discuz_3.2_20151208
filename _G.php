<?php 

define('APPTYPEID', 1);
define('CURSCRIPT', 'home');

if(!empty($_GET['mod']) && ($_GET['mod'] == 'misc' || $_GET['mod'] == 'invite')) {
  define('ALLOWGUEST', 1);
}

require_once './source/class/class_core.php';

C::app()->init();

var_dump('$_SESSION', $_SESSION);
var_dump('$_COOKIE', $_COOKIE);
var_dump('$_G', $_G);
