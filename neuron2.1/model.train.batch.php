<?php

include './lib/run.func.batch.abs.php';

$network = unserialize(file_get_contents('./model/currentBatch/net.txt'));
$rate = 0.0001;
$num = 0;
// 清空loss
file_put_contents("lossMap.txt", "");
$t1 = hrtime(true);
while($network->loss > 0.000001 and $num < 1000){
    $network->neurons = study($network, 1, $rate, 2.26, 0.772);
    $network->neurons = study($network, 1, $rate, 1.32, 0.969);
    $network->neurons = study($network, 1, $rate, 0.62, 0.581);
    $network->neurons = study($network, 1, $rate, 0.17, 0.169);
    $network->neurons = study($network, 1, $rate, 1.92, 0.94);
    $network->neurons = study($network, 1, $rate, 2.9, 0.239);
    $network->neurons = study($network, 1, $rate, 0.28, 0.276);
    $network->neurons = study($network, 1, $rate, 1.13, 0.904);
    $network->neurons = study($network, 1, $rate, 0.71, 0.652);
    $network->neurons = study($network, 1, $rate, 0.06, 0.06);
    $network->neurons = study($network, 1, $rate, 2.28, 0.759);
    $network->neurons = study($network, 1, $rate, 0.74, 0.674);
    $network->neurons = study($network, 1, $rate, 0.24, 0.238);
    $network->neurons = study($network, 1, $rate, 1.03, 0.857);
    $network->neurons = study($network, 1, $rate, 0.4, 0.389);
    $network->neurons = study($network, 1, $rate, 1.86, 0.958);
    $network->neurons = study($network, 1, $rate, 1.62, 0.999);
    $network->neurons = study($network, 1, $rate, 0.79, 0.71);
    $network->neurons = study($network, 1, $rate, 2.84, 0.297);
    $network->neurons = study($network, 1, $rate, 0.9, 0.783);
    $network->neurons = study($network, 1, $rate, 2.67, 0.454);
    $network->neurons = study($network, 1, $rate, 1.28, 0.958);
    $network->neurons = study($network, 1, $rate, 1.66, 0.996);
    $network->neurons = study($network, 1, $rate, 0.21, 0.208);
    $network->neurons = study($network, 1, $rate, 2.02, 0.901);
    $network->neurons = study($network, 1, $rate, 2.63, 0.49);
    $network->neurons = study($network, 1, $rate, 0.79, 0.71);
    $network->neurons = study($network, 1, $rate, 2.44, 0.645);
    $network->neurons = study($network, 1, $rate, 2.6, 0.516);
    $network->neurons = study($network, 1, $rate, 2.66, 0.463);
    $network->neurons = study($network, 1, $rate, 2.44, 0.645);
    $network->neurons = study($network, 1, $rate, 2.79, 0.344);
    $network->neurons = study($network, 1, $rate, 2.71, 0.418);
    $network->neurons = study($network, 1, $rate, 1.28, 0.958);
    $network->neurons = study($network, 1, $rate, 0.75, 0.682);
    $network->neurons = study($network, 1, $rate, 3.13, 0.012);
    $network->neurons = study($network, 1, $rate, 0, 0);
    $network->neurons = study($network, 1, $rate, 0.61, 0.573);
    $network->neurons = study($network, 1, $rate, 0.52, 0.497);
    $network->neurons = study($network, 1, $rate, 0.98, 0.83);


    $num++;
    $network->loss = $network->lossMap / 40;
    $file = fopen("lossMap.txt", "a");
    fwrite($file, ($network->lossMap / 40).PHP_EOL);
    fclose($file);
    if($network->loss == 0){
        break;
    }
    $network->lossMap = 0;
    //file_put_contents('./model/result/a-net'.$num.'-'.mt_rand(0, 100).'-'.date('m-d-H：i').'-abs.txt', serialize($network));

}
$t2 = hrtime(true);
echo ($t2 - $t1) / 1e9;
file_put_contents('./model/result/net'.$num.'-'.mt_rand(0, 100).'-'.date('m-d-H：i').'-abs.txt', serialize($network));
//$network->neurons = study($network, 100, 0, $network->neurons);
//test($network);
//test($network, $network->neurons);
