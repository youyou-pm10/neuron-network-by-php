<?php
include './lib/run.func.batch.sin-BGD.php';

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
            $network->refreshWeight($neuron, $netContainer[$i + 1], Array('api'=>$weightRand, 'parameter1'=>count($netContainer[$i]), 'parameter2'=>count($netContainer[$i + 1]), 'precision'=>$network->precision), mt_rand(-200, 200) / 100);
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
$layer2 = layerCreate(array('C' => 4), $layer1[1]);
$layer3 = layerCreate(array('C' => 16), $layer2[1]);
$layer4 = layerCreate(array('C' => 32), $layer3[1]);
$layer5 = layerCreate(array('B' => 1), $layer4[1]);

$netModel[0] = $layer1[0];
$netModel[1] = $layer2[0];
$netModel[2] = $layer3[0];
$netModel[3] = $layer4[0];

$netContainer = netContainerCreate($netModel);

$num = 0;

$t1 = hrtime(true);
$input = 0;
$rate = 0;
file_put_contents('lossMap.txt', '');
while(empty($network->loss) or $network->loss > 0.1){
//    $network = netInit($netContainer, new ListWeightRand());
    $network = netInit($netContainer, new ReluWeightRand());
//    for($i=0; $i<30; $i++){
//        for($i=0; $i<40; $i++){
//            $input = mt_rand(0, round(pi(), 3) * 100) / 100;
//            $output = round(sin($input), 3);
//            $network->neurons = study($network, 1, $rate, $input, $output);
//        }
//
//        $num++;
//        $network->loss = $network->lossMap / 40;
//        $network->train($network->neurons[24], $network->loss);
//        // 此处不可以用循环等方法替代，性能会差到无法使用
//        //            foreach ($network->layers[$finalLayer] as $neuronFinal){
//        //                $network->train($neuronFinal, $loss);
//        //            }
//        $rate = 0.01;
//        $network->feedback($network->neurons, $rate);
//    }
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

    $file = fopen("lossMap.txt", "a");
    fwrite($file, ($network->lossMap / 40).PHP_EOL);
    fclose($file);
    $num++;
    $network->loss = $network->lossMap / 40;
    //break;
    //file_put_contents('./model/result/a-net'.$num.'-'.mt_rand(0, 100).'-'.date('m-d-H：i').'-abs.txt', serialize($network));

}

file_put_contents('./model/result/net'.$num.'-'.mt_rand(0, 100).'-'.date('m-d-H：i').'-sin.txt', serialize($network));

$t2 = hrtime(true);
echo ($t2 - $t1) / 1e9;

//test($network, $network->neurons);