<?php
    $this->set('sidebar', 'Profile' . DS . 'dashboard');
    $this->set('title', _('Dashboard'));
?>
    <div id="DashboardSidebar">
        <div class="panel">
        <div class="inner">
        <div class="legend"><?= _('Search') ?></div>
        <?php
            echo $this->Form->create(null, [
                'type' => 'GET',
                'action' => $this->url('/profiles/index')
            ]);
            echo '<fieldset class="center" id="DashboardSearchFieldset">';
            echo $this->Form->text('criterio', ['size' => 15]);
            echo $this->Form->button('Go', ['type' => 'submit']);
            echo '</fieldset>';
            echo $this->Form->end();
            ?>
        </div>
        </div>
    </div>
<div id="DashboardMain">
    <div>
        <div class="message" id="WelcomeMessage">
            <br />
            <?php
                echo '<b>';
                echo _('Welcome to Famiree!');
                echo '</b>';
                echo '<br />';
                echo _('This is a private place for your family to build your family tree, preserve your history and share your lives.');
            ?>
            <br /><br />
        </div>

        <div id="DashboardDates">
            <?php
                $hasToday = false;
                $hasTomorrow = false;
                $thisMonth = false;
                $nextMonth = false;
                $inFuture = false;

            if (!empty($birthdays)) {
                foreach ($birthdays as $profile) {
                    $bdShowDate = true;
                    $oneIfFuture = 1;

                    $dobReal = \Cake\Chronos\Chronos::parse($profile->dob);
                    $dob = $dobReal->year((new FrozenDate())->year);


                    if ($dob->isToday() && empty($hasTodayCaption)) {
                        echo '<div class="date_title">' . _('Today') . '</div>'.PHP_EOL;
                        $bdShowDate = false;
                        $oneIfFuture = 0;
                        $hasTodayCaption = true;
                    } elseif ($dob->isTomorrow() && empty($hasTommorowCaption)) {
                        echo '<div class="date_title">'._('Tomorrow').'</div>'.PHP_EOL;
                        $bdShowDate = false;
                        $hasTommorowCaption = true;
                    } elseif ($dob->isThisMonth() && empty($thisMonth)) {
                        echo '<div class="date_title">'._('This month').'</div>'.PHP_EOL;
                        $thisMonth = true;
                    } elseif ($dob->isNextMonth() && empty($nextMonth)) {
                        echo '<div class="date_title">'._('Next month').'</div>'.PHP_EOL;
                        $nextMonth = true;
                    } elseif ($dob->diffInMonths((new FrozenDate())->day(1)) > 1 && empty($inFuture)) {
                        echo '<div class="date_title">'._('Furher on').'</div>'.PHP_EOL;
                        $inFuture = true;
                    }

                    echo '<div class="date_birthday">';
                    echo $this->Html->image('ico_cake.gif');
                    if ($bdShowDate) {
                        $bd_string = _('%1$s celebrates %2$s %3$d birthday on %4$s.');
                    } else {
                        $bd_string = _('%1$s celebrates %2$s %3$d birthday.');
                    }
                    printf(
                        $bd_string,
                        $this->Html->link($profile->d_n, ['action' => 'view', $profile->id]),
                        ($profile->g == 'm') ? _('his') : _('her'),
                        $this->Famiree->age($dobReal) + $oneIfFuture,
                        '<b>' . $dobReal->i18nFormat(Configure::read('noYearDateFormat')) . '</b>'
                    );
                    echo '</div>'.PHP_EOL;
                }
            }
            ?>
        </div>
    </div>
</div>
