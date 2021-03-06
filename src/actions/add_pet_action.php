<?php
include_once "../database/pet_queries.php";
include_once "../database/user_queries.php";
session_start();

class add_pet_reply
{
    public $safety_error = false;
    public $main_pic = false;
    public $other_pics = false;
    public $name = false;
    public $size = false;
    public $species = false;
    public $color = false;
    public $location = false;
    public $query = false;
    public $gender = false;
    public $pet_id = null;

    function add_error($name,$size,$species,$color,$location,$gender){
        $this->name = !$name;
        $this->size = !$size;
        $this->species = !$species;
        $this->color = !$color;
        $this->location = !$location;
        $this->gender = !$gender;
    }
    function has_error(){
        return $this->name || $this->size|| $this->species || $this->color || $this->location;
    }
}

$reply = new add_pet_reply();

//check if csrf matches
if ($_SESSION['csrf'] !== $_POST['csrf']) {
    $reply->safety_error = true;
}
//check if any image was set
else if(count($_FILES)==0){
    $reply->main_pic = true;
    $reply->add_error(isset($_POST['name']),strlen($_POST['size'] ) > 0 ,strlen($_POST['species']) > 0 , strlen($_POST['color']) > 0 ,strlen($_POST['location']) > 0,strlen($_POST['gender']) > 0);

}
//check if user is logged in
else if(isset($_POST['submit']) && isset($_SESSION['user'])) {
    $user = 0;
    $reply->add_error(isset($_POST['name']),strlen($_POST['size'] ) > 0 ,strlen($_POST['species']) > 0 , strlen($_POST['color']) > 0 ,strlen($_POST['location']) > 0,strlen($_POST['gender']) > 0);
    if(!$reply->has_error()){
        $main_pic = get_animal_profile_pic();
        if($main_pic['name'] == null){
            $reply->main_pic = true;
        }
        else{
            $error_on_query = true;
            try {
                $specie = get_specie_id($_POST['species']);
                $user = get_user($_SESSION['user'])['userId'];
                $color = get_color_id($_POST['color']);

                if (strlen($_POST['name'])>0 && !preg_match ("/^[A-Za-zÀ-ÖØ-öø-ÿ\s-]+$/", $_POST['name'])) {
                    $reply->name = true;
                }
                else {
                    $name_stripped = preg_replace ("/[^A-Za-zÀ-ÖØ-öø-ÿ\s-]/", '', $_POST['name']);
                    $location_stripped = preg_replace ("/[^A-Za-zÀ-ÖØ-öø-ÿ\s()-]/", '', $_POST['location']);
                    $size_stripped = preg_replace ("/[^0-9\s-]/", '', $_POST['size']);
                    $gender = null;
                    if($_POST['gender']=='female'){
                        $gender = 'f';
                    }
                    elseif ($_POST['gender'] == 'male'){
                        $gender = 'm';
                    }
                    else{
                        echo json_encode($reply);
                        die();
                    }
                    $pet_id = null;
                    if (!is_numeric($size_stripped)) {
                        $reply->size = true;
                    } else {
                        $error_on_query = add_pet($name_stripped, $specie, $size_stripped, $color, $location_stripped, 1, $user, "nill" . $user,$gender);

                        $pet_id = get_last_pet_id($user, $name_stripped);
                        if ($pet_id == -1) {
                            $reply->query = true;
                        } else if (!add_animal_photo($pet_id, $main_pic, true)) {
                            $reply->main_pic = true;
                        } else {
                            foreach ($_FILES as $file) {
                                if (!add_animal_photo($pet_id, $file, false)) {
                                    $reply->other_pics = true;
                                    break;
                                }
                            }
                        }
                    }
                    $reply->pet_id = $pet_id;
                }
            } catch (PDOException $er) {
                $reply->query = true;
            }
        }
    }
}
echo json_encode($reply);


/**
 * @param $pet_id   id of the pet to add the photo
 * @param $picture  picture to add
 * @param $is_main  true if it is the animal profile picture
 * @return true on succesfull addition
 */
function add_animal_photo($pet_id, $picture, $is_main){
    //$check = getimagesize($picture["tmp_name"]);
    $photo = imagecreatefromjpeg($picture['tmp_name']);
    if($photo === false) {
        $photo = imagecreatefrompng($picture['tmp_name']);
    }
    if($photo === false)
        $photo = imagecreatefromgif($picture['tmp_name']);
    if ($photo === false)
        return false;


    $file_name = "../img/pet_pic" . $pet_id.uniqid();

    $width = imagesx($photo);
    $height = imagesy($photo);
    $square = min($width,$height);

    $pic  = imagecreatetruecolor(400,400);
    imagecopyresized(
        $pic,
        $photo,
        0,
        0,
        ($width>$square)?($width-$square)/2:0,
        ($height>$square)?($height-$square)/2:0,
        400,
        400,
        $square,
        $square);
    imagejpeg($pic,$file_name);

    $photo_id = add_pet_photo_to_db($file_name, $pet_id);
    if($is_main) {
        change_pet_photo_id($pet_id, $photo_id);
    }
    return true;
}

/**
 * @param $user user_id of the pet's owner
 * @param $pet  current pet name
 * @return pet id or -1 if no pet found
 */
function get_last_pet_id($user, $pet){
    $pets = get_pet($pet);
    //check for pets of the $user with the profilepic nill(last inserted pet)
    foreach ($pets as $Pet) {
        if ($Pet['user'] == $user && $Pet['profilePic'] == "nill".$user) {
            return $Pet['petId'];
        }
    }
    return -1;
}


/**
 * @return get main photo from $files and remove it from files array
 */
function get_animal_profile_pic(){
    $photo = $_FILES['picture'];
    unset($_FILES['picture']);
    return $photo;
}