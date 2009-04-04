<?php
require_once('../../database.php');
?>

<html>

<body>

<?php if (isset($_POST['username'], $_POST['api_key'], $_POST['token'])) { ?>

<?php
$result = $mdb2->query('UPDATE Auth SET '
	. 'username = ' . $mdb2->quote($_POST['username'], 'text') . ' '
	. 'WHERE '
	. 'token = ' . $mdb2->quote($_POST['token']));
if (PEAR::isError($result))
	die("Database error");
?>

<p>Thank you very much, <?php print($_POST['username']); ?>.  Your authorization has been recorded.</p>

<p>You may now close the browser.</p>

<?php } elseif (!isset($_GET['api_key'], $_GET['token'])) { ?>

<p>Must submit an api_key and token to proceed.</p>

<?php } else { ?>

<?php
$result = $mdb2->query('SELECT * FROM Auth WHERE ('
	. 'token = ' . $mdb2->quote($_GET['token'], 'text') . ')');
if (PEAR::isError($result))
	die("Database error");
if (!$result->numRows())
	die("Invalid key");
?>

<form method="post" action="">

<p>Your Username: <input type="text" name="username" /></p>

<p>Your Password: <input type="password" name="password" /></p>

<p>
<input type="submit" value="Submit" />
<input type="hidden" name="api_key" value="<?php print($_GET['api_key']); ?>" />
<input type="hidden" name="token" value="<?php print($_GET['token']); ?>" />
</p>

</form>

<?php } ?>

</body>

</html>
