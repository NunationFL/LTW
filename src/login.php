<?php
include_once('templates/tpl_common.php');
include_once('templates/tpl_login.php');

session_start();

draw_head();
draw_header();
draw_login();
draw_footer();