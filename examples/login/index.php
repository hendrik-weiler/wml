<?php
session_start();

require_once '../../src/Parser.php';
require_once '../../src/Writer.php';

use \wml\Parser as Parser;
use \wml\Writer as Writer;

if(isset($_GET['auth'])) {
	$request_body = file_get_contents('php://input');

	$obj = Parser::parse($request_body);
	$login = $obj['children']['Login']['children'];

	$users = file_get_contents('users.wml');
	$users_parsed = Parser::parse($users);

	$foundUser = NULL;
	foreach($users_parsed['children']['Users'] as $user) {
		if($user['children']['Username'] == $login['Username']
			&& $user['children']['Password'] == $login['Password']) {
			$foundUser = $user;
			break;
		}
	}

	if(!is_null($foundUser)) {
		unset($foundUser['children']['Password']);
		$_SESSION['user'] = $foundUser['children'];
		print 1;
	}

	exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login example</title>
	<base href="/examples/login/">
</head>
<body>
    <div>
        <form name="form" action="">
            <label for="username">Username</label>
            <input type="text" name="userame" id="username" />
            <label for="password">Password</label>
            <input type="password" name="password" id="password" />
            <button type="submit">Login</button>
        </form>
    </div>
	<pre>
		Possible accounts:
		max.mustermann abc123
		maxim.mustermann 12345678
	</pre>
    <script>
        document.forms.form.onsubmit = async function (e) {
            e.preventDefault();
            const rawResponse = await fetch('./index.php?auth=1', {
                method: 'POST',
                body: `
Login : Auth
	Username ${document.forms.form.username.value}
	Password ${document.forms.form.password.value}
                `
            });
            const content = await rawResponse.text();
			if(content == 1) {
			    location.href = 'app.php';
			} else {
			    alert('The username or the password is invalid.');
			}
        }
    </script>
</body>
</html>