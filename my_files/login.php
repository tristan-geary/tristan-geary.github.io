<?php
///error message variable
$error_msg = "";


///check if login has been submitted
if (isset($_POST['password'])) {
    
	///store submitted password
    $submitted_pass = $_POST['password'];
    
	///hash
    $target_hash = 'b14e9015dae06b5e206c2b37178eac45e193792c5ccf1d48974552614c61f2ff';
    
	///hashes the users submission
    $submitted_hash = hash("sha256", $submitted_pass);
	
	///checking hashes
    if ($submitted_hash === $target_hash) {
        
        $BASE_URL = '';
        if ($_SERVER['SERVER_NAME'] === 'localhost') {
			
            $BASE_URL = $_SERVER['HTTP_HOST'] . '/my-website/my_files/';
			
        } else if ($_SERVER['SERVER_NAME'] === 'osiris.ubishops.ca') {
			
            $BASE_URL = $_SERVER['HTTP_HOST'] . '/tgeary/';
			
        } else {
			
            $BASE_URL = $_SERVER['HTTP_HOST'];
        }
        
        header('Location: http://' . $BASE_URL . 'to-do.php');
        
        exit();
		
    ///wrong password 
    } else {
        $error_msg = "The password is wrong.";
    }
}

$current_page = 'login'; 
?>


<?php $current_page = 'login'; ?>
<!DOCTYPE html>
<html>
<head>

	<!--metadata-->
    <meta name="author" content="Tristan Geary">
    <meta name="Description" content="Login Page">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="my_style.css">
	
	
	<!--title-->
    <title>Login</title>
	
	<!--linking javascript nav menu-->
	<script src="nav_menu.js"></script>

</head>
<body>
	
	<div class="body_wrapper">
	<!--linking the actual nav-->
    <?php include 'nav.php'; ?>

    <main class="page-body">
        <br>
        <h1>To-Do List Login</h1>
        
		<!-- form for submission-->
        <form action="login.php" method="POST" style="text-align: center;">
            <div>
                <label for="password">Password:</label>
                <input type="password" id="password" name="password">
            </div>
            <br>
            <input type="submit" value="Login">
        </form>
        
		<!--php for if nothing was submitted-->
        <?php if (!empty($error_msg)): ?>
		
            <p style="color: red; text-align: center;"><?php echo $error_msg; ?></p>
			
        <?php endif; ?>
        
    </main>

	<!-- footer -->
    <?php include 'footer.php'; ?>
	
	</div>
</body>
</html>