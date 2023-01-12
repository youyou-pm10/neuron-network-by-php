<?php
include './lib/run.func.batch.abs.php';

function layerCreate(array $style, $name){
    $layer = [];
    foreach ($style as $kind => $number){
        for ($i = 0; $i < $number; $i++){
            $neuron = 'Neuron'.$kind;
            $layer[] = new $neuron($name);
            $name ++;
        }
    }
    return array(0 => $layer, 1 => $name);
}
// $layer = layerCreate(array('A' => 4, 'C'=> 6));
function netContainerCreate(array $netModel){
    $depth = count($netModel);
    for($i = 0; $i + 1 < $depth; $i++){
        foreach ($netModel[$i] as $neuron){
            $neuron->connection($netModel[$i + 1]);
        }
    }
    return $netModel;
}
//$netModel[0] = layerCreate(array('A' => 4, 'C'=> 6));
//$netModel[1] = layerCreate(array('C'=> 10));
//$netContainer = netContainerCreate($netModel);
abstract class WeightRand{
    public function gaussianRandom($mean, $variance, $precision){
        // Box-Muller变换
        $range = pow(10, 15);
        $u = mt_rand(0, $range) / $range;
        $v = mt_rand(0, $range) / $range;
        $x = sqrt(-2 * log($u)) * cos(2 * pi() * $v);
        $x = $mean + $x * sqrt($variance);
        return round($x, $precision);
    }
    /*
    $test = '';
    for ($i=0;$i<100000;$i++){
        $test .= gaussianRandom(0, 0.01, 3).PHP_EOL;
    }
    file_put_contents('rand.txt', $test);
    */
    abstract public function init($input, $output, $precision);
}
class ReluWeightRand extends WeightRand{
    public function init($input, $output, $precision)
    {
        $variance = 4 / ($input + $output);
        return $this->gaussianRandom(0, $variance, $precision);
    }
}
class ListWeightRand extends WeightRand{
    public function init($input, $output, $precision)
    {
        $list = Array(-1,1);
        return $list[mt_rand(0,1)];
    }
}

function netInit($netContainer, $weightRand, $precision = 16){
    $depth = count($netContainer);
    $network = new Network($precision);
    for($i = 0; $i + 1 < $depth; $i++){
        foreach ($netContainer[$i] as $neuron){
            //$network->neurons[] = &$neuron;
            $network->refreshWeight($neuron, $netContainer[$i + 1], Array('api'=>$weightRand, 'parameter1'=>count($netContainer[$i]), 'parameter2'=>count($netContainer[$i + 1]), 'precision'=>$network->precision));
        }
    }
    foreach ($netContainer[0] as $neuronStart){
        $network->refreshWeight(null, $neuronStart, 1);
    }/*
    foreach ($netContainer[$depth - 1] as $finalNeuron){
        $network->neurons[] = &$finalNeuron;
    }*/
    // 考虑“ & ”中
    $network->layers = $netContainer;
    foreach ($network->layers as &$layer){
        foreach ($layer as $neuron){
            $network->neurons[] = $neuron;
        }
    }
    return $network;
}
$layer1 = layerCreate(array('B' => 1), 0);
$layer2 = layerCreate(array('C' => 2), $layer1[1]);
$layer3 = layerCreate(array('B' => 1), $layer2[1]);

$netModel[0] = $layer1[0];
$netModel[1] = $layer2[0];
$netModel[2] = $layer3[0];

$netContainer = netContainerCreate($netModel);

$num = 0;

$t1 = hrtime(true);
$input = 0;
$rate = 0;
file_put_contents('lossMap.txt', '');
while(empty($network->loss) or $network->loss > 0.01 ){
//    $network = netInit($netContainer, new ListWeightRand());
    $network = netInit($netContainer, new ReluWeightRand());
    $network->neurons = study($network, 1, $rate, -0.5, 0.5);
    $network->neurons = study($network, 1, $rate, -1, 1);
    $network->neurons = study($network, 1, $rate, -1.5, 1.5);
    $network->neurons = study($network, 1, $rate, -2, 2);
    $network->neurons = study($network, 1, $rate, 0, 0);
    $network->neurons = study($network, 1, $rate, 0.5, 0.5);
    $network->neurons = study($network, 1, $rate, 1, 1);
    $network->neurons = study($network, 1, $rate, 1.5, 1.5);
    $network->neurons = study($network, 1, $rate, 2, 2);

    $num++;
    $network->loss = $network->lossMap / 9;
    $file = fopen("lossMap.txt", "a");
    fwrite($file, ($network->lossMap / 9).PHP_EOL);
    fclose($file);
    if($network->loss == 0){
        break;
    }
    //file_put_contents('./model/result/a-net'.$num.'-'.mt_rand(0, 100).'-'.date('m-d-H：i').'-abs.txt', serialize($network));

}

file_put_contents('./model/result/net'.$num.'-'.mt_rand(0, 100).'-'.date('m-d-H：i').'-abs.txt', serialize($network));

$t2 = hrtime(true);
echo ($t2 - $t1) / 1e9;

//test($network, $network->neurons);