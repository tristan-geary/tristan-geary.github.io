<?php

	include "includes/config.php";
	session_start();
	
	///messages
	$logout_msg = ""; 
	$error_msg = ""; 
	
	///prefill username if cookie exists
	$saved_username = $_COOKIE['to-do-username'] ?? ''; 
	
	
	///logout handling
	if(isset($_POST['logout'])){
		$_SESSION = []; 
		
		if(ini_get("session.use_cookies")){
			$params = session_get_cookie_params(); 
			setcookie(session_name(), '', time() -4200,
				$params["path"],
				$params["domain"], 
				$params["secure"],
				$params["httponly"]
			);
		}
		
		
		session_destroy(); 
		session_start();
		$logout_msg = "Successfully logged out"; 
	}
	
	///if already logged in, go straight to to-do page
	if (!isset($_POST['logout']) && isset($_SESSION['is_logged_in']) && $_SESSION['is_logged_in'] === true) {
    header("Location: to-do.php");
    exit();
}
	
	


///check if login has been submitted
if (isset($_POST['password'])) {
    
	///store submitted password
    $submitted_pass = $_POST['password'] ?? '';
	$username = $_POST['username'] ?? '';
	
	///keep the last typed username in the field
	if(!empty($username)){
		$saved_username = $username; 
	}
	
	$user = $username; 
	
	
	///loading attempts file
	$file = 'login_attempts.json';
	
	if(file_exists($file)){
		$json = file_get_contents($file); 
		$attempts = json_decode($json, true);
		
		if(!is_array($attempts)){
			$attempts = [];
		}
	}else{
		$attempts = []; 
	}
	
	///ensure user exists
	if(!isset($attempts[$user])){
		$attempts[$user] = [
		'attempts' => 0, 
		'locked_until' => ''
		];
	}
	
	$now = time(); 
	$login_success = false;
	
	
	///check if user is currently locked out
	if(!empty($attempts[$user]['locked_until']) && $attempts[$user]['locked_until'] > $now){
		$remaining = $attempts[$user]['locked_until'] - $now; 
		if($remaining < 0){
			$remaining = 0; 
		}
		
		$error_msg = "Too many wrong attempts.  Please wait {$remaining} seconds before trying again.";
	}else{
    
	///hash
    $target_hash = 'b14e9015dae06b5e206c2b37178eac45e193792c5ccf1d48974552614c61f2ff';
    
	///hashes the users submission
    $submitted_hash = hash("sha256", $submitted_pass);
	
	///checking hashes
    if ($submitted_hash === $target_hash) {
		
		///reset attempts on success
		$attempts[$user]['attempts'] = 0; 
		$attempts[$user]['locked_until'] = '';
		
		///cookie for username
		setcookie("to-do-username", $username);
		
		$_SESSION['is_logged_in'] = true;
		
		$login_success = true;
	}else{
		
		///wrong password
		$attempts[$user]['attempts'] +=1;
		
		
		///lockout after 3 attempts
		if($attempts[$user]['attempts']>=3){
			
			$attempts[$user]['locked_until'] = $now + 30; 
			$attempts[$user]['attempts'] = 0;
			
			$error_msg = "Too many wrong attempts.  You're locked out for 30 seconds";
		}else{
			$error_msg = "The password is wrong"; 
	}}}
	
	file_put_contents($file, json_encode($attempts));
	
	if($login_success){
        
        $BASE_URL = '';
        if ($_SERVER['SERVER_NAME'] === 'localhost') {
			
            $BASE_URL = $_SERVER['HTTP_HOST'] . '/my-website/my_files/';
			
        } else if ($_SERVER['SERVER_NAME'] === 'osiris.ubishops.ca') {
			
            $BASE_URL = $_SERVER['HTTP_HOST'] . '/~tgeary/my_files/';
			
        } else {
			
            $BASE_URL = $_SERVER['HTTP_HOST'] . '/';
        }
		
		//setting user to logged in state
        $_SESSION['is_logged_in'] = true;
        header('Location: http://' . $BASE_URL . 'to-do.php');
		
        exit();
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
    <?php include 'includes/nav.php'; ?>

    <main class="page-body">
        <br>
        <h1>To-Do List Login</h1>
		
		<?php if(!empty($logout_msg)):?>
		<p style="color: green; text-align: center;">
		<?php echo $logout_msg; ?>
		</p>
		<?php endif; ?>
        
		<!-- form for submission-->
        <form action="login.php" method="POST" style="text-align: center;">
            <div>
				
				<!-- Username -->
				<label for="username">Username:</label>
				<input type="username" id="username" name="username"
				value="<?php echo htmlspecialchars($saved_username);?>"
				required
				>
					
				<!-- password-->
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
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
	<?php include 'includes/footer.php' ?>
	
	</div>
</body>
</html>