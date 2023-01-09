<?php


include 'neuron.class.php';

function study($network, $count, $rate = 0.0003, $neurons)
{
    for ($i = 0; $i < $count; $i++) {
        echo '学习“加法”的训练' . ($i+1) . '次', PHP_EOL;
        $input = 0.5;
        // 接受刺激，反射开始
        $network->excite($neurons['start1'], $input);
        $network->excite($neurons['start2'], $input);
        // 反射完成，开始训练
        if ($network->reflex === 1) {
            $loss = $network->loss2($input, $network->predict);
            // 记录总体loss值，以便后续生成loss图像
            $network->lossMap .= $loss . PHP_EOL;
            $network->train($neurons['finish'], $loss);
            $network->feedback($neurons, $rate);
            $network->reflex = 0;
        }
        foreach ($neurons as $neuron) {
            $neuron->input = 0;
            $neuron->output = 0;
            $neuron->inputLoss = Array();
            $neuron->outputLoss = 0;
        }
    }
    file_put_contents('lossMap.txt', $network->lossMap);
    //echo '学习“加法”的训练结束', PHP_EOL;
    return $neurons;
}

function test($network, $neurons)
{
    echo '考试开始', PHP_EOL;
    // 重置结果记录
    //file_put_contents('result.txt','');
    for ($x = 0; $x < 1; $x++) {
        $input = 0.5;
        // 接受刺激，反射开始
        $network->excite($neurons['start1'], $input);
        $network->excite($neurons['start2'], $input);
        // 预期输出
        //$input = sin($input);
        echo $input. '最终预测结果: ' . $network->predict, PHP_EOL;
        // 神经元恢复初始状态
        foreach ($neurons as $neuron) {
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


