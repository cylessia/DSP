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
    background-image: url('styles/logo.png');
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