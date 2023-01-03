<?php
$file = file_get_contents('data.txt');
$lines = explode(PHP_EOL, $file);
$cmd = '';
foreach ($lines as $line){
    $line = explode('|', $line);
    $line = array_map('floatval', $line);
    $line[0] = '$neuron'.$line[0];
    $line[1] = '$neuron'.$line[1];
    if($line[0] === '$neuron0'){
        $line[0] = 'null';
    }
    $cmd .= '$network->refreshWeight('.$line[0].', '.$line[1].', '.$line[2].', '.$line[3].');'.PHP_EOL;
}
file_put_contents('./data/'.date('Y-m-d_H.i.s').'-cmd-n-.txt', $cmd);
