<?php


include 'neuron.class.php';

function study($network, $count, $rate, $input, $output)
{
    //$finalLayer = count($network->layers) - 1;
    for ($i = 0; $i < $count; $i++) {
        //echo '学习“加法”的训练' . ($i+1) . '次', PHP_EOL;
        //$input = 0.5;
        // 接受刺激，反射开始
        $network->excite($network->neurons[0], $input);
        //$network->excite($network->neurons[1], $input);
        // 此处不可以用循环等方法替代，性能会差到无法使用
//        array_map('$network->excite', $network->layers[0], array($input, $input));
//        foreach ($network->layers[0] as $neuronStart){
//            $network->excite($neuronStart, $input);
//        }
        // 反射完成，开始训练
        if ($network->reflex === 1) {
            $loss = $network->loss2($output, $network->predict);
            // 记录总体loss值，以便后续生成loss图像
            $network->lossMap += $loss;
            $network->train($network->neurons[5], $loss);
            // 此处不可以用循环等方法替代，性能会差到无法使用
//            foreach ($network->layers[$finalLayer] as $neuronFinal){
//                $network->train($neuronFinal, $loss);
//            }
            $network->feedback($network->neurons, $rate);
            $network->reflex = 0;
        }
        foreach ($network->neurons as $neuron) {
            $neuron->input = 0;
            $neuron->output = 0;
            $neuron->inputLoss = Array();
            $neuron->outputLoss = 0;
        }
    }
//    $network->loss = $network->lossMap / $count;
//    $file = fopen("lossMap.txt", "a");
//    fwrite($file, ($network->lossMap / $count).PHP_EOL);
//    fclose($file);
    //file_put_contents('lossMap.txt', ($network->lossMap / $count).PHP_EOL);
    //echo '学习“加法”的训练结束', PHP_EOL;
    return $network->neurons;
}

function test($network)
{
    echo '考试开始', PHP_EOL;
    // 重置结果记录
    file_put_contents('result.txt','');
    for ($x = 0; $x < 100; $x++) {
        //$input = 0.5;
        $input = mt_rand(0, round(pi(), 3) * 100) / 100;
        //$input = mt_rand(-200, 200) / 100;
        // 接受刺激，反射开始
        $network->excite($network->neurons[0], $input);
        //$network->excite($network->neurons[1], $input);
        // 预期输出
        $output = sin($input);
        echo $input. '|' .$output.'最终预测结果: ' . $network->predict, PHP_EOL;
        // 神经元恢复初始状态
        foreach ($network->neurons as $neuron) {
            $neuron->input = 0;
            $neuron->output = 0;
            $neuron->inputLoss = Array();
            $neuron->outputLoss = 0;
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
    $file = fopen("result.txt", "a");
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


