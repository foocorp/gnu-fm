<html>

<body>

<?php if (isset($_POST['username'], $_POST['api_key'], $_POST['token'])) { ?>

<p>Thank you very much, <?php print($_POST['username']); ?>.  Your authorization has been recorded.</p>

<p>You may now close the browser.</p>

<?php } elseif (!isset($_GET['api_key'], $_GET['token'])) { ?>

<p>Must submit an api_key and token to proceed.</p>

<?php } else { ?>

<form method="post" action="">

<p>Your Username: <input type="text" name="username" /></p>

<p>
<input type="submit" value="Submit" />
<input type="hidden" name="api_key" value="<?php print($_GET['api_key']); ?>" />
<input type="hidden" name="token" value="<?php print($_GET['token']); ?>" />
</p>

</form>

<?php } ?>

</body>

</html>
