<?php
$title = 'Change Admin Password';

?>

<form method="post">
    <fieldset>
    <input type="hidden" name="token" value="" />

    <label for="old_password">New Password:</label>
    <input type="password" name="old_password" id="old_password" value="" required>

    <label for="password">New Password:</label>
    <input type="password" name="password" id="password" value="" required>

    <label for="repeat_password">Repeat Your Password:</label>
    <input type="password" name="repeat_password" id="repeat_password" value="" required>
    </fieldset>

    <fieldset>
    <input class="button-primary" type="submit" value="Change Password">
    </fieldset>
</form>
