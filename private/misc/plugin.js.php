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
    if(!audioElement.paused){
        audioElement.play();
    } else {
        audioElement.pause();
    }
}