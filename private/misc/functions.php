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

function getFileList($directory){
    return array_diff(scandir($directory), array('.', '..'));
}

function arrayRandomValue(&$array){
    return $array[array_rand($array)];
}