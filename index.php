<?php

function getColor($idx){
    /**if($idx <= 1) return 'red';
    if($idx < 17) return 'blue';
    if($idx < 64) return 'yellow';
    if($idx < 256) return 'orange';
    if($idx < 512) return 'purple';
    if($idx < 1024) return 'green';/**/
    return 'white';
}

$musics = array_diff(scandir('musics/'), array('.', '..'));
$current = $musics[array_rand($musics)];
$bgs = array_diff(scandir('bgs/'), array('.', '..'));
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
      /* FF seems to need explicit dimensions */
      #player {
          position: absolute;
          border: 1px solid black;
          height: 500px;
          width: 1000px;
          background-color: #000;
          overflow: hidden;
      }

      #playlist {
          position: absolute;
          left:1009px;
          height: 501px;
          overflow:auto;
      }

      #playlist div {
          background-color:#888;
          color:#eee;
          border-top:1px solid #aaa;
          padding: 5px;
      }

      #playlist div:hover {
          cursor: pointer;
          background-color: #444;
      }

      #bg {
          height:550px;
          width:1100px;
          position: relative;
          top: <?php echo $bgTop;?>px;
          left: <?php echo $bgLeft;?>px;
          background-image: url('bgs/<?php echo $bgs[array_rand($bgs)]; ?>');
          background-size: cover;
          background-position: center center;
      }

      #logo {
          position:absolute;
          left: <?php echo $logoLeft; ?>px;
          top: <?php echo $logoTop;?>px;
          background-image: url('logo.png');
          width: <?php echo $logoSize ;?>px;
          height: <?php echo $logoSize ;?>px;
          background-size: contain;
          z-index: 20;
          opacity: 1;
      }
      svg {
          z-index: 10;
          position: absolute;
          top: 0;
      }

      #playlist div.selected {
          background-color:white;
          color:black;
      }

      audio {
        position:absolute;
        top: 509px;
        width: 1250px;
      }
    </style>
  </head>
  <body>
    <?php
    $ratio = 1;
    $hzSize = 1024;
    $freqSize = 192;
    $x = 500;
    $y = 250;
    $r1 = 65;
    $r2 = 95;
    $idx = 1;
    $count = $freqSize;
    $minLength = 5;
    $a = 360/$count;
    $pi = M_PI+0.75;
    ?>

    <div id="player">
          <div id="bg" onclick="switchPlayerState();"></div>
          <div id="logo"> </div>
        <svg width="1000" height="500" xmlns="http://www.w3.org/2000/svg">
        <?php
    for($j = 0; $j < $count; ++$j){
        $angle = deg2rad($a*$j)+$pi;
    ?>
    <line id="line<?php echo $j; ?>"
          x1="<?php echo $x+$r1*cos($angle); ?>"
          y1="<?php echo $y+$r1*sin($angle); ?>"
          x2="<?php echo $x+(($r2-$r1)*$minLength/100+$r1)*cos($angle) ?>"
          y2="<?php echo $y+(($r2-$r1)*$minLength/100+$r1)*sin($angle);?>"
          style="fill:white;stroke:<?php echo getColor($idx*$j);?>;stroke-width:2;" />
    <?php } ?>
        </svg>
      </div>
      <div id="playlist">
          <?php foreach($musics as $music){
              echo '<div id="' . $music . '" ' . ($music == $current ? 'class="selected"' : '') . ' onclick="play(\'' . addcslashes($music, '\'') . '\')">' . $music . '</div>' . "\n";
          } ?>
      </div>
      <audio controls id="audioElement" volume="1">
          <source src="musics/<?php echo $current; ?>" type="audio/mpeg">
<!--          <source src="musics/Zeds Dead - Rude Boy HD.mp3" type="audio/mpeg">-->
<!--          <source src="musics/Spag Heddy - Mariposa.mp3" type="audio/mpeg">-->
      </audio>
  </body>
  <script type="text/javascript">
        var cx = <?php echo $x;?>;
        var cy = <?php echo $y; ?>;
        var logo = document.getElementById('logo');
        var bg = document.getElementById('bg');
        var audioCtx = new (window.AudioContext || window.webkitAudioContext)();
        var audioElement = document.getElementById('audioElement');
        //audioElement.volume = 0.1;
        var audioSrc = audioCtx.createMediaElementSource(audioElement);
        var analyser = audioCtx.createAnalyser();
        var frequencyData = new Uint8Array(1024);
        var bgs = [
            <?php echo '"' . implode('","', $bgs) . '"'; ?>
        ];
        var playlist = document.getElementById('playlist');
        var BEAT_MIN = 192;
        audioSrc.connect(analyser);
        audioSrc.connect(audioCtx.destination);
        idx = <?php echo $idx;?>;
        var spectrumInterval = setInterval(renderSpectrum,9);
        audioElement.onended = function(){
            play(playlist.children[Math.floor(Math.random() * playlist.childElementCount)].getAttribute('id'));
        };

        var beat = false, beatCutOff = 0, lastAvg = 256, elapsed = 12, beatThreshold = 30, lastMinAvg = 256;
        function renderSpectrum(){
            if(!audioElement.paused){
                analyser.getByteFrequencyData(frequencyData);
                var sum = frequencyData.reduce(function(pv, cv) { return pv + cv; }, 0);
                var move = (sum/262144)*60;
                move *= move;

                var beatData = frequencyData.slice(2,16);


                var avg = beatData.reduce(function(pv, cv) { return pv + cv; }, 0)/beatData.length;
                    if(lastAvg > beatCutOff && avg > BEAT_MIN && (avg - lastMinAvg) > beatThreshold){

                    //console.log('BEAT calc');
                    //console.log(lastMinAvg);
                    //console.log(lastAvg + ' ' + beatCutOff);
                    //console.log(avg + ' ' + BEAT_MIN);
                    //console.log((avg - lastMinAvg) + ' ' + beatThreshold);

                    beat += 0.75;
                    beat = Math.min(beat, 2);
                    beatCutOff = avg*1.2;
                    lastMinAvg = lastAvg = avg;
                } else {
                    beat *= 0.9;
                    beat = Math.max(beat, 0);
                    beatCutOff *= 0.95;
                    beatCutOff = Math.max(beatCutOff, BEAT_MIN);
                }
                lastMinAvg = Math.min(lastMinAvg, avg);
                if(beat){
                    logo.style.width = (<?php echo ($logoSize);?>*(1+beat))+"px";
                    logo.style.height = (<?php echo $logoSize;?>*(1+beat))+"px";
                    beatMove = <?php echo -$logoSize; ?>*beat/2;
                } else {
                    logo.style.width = "<?php echo ($logoSize);?>px";
                    logo.style.height = "<?php echo ($logoSize);?>px";
                    beatMove = 0;
                }
                //Math.random()*sum/261120
                rand = Math.round((Math.random()*move-(move/10)))/100;
                cx = <?php echo $x; ?> + rand;
                cy = <?php echo $y; ?> + rand;
                a = 360/(<?php echo $count;?>) * 0.017453292519943295;
                var l = <?php echo $count; ?>;
                var j = 0;

                //logo.style.width = (<?php echo $logoSize;?> ) + "px";
                //logo.style.height = (<?php echo $logoSize;?>) + "px";
                logo.style.left = (<?php echo $logoLeft;?>+rand+beatMove) + "px";
                logo.style.top = (<?php echo $logoTop;?>+rand+beatMove) + "px";
                logo.style.backgroundSize = "100% 100%";

                for(var i = 0, j = 0; j < l; i+=idx, ++j){
                    //console.log(i);
                    f = frequencyData[i];
                    data = f < <?php echo $minLength; ?> ? <?php echo $minLength; ?> : f;
                    angle = a*j+<?php echo $pi; ?>;
                    p = data/256;
                    p=p*p*p;
                    e = document.getElementById('line'+j);
                    e.setAttribute('x1', cx+<?php echo $r1;?>*(1+beat)*Math.cos(angle));
                    e.setAttribute('y1', cy+<?php echo $r1;?>*(1+beat)*Math.sin(angle));
                    e.setAttribute('x2', cx+(<?php echo ($r2-$r1);?>*data/100*p*(1+beat*1.1)    +<?php echo $r1;?>)*Math.cos(angle));
                    e.setAttribute('y2', cy+(<?php echo ($r2-$r1);?>*data/100*p*(1+beat*1.1)+<?php echo $r1;?>)*Math.sin(angle));
                }
                //console.log(j + ' lines');
                //clearInterval(spectrumInterval);
                //console.log(frequencyData);
            }
        }

        function play(music){
            selected = document.getElementsByClassName('selected');
            console.log(selected);
            selected[0].setAttribute('class', '');
            document.getElementById(music).setAttribute('class', 'selected');
            console.log(document.getElementById(music));
            if(('musics/' + music) != audioElement.getAttribute('src')){
                bg.style.backgroundImage = "url('bgs/" + bgs[Math.floor(Math.random()*(bgs.length))] + "')";
                if(!audioElement.paused){
                    audioElement.pause();
                }
                audioElement.setAttribute('src', 'musics/' + music);
                audioElement.play();
            }
        }

        function switchPlayerState(){
            console.log('foo');
            if(!audioElement.paused){
                audioElement.play();
            } else {
                audioElement.pause();
            }
        }
  </script>
</html>