<?php
$title = 'Pair Thorbell with new device';

?>
<div>Please enter pairing code below into your device to pair it with Thorbell.</div>
<h1><?= $pairDevice->id ?></h1>

<p class="description">
    Please enter data below for new device.
</p>
<form method="post">
    <fieldset>
    <input type="hidden" name="token" value="" />

    <label for="id">Pairing Code:</label>
    <input type="text" name="id" id="id" value="<?= $pairDevice->id ?>" required>

    <label for="id">Device Title:</label>
    <input type="text" name="title" id="title" required>

    <label for="id">VOIP Token:</label>
    <input type="text" name="token" id="token" required>
    </fieldset>

    <fieldset>
    <input class="button-primary" type="submit" value="Pair Device">
    </fieldset>
</form>