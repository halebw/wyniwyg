<?php
echo '<div id="log-in-form" class="rounded">
		<h1>Log-In</h1>
		<form method="POST" action="users/v1/user.php">
			Username: <input type="text" name="user_name" size="15" /><br />
			Password: <input type="password" name="password" size="15" /><br />
			<div align="center">
				<p><input type="submit" value="Login" /></p>
                                                                                                <input type="hidden" name="mobile" value="mobile">
			</div>
                                                                        
		</form>
	</div>
        
<pre>';
print_r($_POST);
echo '<br/></pre>';


?>
