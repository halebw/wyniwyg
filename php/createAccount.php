<?php

session_start();

include("connect.php");



//start the browser communication:
header("Content-Type: text/html");
header("Cache-Control: no-store");
header("Pragma: no-cache");
?>

<!--//start the html declaration-->	
<!DOCTYPE html>
	<html>
	
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"></meta>
		<link rel="stylesheet" href="../css/styles.css">
		<script src="../js/jquery-1.10.2.min.js"></script>
		<title>WYNIWYG Create Account</title>
		<script type="text/javascript">
			
		</script>
	</head>
    
    <body>
		<header>
			<div id="header_text">WYNIWYG Create Account</div>
		</header>
		<div class="main_container">
        
<?php		
		if((isset($_REQUEST["username"]))&&(isset($_REQUEST["password"]))){
			
			//--------------------------is the username empty?------------------------------
			//isset will test for NULL, but not for "" (empty quotes)
			if(strlen($_REQUEST["username"]) < 1){
				echo '<div id="create_acct_form">
				<h1>Create Account</h1>
					<form method="POST" action="createAccount.php">
						<div id="error">*Please enter a username.</div>
						Enter a Username: <input type="text" name="username" size="15" /><br />
						<div id="instruction">Please use at least 8 characters for your password.</div>
						Enter a Password: <input type="password" name="password" size="15" value="'.strip_tags($_REQUEST["password"]).'"/><br />
						Confirm Password: <input type="password" name="password2" size="15" value="'.strip_tags($_REQUEST["password2"]).'"/><br />
			<div align="center">
				<p><input type="submit" value="Create Account" /></p>
			</div>
		</form>
			</div>
		';
			}else{//if(strlen($_REQUEST["username"]) < 1)
				//verify it all here...
				//-----------------------We have username AND password----------------------------
				//store the user input in variables
				$myusername=$_REQUEST["username"]; 
				$mypassword=$_REQUEST["password"]; 
					
				$myusername = trim($myusername);//gets rid of any extra spaces
				$myusername = stripslashes($myusername);//gets rid of escape backslashes
				$myusername = mysql_real_escape_string($myusername);//escapes special chars for mySQL
					
				$mypassword = trim($mypassword);
				$mypassword = stripslashes($mypassword);
				$mypassword = mysql_real_escape_string($mypassword);
				
				
				//--------------------------is the username unique?-----------------------------
				$user_query = "SELECT user_name FROM users WHERE user_name = '".$myusername."'";
				$user_result = mysql_query($user_query);
				//how many records have the same username?
				if(mysql_num_rows($user_result) > 0){
					//there is at least one other record with this username
					echo '<div id="create_acct_form">
					<h1>Create Account</h1>
						<form method="POST" action="createAccount.php">
							<div id="error">*This username is already taken.  Please choose a different username.</div>
							Enter a Username: <input type="text" name="username" size="15" /><br />
							<div id="instruction">Please use at least 8 characters for your password.</div>
							Enter a Password: <input type="password" name="password" size="15" value="'.strip_tags($_REQUEST["password"]).'"/><br />
							Confirm Password: <input type="password" name="password2" size="15" value="'.strip_tags($_REQUEST["password2"]).'"/><br />
				<div align="center">
					<p><input type="submit" value="Create Account" /></p>
				</div>
			</form>
				</div>
			';
				}else{  //if(mysql_num_rows($user_result) > 0)
				//the username is unique, now for the password...
				
				//--------------------------is the password long enough?--------------------------
					$checkpwd = trim($_REQUEST["password"]);
					$checkpwd = stripslashes($checkpwd);
					if(strlen($checkpwd) < 8){
						echo '<div id="create_acct_form">
					<h1>Create Account</h1>
						<form method="POST" action="createAccount.php">
							Enter a Username: <input type="text" name="username" size="15" value="'.strip_tags($_REQUEST["username"]).'"/><br />
							<div id="instruction">Please use at least 8 characters for your password.</div>
							<div id="error">*You must use at least 8 characters for your password.</div>
							Enter a Password: <input type="password" name="password" size="15" /><br />
							Confirm Password: <input type="password" name="password2" size="15" /><br />
				<div align="center">
					<p><input type="submit" value="Create Account" /></p>
				</div>
			</form>
				</div>
			';
					}else{  //if(strlen($_REQUEST["password"]) < 8)
					//the password is long enough
					
					//----------------Does the password match the comfirmation password?----------------------
						if($_REQUEST["password"] != $_REQUEST["password2"]){
							echo '<div id="create_acct_form">
					<h1>Create Account</h1>
						<form method="POST" action="createAccount.php">
							Enter a Username: <input type="text" name="username" size="15" value="'.strip_tags($_REQUEST["username"]).'"/><br />
							<div id="instruction">Please use at least 8 characters for your password.</div>
							<div id="error">*The two passwords did not match.  Please re-enter them.</div>
							Enter a Password: <input type="password" name="password" size="15" /><br />
							Confirm Password: <input type="password" name="password2" size="15" /><br />
				<div align="center">
					<p><input type="submit" value="Create Account" /></p>
				</div>
			</form>
				</div>
			';
						}else{ //if($_REQUEST["password"] != $_REQUEST["password2"])
						//the username and password have passed the tests!
						
						//----------------INSERT the new account record into the database------------------------
						$insert_query = "INSERT INTO `users` (`user_id` ,`user_name` ,`password`)
											VALUES (NULL ,  '".$myusername."',  '".sha1($mypassword)."')";
						$insert_result = mysql_query($insert_query)	;
						$_SESSION["user_id"] = mysql_insert_id();	
						$_SESSION["last_activity"] = time(); // update last activity time stamp		
						echo '<div id="create_acct_form">
					<h1>Create Account</h1>
					<div id="success"><p>Success!  Your account has been created.<br/><br/>
					<a href="logout.php">Continue...</a></p></div>
				</div>
			</form>
				</div>
			';
							
						}//end }else{ if($_REQUEST["password"] != $_REQUEST["password2"])
					}// end  -- }else{ if(strlen($_REQUEST["password"]) < 8)
				}//  end-- }else{  //if(mysql_num_rows($user_result) > 0)
			}////  end-- }if(strlen($_REQUEST["username"]) < 1)

		}else{
			//no username or password
			
			//----------------------------------Draw the initial form-------------------------------------
			echo '<div id="create_acct_form">
				<h1>Create Account</h1>
					<form method="POST" action="createAccount.php">
						Enter a Username: <input type="text" name="username" size="15" /><br />
						<div id="instruction">Please use at least 8 characters for your password.</div>
						Enter a Password: <input type="password" name="password" size="15" /><br />
						Confirm Password: <input type="password" name="password2" size="15" /><br />
			<div align="center">
				<p><input type="submit" value="Create Account" /></p>
			</div>
		</form>
			</div>
		';
		}
?>
		
<!--//close the main container tags-->

			</div>
	</body>
	
</html>';

<?php
session_write_close();
?>		
		