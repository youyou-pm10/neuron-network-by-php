<?php

include 'neuron.class.php';

function study($network, $neuron1, $neuron2, $count, $rate = 0.0003, $size)
{
    for ($i = 0; $i < $count; $i++) {
        echo '学习“加法”的训练' . $i . '次', PHP_EOL;
        //$input[0] = mt_rand(0, 10) /10;
        //$input[1] = mt_rand(0, 10) /10;
        //$input = 0.2;
        $input = mt_rand(0, 1000) /100;
        // 接受刺激，反射开始
        $network->excite($neuron1, $input);
        $network->excite($neuron2, $input);
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
        $input = mt_rand(0, 10000) / 100;
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
$neuron5 = new NeuronB(5);

$neuron1->connection($neuron3);
$neuron2->connection($neuron3);
$neuron1->connection($neuron4);
$neuron2->connection($neuron4);
$neuron3->connection($neuron5);
$neuron4->connection($neuron5);

$network = new Network();

$network->refreshWeight($neuron3, $neuron5, 1.787552, -0.000725);
$network->refreshWeight($neuron4, $neuron5, 1.971709, -0.000806);
$network->refreshWeight($neuron1, $neuron4, 3.924558, -0.009672);
$network->refreshWeight($neuron2, $neuron4, 0.987426, -0.003224);
$network->refreshWeight($neuron1, $neuron3, 1.651905, -0.006166);
$network->refreshWeight($neuron2, $neuron3, 0.983025, -0.004352);
$network->refreshWeight(null, $neuron2, 0.293162, -0.026518);
$network->refreshWeight(null, $neuron1, 0.011704, -0.126705);

// 重置结果记录
file_put_contents('result.txt','');
test($network, $neuron1, $neuron2);
//study($network, $neuron1, $neuron2, 5, 0.00001, 5);
//test($network, $neuron1, $neuron2);
