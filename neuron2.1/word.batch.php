<?php
// $network->neurons = study($network, 10, 0, $input, $output);
$text = '';
for($i=0; $i<40; $i++){
    $input = mt_rand(0, round(pi(), 3) * 100) / 100;
    $output = round(sin($input), 3);
    $text .= "\$network->neurons = study(\$network, 1, \$rate, $input, $output);".PHP_EOL;
}
file_put_contents('word.txt', $text);