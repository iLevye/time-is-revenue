<!DOCTYPE html>
<html>
	<head>
		<?php include 'inc/meta.php'; ?>
		<link rel="stylesheet" type="text/css" href="assets/css/login.css?v=<?=time()?>"/>
		<title>Woney | Register</title>
	</head>
	<body>
		<div id="form-area">
			<div id="form">
				<div id="bals"><span></span><span></span><span></span><span></span></div>
				<div id="logo">
					<div>Woney</div>
					<span>Time is money</span>
				</div>
				<form name="fos_user_registration_form" method="post" action="/register/" class="fos_user_registration_register">
					<label for="fos_user_registration_form_email" class="required">
						<div>Email</div>
						<input type="email" id="fos_user_registration_form_email" name="fos_user_registration_form[email]" required="required" class="form-control" />
					</label>
					<label for="fos_user_registration_form_plainPassword_first" class="required">
						<div>Password</div>
						<input type="password" id="fos_user_registration_form_plainPassword_first" name="fos_user_registration_form[plainPassword][first]" required="required" autocomplete="new-password" class="form-control" />
					</label>
					<label for="fos_user_registration_form_plainPassword_second" class="required">
						<div>Repeat password</div>
						<input type="password" id="fos_user_registration_form_plainPassword_second" name="fos_user_registration_form[plainPassword][second]" required="required" autocomplete="new-password" class="form-control" />
					</label>
					<input type="hidden" id="fos_user_registration_form__token" name="fos_user_registration_form[_token]" value="Io0PkTPegE6eDw8SSw9AHxxsmFYnzzog62t1WE9MvNw" />
					<button name="_submit">Register</button>
				</form>
			</div>
		</div>
	</body>
</html>