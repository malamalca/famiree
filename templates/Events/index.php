<?php
use App\Core\App;

$title = 'Event List';
?>

<table>
    <thead>
        <tr>
            <th><?= _('Date') ?></th>
            <th><?= _('Image') ?></th>
            <th>&nbsp;</th>
        </tr>
    </thead>
    <tbody>
    <?php
        foreach ($events as $event) {
    ?>
        <tr>
            <td><?= $device->title ?></td>
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