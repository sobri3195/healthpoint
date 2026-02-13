<?php
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED);
session_start();
$lang = $_SESSION['lang'];
unset($_SESSION['id_user']);
unset($_SESSION['sml_si']);
unset($_SESSION['sml_si_l']);
session_destroy();
session_start();
session_regenerate_id();
$_SESSION['lang'] = $lang;
session_write_close();

