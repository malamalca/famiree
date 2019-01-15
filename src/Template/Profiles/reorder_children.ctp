<?php
    $this->set('sidebar', 'Profiles/reorder_children');
?>
<h1><?= __('Reorder Children') ?></h1>
<?php
    echo $this->Html->script('jquery-ui-personalized-1.6rc6.min');
?>
<style type="text/css">
    .sortable { margin: 0; padding: 0; width: 60%; margin-bottom: 0; }
    .sortable li { cursor: ns-resize; margin: 0 3px 3px 3px; padding: 0.4em; padding-left: 1.5em; font-size: 1.4em; height: 18px; }
</style>

<div class="demo">
    <?php
        echo $this->Form->create(null);

        $k = 0;
        foreach ($marriages as $i => $marriage) {
            if (isset($marriage['spouse'])) {
                $spouse = $marriage['spouse']->d_n;
            } else {
                $spouse = __('unknown');
            }
    ?>
    <div class="panel">
    <div class="legend"><?= h($spouse) ?></div>
    <ul class="sortable" id="sortable<?= $i ?>">
        <?php
            foreach ($marriage['children'] as $j => $child) {
        ?>
        <li id="li<?php echo $child->id; ?>"> &rsaquo;
        <?php
            echo h($child->d_n);
            echo $this->Form->hidden('units.' . $k . '.sort_order', ['value' => $j, 'id' => 'so_li' . $child->id]);
            echo $this->Form->hidden('units.' . $k . '.id', ['value' => $child->_matchingData['Units']->id]);
        ?></li>
        <?php
                $k++;
            }
        ?>
    </ul>
    </div>
    <script type="text/javascript">
        $(function() {
            $("#sortable<?= $i ?>").sortable({ cursor: 'crosshair', update: function(event, ui) {
                var els = $(this).sortable('toArray');
                var i = 0;
                $.each(els, function() {
                    $("#so_" + this).val(i);
                    i++;
                });
            }});

        });
    </script>
    <?php
        }

        echo '<div class="input submit">';
        echo $this->Form->button(__('Save'), ['type' => 'submit']);
        echo ' '.__('or').' '.$this->Html->link(__('Cancel'), ['action' => 'view', $profile->id]);
        echo '</div>';

        echo $this->Form->end();
    ?>
</div>
