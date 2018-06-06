<?php

include '../private/misc/functions.php';

$musics = getFileList('musics/');
$current = arrayRandomValue($musics);
$bgs = getFileList('bgs/');

$logoLeft = 435;
$logoTop = 185;
$logoSize = 130;
$bgLeft = -50;
$bgTop = -25;
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="iso-8859-15" />
    <title>DSP : Dubstep Plugin Audio Visualizer</title>
    <style>
      <?php include '../private/misc/style.php'; ?>
    </style>
  </head>
  <body>
    <?php
    $x = 500;
    $y = 250;
    $r1 = 65;
    $r2 = 95;
    $idx = 1;
    $minLength = 5;
    $a = 360/192; // 192 -> Number of frequences displayed
    $pi = M_PI+0.75;
    ?>

    <div id="player">
        <div id="bg" onclick="switchPlayerState();"></div>
        <div id="logo"> </div>
        <?php include '../private/misc/visualizer.php'; ?>
      </div>
      <div id="playlist">
          <?php foreach($musics as $music){
              echo '<div id="' . $music . '" ' . ($music == $current ? 'class="selected"' : '') . ' onclick="play(\'' . addcslashes($music, '\'') . '\')">' . $music . '</div>' . "\n";
          } ?>
      </div>
      <audio controls id="audioElement" volume="1">
          <source src="musics/<?php echo $current; ?>" type="audio/mpeg">
      </audio>
  </body>
  <script type="text/javascript">
        <?php include '../private/misc/plugin.js.php'; ?>
  </script>
</html>