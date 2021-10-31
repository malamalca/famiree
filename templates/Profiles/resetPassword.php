<?php
use App\Core\App;

$title = false;

?>
<div class="container">

<div class="row">
  <div class="column"></div>
  <div class="column">
    <h1>Thorbell Login</h1>
    <p>Reset Password.</p>

<form method="post">
    <fieldset>
    <input type="hidden" name="token" value="" />

    <label for="username">Username:</label>
    <input type="text" name="u" id="username" value="" required>

    <label for="password">Password:</label>
    <input type="password" name="p" id="password" value="" required>
    </fieldset>

    <fieldset>
    <input class="button-primary" type="submit" value="Login">
    </fieldset>

    <fieldset>
        <a href="<?= $this->url('/resetpasswd') ?>">Forgot your password?</a>
    </fieldset>
</form>

    </div>
    <div class="column"></div>
</div>