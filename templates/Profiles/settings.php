<?php
$title = 'Thorbell Settings';

?>

<form method="post">
    <input type="hidden" name="token" value="" />
    <fieldset>
        <label for="name">Thorbell Name:</label>
        <input type="text" name="name" id="name" value="<?= $name->value ?>" class="<?= ($name->hasErrors ? 'error' : '') ?>"required>
        <?php if ($name->hasErrors) { echo '<div class="error">Invalid Thorbell Name.</div>'; } ?>
    </fieldset>


    <fieldset>
        <label for="mqtt_server">MQTT Server:</label>
        <input type="text" name="mqtt_server" id="mqtt_server" value="<?= $mqtt_server->value ?>" class="<?= ($mqtt_server->hasErrors ? 'error' : '') ?>">
        <?php if ($mqtt_server->hasErrors) { echo '<div class="error">Invalid MQTT Server.</div>'; } ?>

        <label for="mqtt_port">Port:</label>
        <input type="number" name="mqtt_port" id="mqtt_port" value="<?= $mqtt_port->value ?>" class="<?= ($mqtt_port->hasErrors ? 'error' : '') ?>">
        <?php if ($mqtt_port->hasErrors) { echo '<div class="error">Invalid MQTT Port.</div>'; } ?>

        <label for="mqtt_username">Username:</label>
        <input type="text" name="mqtt_username" id="mqtt_username" value="<?= $mqtt_username->value ?>" class="<?= ($mqtt_username->hasErrors ? 'error' : '') ?>">
        <?php if ($mqtt_username->hasErrors) { echo '<div class="error">Invalid MQTT Username.</div>'; } ?>

        <label for="mqtt_password">Password:</label>
        <input type="password" name="mqtt_password" id="mqtt_password" value="<?= $mqtt_password->value ?>" class="<?= ($mqtt_password->hasErrors ? 'error' : '') ?>">
        <?php if ($mqtt_password->hasErrors) { echo '<div class="error">Invalid MQTT Password.</div>'; } ?>

        <label for="mqtt_mdnsname">Client Name:</label>
        <input type="text" name="mqtt_mdnsname" id="mqtt_mdnsname" value="<?= $mqtt_mdnsname->value ?>" class="<?= ($mqtt_mdnsname->hasErrors ? 'error' : '') ?>">
        <?php if ($mqtt_mdnsname->hasErrors) { echo '<div class="error">Invalid MQTT Client Name.</div>'; } ?>
    </fieldset>

    <fieldset>
    <input class="button-primary" type="submit" value="Save">
    </fieldset>
</form>
