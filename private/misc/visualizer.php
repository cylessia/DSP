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