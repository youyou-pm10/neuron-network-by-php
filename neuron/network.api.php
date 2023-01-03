<?php
include 'neuron.class.php';

function study($network, $neuron1, $count, $rate = 0.0003)
{
    for ($i = 0; $i < $count; $i++) {
        echo '学习“加法”的训练' . $i . '次', PHP_EOL;
        //$input[0] = mt_rand(0, 10) /10;
        //$input[1] = mt_rand(0, 10) /10;
        $input = mt_rand(0, 10) / 10;
        // 接受刺激，反射开始
        $network->excite($neuron1, $input);
        //$network->excite($neuron2, $input);
        // 反射完成，开始训练
        if ($network->reflex === 1) {
            //$input = -1 * $input + 2;
            $network->train($input, $network->predict);
            $network->feedback($input, $rate);
            $network->reflex = 0;
        }
        $network->weights = $network->result;
        foreach ($network->network as $neuron) {
            $neuron->input = 0;
            //$neuron->output = 0;
        }
    }
    file_put_contents('lossMap.txt', $network->text);
    echo '学习“加法”的训练结束', PHP_EOL;
}
function test($network, $neuron1, $neuron2)
{
    echo '考试开始', PHP_EOL;
    for ($x = 0; $x < 5; $x++) {
        // 判断第一个数是不是第二个数
        /*
        $input[0] = mt_rand(0, 10)/10;
        $input[1] = mt_rand(0, 10)/10;
        */
        $input = mt_rand(0, 10) / 10;
        // 接受刺激，反射开始
        $network->excite($neuron1, $input);
        $network->excite($neuron2, $input);
        // $input = -1 * $input + 2;
        echo $input . '最终预测结果: ' . $network->predict, PHP_EOL;
        // 神经元恢复初始状态
        foreach ($network->network as $neuron) {
            $neuron->input = 0;
            //$neuron->output = 0;
        }
        record($network);
        resultMap($network, $input);
        innerLoss($network);
    }
}
// 输出某个神经元loss数据
function innerLoss($network){
    $test = '';
    $test .= $network->weights[0]['loss'].PHP_EOL;
    file_put_contents('testLossMap.txt', $test);
}
// 输出结果数据
function resultMap($network, $input){
    $result = '';
    $result .= $input.'|'.$network->predict.PHP_EOL;
    file_put_contents('result.txt', $result);
}
// 记录权重数据
function record($network){
    $record = null;
    foreach ($network->weights as $weight) {
        $record .= $weight['from']->name . ' | ' . $weight['to']->name . ' | ' . $weight['weight'] . ' | ' . $weight['bias'] . PHP_EOL;
    }
    //var_dump($network->weights);
    // 储存每次训练后的模型数据
    file_put_contents('data.txt', $record);
}
/*
$neuron1 = new NeuronA(1);
$neuron2 = new NeuronA(2);
$neuron3 = new NeuronA(3);
$neuron4 = new NeuronA(4);
$neuron5 = new NeuronA(5);
$neuron6 = new NeuronA(6);
$neuron7 = new NeuronB(7);
*/

$neuron1 = new NeuronC(1);
$neuron2 = new NeuronC(2);
$neuron3 = new NeuronC(3);
$neuron4 = new NeuronC(4);
$neuron5 = new NeuronC(5);
$neuron6 = new NeuronC(6);
$neuron7 = new NeuronB(7);

// 神经网络第二层
$neuron1->connection($neuron3);
$neuron1->connection($neuron4);
$neuron1->connection($neuron5);
$neuron1->connection($neuron6);
/*$neuron2->connection($neuron3);
$neuron2->connection($neuron4);
$neuron2->connection($neuron5);
$neuron2->connection($neuron6);*/
// 神经网络第三层
$neuron3->connection($neuron7);
$neuron4->connection($neuron7);
//$neuron5->connection($neuron7);
//$neuron6->connection($neuron7);

$network = new Network();
/*
$network->refreshWeight($neuron3, $neuron7, 0.175, 12.621);
$network->refreshWeight($neuron4, $neuron7, 0.284, 0.098);
$network->refreshWeight($neuron5, $neuron7, 0.072, 0.39);
$network->refreshWeight($neuron6, $neuron7, 0.148, 0.574);
$network->refreshWeight($neuron1, $neuron6, 1.225, 0.357);
//$network->refreshWeight($neuron2, $neuron6, 0.691, 1.102);
$network->refreshWeight($neuron1, $neuron5, 1.3, 1.206);
//$network->refreshWeight($neuron2, $neuron5, 1.626, 0.272);
$network->refreshWeight($neuron1, $neuron4, 1.214, 0.073);
//$network->refreshWeight($neuron2, $neuron4, 0.247, 0.084);
$network->refreshWeight($neuron1, $neuron3, 1.046, 0.145);
//$network->refreshWeight($neuron2, $neuron3, 1.027, 0.045);
//$network->refreshWeight(null, $neuron2, 1.618, 0.378);
$network->refreshWeight(null, $neuron1, 1.799, -0.332);
*/

//$network->refreshWeight($neuron5, $neuron7, 0.07216, 0.389905);
//$network->refreshWeight($neuron6, $neuron7, 0.14807, 0.573683);
//$network->refreshWeight($neuron1, $neuron6, 1.225173, 0.356694);
//$network->refreshWeight($neuron1, $neuron5, 1.300116, 1.205819);
/*
$network->refreshWeight($neuron3, $neuron7, 0.174876, 12.620433);
$network->refreshWeight($neuron4, $neuron7, 0.28413, 0.097316);
$network->refreshWeight($neuron1, $neuron4, 1.214287, 0.072325);
$network->refreshWeight($neuron1, $neuron3, 1.046175, 0.144694);
$network->refreshWeight(null, $neuron1, 1.797137, -0.337406);*/
$network->refreshWeight($neuron3, $neuron7, 0.175, 12.621);
$network->refreshWeight($neuron4, $neuron7, 0.284, 0.098);
$network->refreshWeight($neuron5, $neuron7, 0.072, 0.39);
$network->refreshWeight($neuron6, $neuron7, 0.148, 0.574);
$network->refreshWeight($neuron1, $neuron6, 1.225, 0.357);
$network->refreshWeight($neuron1, $neuron5, 1.3, 1.206);
$network->refreshWeight($neuron1, $neuron4, 1.214, 0.073);
$network->refreshWeight($neuron1, $neuron3, 1.046, 0.145);
$network->refreshWeight(null, $neuron1, 1.799, -0.332);

study($network, $neuron1, 100, 0.0001);
test($network, $neuron1, $neuron2);