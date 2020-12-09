<?php
include_once('../database/animal_queries.php');
include_once('../database/user_queries.php');
function draw_aside(){?>

    <aside id="filter">
        <div id="filterTag">
            Filter
            <button type="button">Apply</button> 
        </div>
        <div class="no_bullet" id="filterElement">
            <div class="textInput">
                <label>Name</label>
                <div>
                    <label>
                        <input type="text" class="textInput" id="Name" name="Name" placeholder="Insert Name...">
                        <br>
                    </label>
                </div>
            </div>
            <div class="textInput">
                <label>Location</label>
                <div>
                    <label>
                        <input type="text" class="textInput" id="Location" name="Location" placeholder="Insert Location...">
                        <br>
                    </label>
                </div>
            </div>
            <div class="intRange">
                <label>Size (cm)</label>
                <div>
                    <label>
                        <label>Min   <input type="number" class="intInput" id="MinSize" name="MinSize" min="0"></label>
                        <br>
                        <label>Max   <input type="number" class="intInput" id="MaxSize" name="MaxSize" min="1"></label>
                        <br>
                    </label>
                </div>
            </div>
            <div class="multipleSelector">
                <label>Species</label>
                <div>
                    <?php
                    $species = get_species();
                    foreach ($species as $specie){
                        $specie = $specie["specie"];
                        ?>
                        <label>
                            <input type="checkbox" id="<?=$specie?>" name="<?=$specie?>" checked>
                            <span></span>
                            <?=$specie?>
                            <br>
                        </label>
                        <?php
                    }
                    ?>
                </div>
            </div>
            <div class="multipleSelector">
                <label>Colors</label>
                <button onclick="dropdown('colors_dropdown')" class="dropdown_button">Show</button>
                <div id="colors_dropdown" class="dropdown_content">
                    <?php
                    $colors = get_colors();
                    foreach ($colors as $color){
                        $color = $color['color'];
                        ?>
                        <label>
                            <input type="checkbox" id="<?=$color?>" name="<?=$color?>" checked>
                            <span></span>
                            <?=$color?>
                            <br>
                        </label>
                        <?php
                    }
                    ?>
                </div>
            </div>
            <div class="multipleSelector">
                <label>States</label>
                <div>
                    <?php
                    $states = get_states();
                    foreach ($states as $state){
                        $state = $state['state'];
                        ?>
                        <label>
                            <input type="checkbox" id="<?=$state?>" name="<?=$state?>" checked>
                            <span></span>
                            <?=$state?>
                            <br>
                        </label>
                        <?php
                    }
                    ?>
                </div>
            </div>
            <div class="radioSelector">
                <label>Gender</label>
                <div>
                    <label>
                        <input type="radio" id="male" name="gender" value="male">
                        <span></span>
                        Male
                        <br>
                    </label>
                    <label >
                        <input type='radio' id="female" name="gender" value="female">
                        <span></span>
                        Female
                        <br>
                    </label>
                    <label >
                        <input type='radio' id="all" name="gender" value="all"checked="checked">
                        <span></span>
                        All
                        <br>
                    </label>
                </div>
            </div>
        </div>
    </aside>
<?php } ?>

<?php
function draw_animal_profiles(){?>
    <div class=pets_display>
        <p class="title_pets_display">Pets available for adoption</p>
        <div id="animal_profiles">
            <?php
            $animals_array = getAnimals(null,null,null,null,null,null,null,0,20);
            foreach ($animals_array as $animal){
                draw_animal($animal["petId"],$animal["name"],null,$animal["size"],$animal["color"],$animal["location"],null,$animal["user"]);
            }
            ?>
        </div>
    </div>

<?php }



function draw_animal($pet_id,$name,$species,$size,$color,$location,$state,$user){
    ?>
    <a class="animal_main_page" href = "animal_profile.php?pet_id=<?=$pet_id?>"  >
        <div class="animal_img_box">
            <img class= "animal_image_box" src="<?=get_animal_photo($pet_id)?>" width="200" height="200">
            <label class="animal_text_box">
                <?=$name?>
            </label>
            <label>
                <?=$species?>
            </label>
            <label>
                <?=$size?>
            </label>
            <label>
                <?=$color?>
            </label>
            <label>
                <?=$location?>
            </label>
            <label>
                <?=$user?>
            </label>
        </div>
    </a>
    <?php
}