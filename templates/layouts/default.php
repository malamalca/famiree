<?php
  use App\Core\App;
?>
<!doctype html>

<html lang="en">
<head>
    <meta charset="utf-8">

    <title>Famiree :: Your Family Tree</title>
    <meta name="description" content="ARHIM orodja">
    <meta name="author" content="ARHIM d.o.o.">

    <link rel="stylesheet" href="<?= App::url("/css/main.css") ?>">
</head>

<body>
<body translate="no" >
  <div id="container">
      <?php include TEMPLATES . 'elements' . DS . 'header.php'; ?>
      <div id="content">
        <?= App::flash() ?>

        <?php
          if (!empty($sidebar)) {
        ?>
        <div id="sidebar">
        <?php require TEMPLATES . 'elements' . DS . 'sidebar' . DS . $sidebar . '.php'; ?>
        </div>
        <?php
          }
        ?>

        <div id="main"<?php if (isset($sidebar)) echo ' style="margin-left: 230px;"'; ?>>
          <?= $contents ?>
			  </div>

        <div id="footer">
          &copy; Famiree.com 2009-2019
        </div>
      </div>
		</div>
	</div>
</body>

</html>
