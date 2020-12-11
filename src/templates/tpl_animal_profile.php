<?php
include_once('../database/animal_queries.php');
include_once('../database/user_queries.php');

function draw_animal_aside($animal){
    $animal_data = get_animal_data($animal);
    $user_name = get_user_by_ID($animal_data['user'])['userName'];
    $state = get_state_description($animal_data['state']);
    ?>
    <aside id="animal_profile">

        <img src="<?=get_animal_photo($animal)?>" width="100%" height="100%">
        <label>
            <?=$animal_data['name']?>
        </label>
        <label>
            <?=$animal_data['size']?>
        </label>
        <label>
            <?=$animal_data['color']?>
        </label>
        <label>
            <?=$animal_data['location']?>
        </label>
        <a id="owner_profile" href="user.php?user=<?=$user_name?>">
            <?=$user_name?>
        </a>

        <label>
            <?=$state['state']?>
        </label>
        <?php
        if(isset($_SESSION['user'])){
            ?>
            <section  id = "favourites">
                <?php
                if(!check_pet_user_association($_SESSION['user'],$animal)){
                    ?>
                    <form action="../actions/favourite_action.php" method="POST"  id = "favourite_form">
                        <input type="hidden" name="petId" value="<?=$animal?>">
                        <input type="submit" id="fav_button" value="Add to Favourites">
                        <input type="hidden" name="csrf" value="<?=$_SESSION['csrf']?>">
                    </form>
                    <?php
                }
                else{
                    ?>
                    <form action="../actions/favourite_action.php" method="POST"  id = "favourite_form">
                        <input type="hidden" name="petId" value="<?=$animal?>">
                        <input type="submit" id="fav_button" value="Remove from Favourites">
                        <input type="hidden" name="csrf" value="<?=$_SESSION['csrf']?>">
                    </form>
                    <?php
                }
                ?>
            </section>
            <?php
        }
        ?>
    </aside>

    <?php
}

function draw_animal_profile($animal){
    $photos = get_animal_photos($animal);
    ?>
    <section id="animal_main_section">
        <section id="gallery">
            <?php
            foreach($photos as $photo){
                ?>
                <div class=gallery_photo>
                    <img src = "<?=$photo['path']?>" width=250 height=250/>
                </div>

                <?php
            }
            ?>
        </section>
    </section>
    <?php
}

function draw_animal_comments($animal){
    $questions = get_animal_questions($animal);
    ?>
    <section id="questions">
        <?php foreach ($questions as $question){
            $replies = show_question_reply($question['questionId']);
            ?>
            <article class="question" id="question_id_<?=$question['questionId']?>">
                <!-- <span class="question_id"><?=$question['questionId']?></span> -->
                <div id="question_header">
                    <a id="author" href="user.php?user=<?=$question['userName']?>">
                        <?=$question['userName']?> asked:
                    </a>
                    <span class="date"><?=date('Y-m-d H:i:s', $question['date']);?></span>
                    <div class="drop_down">
                        <button onclick="dropdown('replies_dropdown_<?=$question['questionId']?>')" class="dropdown_button">Show Replies</button>
                    </div>
                </div>
                <p id="question_text"><?=$question['questionTxt']?></p>
                <div id="replies_dropdown_<?=$question['questionId']?>" class="dropdown_content">
                    <?php
                    if($replies){
                        foreach ($replies as $reply){?>
                            <div class="reply">
                                <a id="author" href="user.php?user=<?=$reply['userName']?>">
                                    <?=$reply['userName']?> replied:
                                </a>
                                <span class="date"><?=date('Y-m-d H:i:s', $reply['date']);?></span>
                                <p><?=$reply['answerTxt']?></p>
                            </div>
                            <?php
                        }
                    }
                    else{ ?>
                        <p>There are no replies to this question yet</p>
                        <?php
                    }

                    if(isset($_SESSION['user'])){
                        $userID = getUser($_SESSION['user'])['userId'];
                        ?>
                        <div id="reply_<?=$question['questionId']?>" class="reply_area">
                            <p>Send a reply...</p>
                            <textarea name="reply_text"></textarea>
                            <input type="hidden" name="userId" value="<?=$userID?>">
                            <input type="hidden" name="questionId" value="<?=$question['questionId']?>">
                            <input type="submit" value="submit" onclick="submitReply('reply_<?=$question['questionId']?>')">
                            <input type="hidden" name="csrf" value="<?=$_SESSION['csrf']?>">
                        </div>
                    <?php }
                    ?>
                </div>
            </article>
            <?php
        }
        ?>

        <?php if(isset($_SESSION['user'])){
            $userID = getUser($_SESSION['user'])['userId'];
            ?>
            <script src="../js/comments.js" defer></script>
            <form id="ask_question">
                <p>Ask a question...</p>
                <textarea name="comment_text"></textarea>
                <input type="hidden" name="petId" value="<?=$animal?>">
                <input type="hidden" name="userId" value="<?=$userID?>">
                <input type="submit" value="submit">
                <input type="hidden" name="csrf" value="<?=$_SESSION['csrf']?>">
            </form>
        <?php } ?>

    </section>
    <?php
}

function draw_proposals($user,$animal){
    $proposals = null;
    if($user == null){
        $proposals = get_proposals_for_pet($animal);
        if($proposals!=null){
            echo '<script src="../js/add_proposal_reply.js" defer></script>';
            echo "<section id='proposals'>";
            foreach ($proposals as $proposal){
                $user = get_user_by_ID($proposal['user']);
                $pet = get_animal_data($proposal['pet']);
                ?>
                <div class="proposal">
                    <input type="hidden" name="csrf" value=<?=$_SESSION['user']?>>
                    <input type="hidden" name="proposal_id" value=<?=$proposal['proposalId']?>>
                    <input type="hidden" name="pet_id" value=<?=$pet['petId']?>>
                    <div id="proposal_head">
                        <label id="proposal-user"><?=$user['userName']?> proposed:</label><label><?php
                            switch($proposal['state']){
                                case 0:
                                    echo "For Review";
                                    break;
                                case 1:
                                    echo "Accepted";
                                    break;
                                case 2:
                                    echo "Denied";
                                    break;
                            }
                            ?>
                        </label>
                    </div>
                    <label id="proposal-text"><?=$proposal['text']?></label>

                    <?php
                    if($proposal['state']==0){
                        echo "    <button id='accept_button'>Accept Proposal</button>
                                  <button id='deny_button'>Deny Proposal</button>";
                    }
                    elseif ($proposal['state']==1){
                        echo "<button id='deny_button'>Deny Proposal</button>";
                    }
                    elseif ($proposal['state']==2){
                        echo "<button id='accept_button'>Accept Proposal</button>";
                    }
                    ?>
                </div>
                <?php
            }
            echo "</section>";
        }
    }
    else{
        $user_data = getUser($user);
        $proposals = get_proposals($user_data['userId'],$animal);
        if($proposals!=null){
            echo "<section id='proposals'>";
            foreach ($proposals as $proposal){
                $pet = get_animal_data($proposal['pet']);
                draw_proposal($user_data['userName'],$pet['name'],$proposal['text'],$proposal['state']);
            }?>
            <div id="add_proposal">
                <label>Add A proposal</label>
                <form id="proposal_form" action="../actions/add_proposal_action.php" method="post">
                    <textarea id="proposal-text" name="proposal_text" rows="4" cols="50"></textarea>
                    <br><br>
                    <input type="submit" value="Submit">
                    <input type="hidden" name="csrf" value=<?=$_SESSION['csrf']?>>
                    <input type="hidden" name="pet_id" value=<?=$animal?>>
                </form>
            </div>
            </section>
            <?php
        }

    }
}