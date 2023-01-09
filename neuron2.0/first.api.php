<?php
include './lib/run.func.php';

$neuron1 = new NeuronC(1);
$neuron2 = new NeuronA(2);
$neuron3 = new NeuronA(3);

$neuron1->connection($neuron3);
$neuron2->connection($neuron3);

$network = new Network();
$network->refreshWeight($neuron1, $neuron3, -1);
$network->refreshWeight($neuron2, $neuron3, 3);
$network->refreshWeight(null, $neuron2, 1);
$network->refreshWeight(null, $neuron1, 1);

// 重置结果记录
file_put_contents('result.txt', '');
//test($network, $neuron1, $neuron2);
/*
for($c=0; $c < 100; $c++){
    study($network, $neuron1, $neuron2, 100, 0.000000003, 9);
    echo $c.'-------------',PHP_EOL;
}*/
//$t1 = hrtime(True);
$neurons = Array('finish' =>$neuron3, 'start2' =>$neuron2, 'start1' =>$neuron1);
$neurons = study($network, 1000, 0.3, $neurons);
$network->neurons = $neurons;
file_put_contents('./model/net.txt', serialize($network));
//$t2 = hrtime(True);
//echo ($t2 - $t1) / 1e9, PHP_EOL;
//$neurons = unserialize(file_get_contents('./model/arr.txt'));
$network = unserialize(file_get_contents('./model/net.txt'));
test($network, $network->neurons);
//test($network, $neurons);
