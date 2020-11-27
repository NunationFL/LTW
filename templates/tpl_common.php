<?php
function draw_head(){
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width">
	<title>Projeto Black Dog</title>
	<link rel="stylesheet" href="css/style.css">
	<link rel="stylesheet" href="css/layout.css">
	<link rel="stylesheet" href="css/responsive.css">
</head>
<body>
<?php } ?>

<?php
function draw_header(){
    ?>
    <header>
        <a id="title" href="/">Projeto Black Dog</a>
        <section class="login_register">
                <div id="register" class="button" ><a href="login_register.php" class="button-text">Register</a></div>
                <div id="login" class="button"><a href="login_register.php" class="button-text">Login</a></div>
            <label id="user">Name</label>
        </section>
    </header>
<?php } ?>

<?php
function draw_footer(){?>
    <footer>
    </footer>
</body>
</html>
<?php } ?>




