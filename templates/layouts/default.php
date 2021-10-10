<?php
  use App\Core\App;
?>
<!doctype html>

<html lang="en">
<head>
    <meta charset="utf-8">

    <title>Thorbell :: A Smart Doorbell</title>
    <meta name="description" content="ARHIM orodja">
    <meta name="author" content="ARHIM d.o.o.">

    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/milligram/1.3.0/milligram.min.css'>
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/modern-normalize/0.5.0/modern-normalize.min.css'>
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.8.1/css/all.min.css'>
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/ionicons/4.5.6/css/ionicons.min.css'>

    <link rel="stylesheet" href="<?= App::url("/css/main.css") ?>">
</head>

<body>
<body translate="no" >
  <?= App::flash() ?>

  <?php
    $sidebarOpen = App::isLoggedIn() ? ' class="sidebar-open"' : '';
  ?>
  <div id="page-container"<?= $sidebarOpen ?>>
    
    <?php
      if (App::isLoggedIn()) {
        include dirname(__FILE__) . DS . 'sidebar.php';
      }
    ?>

  <div id="page-content" class="container-fluid">
    <?php
      if ($title !== false) {
    ?>
    <header id="page-header">
      <div class="row">
        <div class="column">
          <section class="actions float-left">
          <h2>
            <a href="#" id="toggle-sidebar" class="btn"><i class="fas fa-fw fa-bars"></i></a>
            <?= $title ?></h2>
          </section>
        </div>
      </div>
    </header>
    <?php
      }
    ?>
    
    <div id="page-body">
      <div class="container title">
      </div>
          <?= $contents ?>
      </div>
    </div>
  </div>

  </div>

  <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.0/jquery.min.js'></script>
  <script id="rendered-js">
/**
 * Debounce functions for better performance
 * (c) 2018 Chris Ferdinandi, MIT License, https://gomakethings.com
 * @param  {Function} fn The function to debounce
 * https://gomakethings.com/debouncing-your-javascript-events/
 */
var debounce = function (a) {var e;return function () {var n = this,i = arguments;e && window.cancelAnimationFrame(e), e = window.requestAnimationFrame(function () {a.apply(n, i);});};};

/**
 * Main code section
 */

// calculate 40rem in px (based off body font size)
var mqw = parseInt(getComputedStyle(document.body).fontSize) * 40;

// handle responsive sidebar toggling
$('#toggle-sidebar, #close-sidebar').on('click', function () {
  if (window.innerWidth < mqw) {
    // toggle xs menu
    if ($('#page-container').hasClass('sidebar-open-xs')) {
      $('#page-container').removeClass('sidebar-open-xs');
      $('#sidebar').removeClass('open-xs');
    } else {
      $('#page-container').removeClass('sidebar-open-xs').addClass('sidebar-open-xs');
      $('#sidebar').removeClass('open-xs').addClass('open-xs');
    }
  } else {
    // toggle regular menu
    if ($('#page-container').hasClass('sidebar-open')) {
      $('#page-container').removeClass('sidebar-open');
      $('#sidebar').removeClass('open');
    } else {
      $('#page-container').removeClass('sidebar-open').addClass('sidebar-open');
      $('#sidebar').removeClass('open').addClass('open');
    }
  }
});

// check for resizing and remove XS sidebar when > 40rem
$(window).on('resize', debounce(function () {
  if (window.innerWidth > mqw) {
    $('#page-container').removeClass('sidebar-open-xs');
    $('#toggle-sidebar').hide();
  } else {
    $('#toggle-sidebar').show();
  }
}));

    </script>

</body>

</html>
