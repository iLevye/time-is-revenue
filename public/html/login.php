<!DOCTYPE html>
<html>
	<head>
		<?php include 'inc/meta.php'; ?>
		<link rel="stylesheet" type="text/css" href="assets/css/login.css?v=<?=time()?>"/>
		<title>Woney | Login</title>
	</head>
	<body>
		<div id="bg"></div>
		<div id="form-area">
			<div id="form">
				<div id="bals"><span></span><span></span><span></span><span></span></div>
				<div id="logo">
					<div>Woney</div>
					<span>Time is money</span>
				</div>
				<form action="/login_check" method="post">
					<input type="hidden" name="_csrf_token" value="0-BZZx5gmlyNGNDA_xLnHvZNyoZpXMcyHKnhVoCKjpg" />
					<label for="username">
						<div>E-Mail</div>
						<input type="text" id="username" name="_username" value="" required="required" autocomplete="username" />
					</label>

					<label for="password">
						<div>Password</div>
						<input type="password" id="password" name="_password" required="required" autocomplete="current-password" />
					</label>

					<label for="remember_me">
						<input type="checkbox" id="remember_me" name="_remember_me" checked/>
						<span>Remember me</span>
					</label>
					<button name="_submit">Log in</button>
				</form>
				<div id="hr2txt">
					<span></span>
					<div>OR</div>
				</div>
				<div id="registerBtnArea">
					<a href="">Register for New Account</a>
				</div>
			</div>
		</div>
	</body>
</html>