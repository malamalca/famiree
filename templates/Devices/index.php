<?php
use App\Core\App;

$title = 'Devices';
?>

<div>
    <a href="<?= App::url('/devices/add') ?>" class="button"><?= _('Pair Device') ?></a>
</div>

<table>
    <thead>
        <tr>
            <th><?= _('Device Name') ?></th>
            <th><?= _('Token No.') ?></th>
            <th>&nbsp;</th>
        </tr>
    </thead>
    <tbody>
    <?php
        foreach ($devices as $device) {
    ?>
        <tr id="<?= $device->id ?>">
            <td><?= h($device->title) ?></td>
            <td><?= $device->token ?></td>
            <td>
                <a href="<?= App::url('/devices/delete/' . $device->id) ?>" onclick="return confirm('Are you sure?');">
                    <i class="icon ion-md-trash"></i>
                </a>
            </td>
        </tr>
    <?php
        }
    ?>
    </tbody>
</table>