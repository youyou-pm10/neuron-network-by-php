<?php

include './lib/run.func.php';

$network = unserialize(file_get_contents('./model/current/net.txt'));
$network->neurons = study($network, 1000, 0.3, $network->neurons);
test($network, $network->neurons);
//test($network, $network->neurons);
