<?php
include_once('../templates/tpl_common.php');
include_once('../templates/tpl_edit_pet.php');
include_once("security_functions.php");
include_once '../database/pet_queries.php';


session_start();
if (!isset($_SESSION['csrf'])) {
    $_SESSION['csrf'] = generate_random_token();
}
if(isset($_POST['pet_id']) && check_pet($_POST['pet_id'])) {
    $animal_data = get_pet_data($_POST['pet_id']);
    draw_head($animal_data['name']." Edit Page");
    $location = '<a href="main.php">main </a> > <a href="animal_profile.php?pet_id='.$_POST["pet_id"].'"> pet_profile</a>  >edit_pet';
    draw_header($location);
    echo '<script src="../js/utils.js" defer></script>';
    echo '<script src="../js/edit_pet.js" defer></script>';
    echo '<input type="hidden" id="csrf" value='.$_SESSION['csrf'].'>';
    draw_edit_pet($_POST['pet_id']);
    draw_footer();
}
else{
    header('Location: ' . '../index.php');
}

?>