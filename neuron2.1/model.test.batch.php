<?php

include './lib/run.func.batch.sin.php';

$network = unserialize(file_get_contents('./model/currentBatch/net.txt'));
//$network->neurons = study($network, 100, 0, $network->neurons);
test($network);
//test($network, $network->neurons);
