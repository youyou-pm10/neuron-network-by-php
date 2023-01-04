<?php

include 'neuron.class.php';

function study($network, $neuron1, $neuron2, $count, $rate = 0.0003, $size)
{
    for ($i = 0; $i < $count; $i++) {
        echo '学习“加法”的训练' . $i . '次', PHP_EOL;
        //$input[0] = mt_rand(0, 10) /10;
        //$input[1] = mt_rand(0, 10) /10;
        //$input = 0.2;
        //$input = mt_rand(0, 300) /100;
        $input = 1;
        // 接受刺激，反射开始
        $network->excite($neuron1, $input);
        $network->excite($neuron2, $input);
        // 预期输出
        //$input = sin($input);
        // 反射完成，开始训练
        if ($network->reflex === 1) {
            //$input = -1 * $input + 2;
            $network->train($input, $network->predict, $size);
            $network->feedback($input, $rate);
            $network->reflex = 0;
        }
        foreach ($network->result as &$value){
            unset($value['loss']);
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
    // 重置结果记录
    //file_put_contents('result.txt','');
    for ($x = 0; $x < 100; $x++) {
        // 判断第一个数是不是第二个数
        /*
        $input[0] = mt_rand(0, 10)/10;
        $input[1] = mt_rand(0, 10)/10;
        */
        //$input = mt_rand(0, 300) /100;
        $input = 1;
        // 接受刺激，反射开始
        $network->excite($neuron1, $input);
        $network->excite($neuron2, $input);
        // 预期输出
        //$input = sin($input);
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
function innerLoss($network)
{
    $test = '';
    $test .= $network->weights[0]['loss'] . PHP_EOL;
    file_put_contents('testLossMap.txt', $test);
}

// 输出结果数据
function resultMap($network, $input)
{
    $result = $input . '|' . $network->predict . PHP_EOL;
    $file = fopen("result.txt","a");
    fwrite($file, $result);
    fclose($file);
}

// 记录权重数据
function record($network)
{
    $record = null;
    foreach ($network->weights as $weight) {
        $record .= $weight['from']->name . ' | ' . $weight['to']->name . ' | ' . $weight['weight'] . ' | ' . $weight['bias'] . PHP_EOL;
    }
    //var_dump($network->weights);
    // 储存每次训练后的模型数据
    file_put_contents('data.txt', $record);
}


$neuron1 = new NeuronC(1);
$neuron2 = new NeuronC(2);
$neuron3 = new NeuronC(3);
$neuron4 = new NeuronC(4);
$neuron5 = new NeuronC(5);
$neuron6 = new NeuronC(6);
$neuron7 = new NeuronB(7);

$neuron1->connection($neuron3);
$neuron2->connection($neuron3);
$neuron1->connection($neuron4);
$neuron2->connection($neuron4);
$neuron1->connection($neuron5);
$neuron2->connection($neuron5);
$neuron1->connection($neuron6);
$neuron2->connection($neuron6);
$neuron3->connection($neuron7);
$neuron4->connection($neuron7);
$neuron5->connection($neuron7);
$neuron6->connection($neuron7);

$network = new Network();


$network->refreshWeight($neuron5, $neuron7, 2, 0.984334);
$network->refreshWeight($neuron6, $neuron7, 1, 0.869428);
$network->refreshWeight($neuron2, $neuron6, 2, 0.984334);
$network->refreshWeight($neuron1, $neuron6, -5.996392, 0.869431);
$network->refreshWeight($neuron2, $neuron5, 2.99016, 0.984334);
$network->refreshWeight($neuron1, $neuron5, -0.011726, 0.869428);
$network->refreshWeight($neuron1, $neuron4, 8.562112, -0.766505);
$network->refreshWeight($neuron2, $neuron4, 3.592992, -0.689394);
$network->refreshWeight($neuron1, $neuron3, -0.00304, -1.000867);
$network->refreshWeight($neuron2, $neuron3, -0.03531, -0.356114);
$network->refreshWeight(null, $neuron2, 2.717499, -1.129114);
$network->refreshWeight(null, $neuron1, 5.05128, 2.653415);

// 重置结果记录
file_put_contents('result.txt','');
//test($network, $neuron1, $neuron2);
study($network, $neuron1, $neuron2, 100, 0.0000001, 7);
test($network, $neuron1, $neuron2);
