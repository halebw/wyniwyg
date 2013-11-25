 <?php		
//check for login		
if(!isset($_SESSION["user_id"])){
	
	if((isset($_REQUEST["username"]))&&(isset($_REQUEST["password"]))){
		//------------We have username AND password--------------------------------
		//store the user input in variables
		$myusername=$_REQUEST['username']; 
		$mypassword=sha1($_REQUEST['password']); 
		
		// To protect MySQL injection 
		$myusername = trim($myusername);
		$myusername = stripslashes($myusername);
		$myusername = mysql_real_escape_string($myusername);
		
		
//		$mypassword = trim($mypassword);
//		$mypassword = stripslashes($mypassword);
//		$mypassword = mysql_real_escape_string($mypassword);
		
		
		$query = "SELECT user_id FROM users WHERE user_name = '".$myusername."' AND password = '".$mypassword."'";
		$result = mysql_query($query);
		if(mysql_num_rows($result) > 0){ 
		
		    //if there is a result, login = success
                    
                                                                        // from Skips directory PHP
			$row = mysql_fetch_array($result);   //put the result into a PHP array
			$_SESSION["user_id"] = $row["user_id"];   //here is the user_id
			session_write_close();               //this closes the session so other files can use it
			header("Location: ".$_SERVER["HTTP_REFERER"]);
                                                                    //header("index.php");  //reload this script, and we will test for user_id again...
		}else{    //there were no records found, login fail
		
			session_unset();     // unset $_SESSION variable
			session_destroy();   // destroy session data
			echo '
			<div id="log-in-form">
				<h1>The Log-In failed. Bad username or password.</h1>
				<a href="index.php" />Log-In Again</a>
			</div>';
		}// end -- else if(mysql_num_rows($result) > 0)
		
	}else{   
	
		
		//no one is logged-in, show the log-in form
		echo '<div id="log-in-form" class="rounded">
		<h1>Log-In</h1>
		<form method="POST" action="index.php">
			Username: <input type="text" name="username" size="15" /><br />
			Password: <input type="password" name="password" size="15" /><br />
			<div align="center">
				<p><input type="submit" value="Login" /></p>
                                                                                                <input type="hidden" name="mobile" value="mobile">
			</div>
                                                                        <a href="php/createAccount.php" >Create Account</a>
		</form>
	</div>';
	}
	
	
}else{   //if(!isset($_SESSION["user_id"])){
	//---------------------------------------there is already a session------------------------------
	//
	//--------------------------------------check for an expired session-----------------------------
	//if we have actually set "last_activity on the $_SESSION array...
	//...and when we deduct the stored time (in seconds) from the current time (in seconds)
	//and compare it to the number of seconds we allow for a session...
	if (isset($_SESSION["last_activity"]) && (time() - $_SESSION["last_activity"] > (2*60))){  // 2 minute sessions
		// last request was more than 2 minutes ago, close the session
		session_unset();     // unset $_SESSION variable
		session_destroy();   // destroy session data
		
		//--------------------------------show "Session Expired " message------------------------
		echo '
		<div id="log-in-form">
			<h1>Sorry, but your session has expired</h1>
			<a href="index.php" />Log-In Again</a>
		</div>';
	
		
	}
		
	//end -- if(!isset($_SESSION["user_id"])){ ...
}
?>
