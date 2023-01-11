<?php
abstract class Neurons
{
    public $name = null;
    public $input = 0;
    public $output = 0;
    public $inputLoss = [];
    public $outputLoss = 0;
    protected $axonsBak = [];
    protected $dendritesBak = [];
    public $axons = [];
    public $dendrites = [];
    public $neurons = [];

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function sumWeight($inputs, $weights)
    {
        $this->input += floatval($weights['weight']) * floatval($inputs) + floatval($weights['bias']);
    }

    public function sumLoss($inputs, $weights)
    {
        $this->inputLoss[$weights['to']->name] = floatval($weights['weight']) * floatval($inputs) + floatval($weights['bias']);
        $this->outputLoss += floatval($weights['weight']) * floatval($inputs) + floatval($weights['bias']);
    }

    public function connection($neurons)
    {
        if (is_array($neurons)) {
            foreach ($neurons as $neuron) {
                $this->connection($neuron);
            }
        } else {
            $this->axonsBak[] = $neurons;
            $neurons->dendritesBak[] = $this;
            $this->axons[] = $neurons;
            $neurons->dendrites[] = $this;
        }
    }

    public function rollback(){
        $this->axons = $this->axonsBak;
        $this->dendrites = $this->dendritesBak;
    }

    abstract function active();
    abstract function derivative($input);
}

class NeuronA extends Neurons
{
    public function active()
    {
        $this->output = tanh($this->input);
        return $this->output;
    }
    public function derivative($input)
    {
        // tanh的导函数
        $input = $input * $input;
        $input = 1 - $input;
        return $input;
    }
}

class NeuronB extends Neurons
{
    public $k;
    public function __construct($name, $k=1)
    {
        parent::__construct($name);
        $this->k = $k;
    }

    public function active()
    {
        $this->output = $this->k * $this->input;
        return $this->output;
    }
    public function derivative($input)
    {
        // purelin的导函数
        return $this->k;
    }
}

class NeuronC extends Neurons
{
    public function active()
    {
        if ($this->input <= 0) {
            $this->output = 0;
        } else {
            $this->output = $this->input;
        }
        return $this->output;
    }
    public function derivative($input)
    {
        // relu的导函数
        if ($input <= 0) {
            $input = 0;
        } else {
            $input = 1;
        }
        return $input;
    }

}

class Network
{
    // 记录单次训练状态
    public $reflex = 0;
    public $predict = 0;
    public $network = [];
    public $result = [];
    public $weights = [];
    // 记录loss值，以便生成loss图像
    public $lossMap;
    // 设置权重的精度
    public $precision;
    public $neurons = [];
    public $layers = [];
    public function __construct($precision = 16)
    {
        $this->precision = $precision;
    }
    public function excite($neuron, $input)
    {
        /*
        if (!empty($this->network)) {
            foreach ($this->network as $network) {
                if ($network === $neuron) {
                    unset($network);
                }
            }
        }
        */
        //$neuron->sumWeight($input, $this->findWeight(null, $neuron));
        $neuron->input = $input;
        $output = $neuron->active();
        //$neuron->output = $output;
        // 如果神经元参与突触结构，则传递兴奋
        if (!empty($neuron->axons)) {
            foreach ($neuron->axons as $axon) {
                $axon->sumWeight($output, $this->findWeight($neuron, $axon));
                // 神经递质开始积累
                foreach ($axon->dendrites as $key => $dendrite) {
                    if ($dendrite === $neuron) {
                        unset($axon->dendrites[$key]);
                        $axon->dendrites = array_values($axon->dendrites);
                    }
                }
                $this->excite($axon, $axon->input);
                $neuron->rollback();
            }
        }
        // 树突已经受到所有刺激，释放神经递质
        else
        {
            // 本神经网络完成反射，进行标记
            $this->reflex = 1;
            $this->predict = round($neuron->output, $this->precision);
            //echo '预测结果: '.$this->predict,PHP_EOL;
            $neuron->rollback();
        }
    }

    public function refreshWeight($from, $to, $weight, $bias = 0)
    {
        if(is_array($to)){
            foreach ($to as $toOne){
                //$weightRand->init(count($netContainer[$i]), count($netContainer[$i + 1]), $network->precision)
                $weightOne = $weight['api']->init($weight['parameter1'], $weight['parameter2'], $weight['precision']);
                $this->refreshWeight($from, $toOne, $weightOne, $bias);
            }
        }else{
            if(!empty($this->findWeight($from, $to))){
                //unset($this->weights[array_search($this->weights, Array($this->findWeight($from, $to), true))]);
                unset($this->weights[array_search(Array($this->findWeight($from, $to)), $this->weights, true)]);
                $this->weights = array_values($this->weights);
            }
            $this->weights[] = array('from' => $from, 'to' => $to, 'weight' => $weight, 'bias' => $bias);
        }
    }

    public function findWeight($from, $to)
    {
        foreach ($this->weights as $weights) {
            if ($weights['from']->name === $from->name and $weights['to']->name === $to->name) {
                return $weights;
            }
        }
    }
    public function train($neuron, $input)
    {
        //$neuron->sumLoss($input, $this->findWeight(null, $neuron));
        //$output = $neuron->outputLoss;
        $output = $input;
        // 如果神经元参与突触结构，则传递loss
        if (!empty($neuron->dendrites)) {
            foreach ($neuron->dendrites as $dendrite) {
                $dendrite->sumLoss($output, $this->findWeight($dendrite, $neuron));
                // 神经递质开始积累
                foreach ($dendrite->axons as $key => $axon) {
                    if ($axon === $neuron) {
                        unset($dendrite->axons[$key]);
                        $dendrite->axons = array_values($dendrite->axons);
                    }
                }
                    $this->train($dendrite, $output);
                    $neuron->rollback();
                }
            }
        // 轴突已经取得所有损失，回收神经递质
        else
        {
            // 本神经网络完成回收，进行标记
            $this->reflexLoss = 1;
            $neuron->rollback();
        }
    }
/*
    public function train($result, $predict, $size)
    {
        // 反向传播
        $tmp = $this->loss2($result, $predict);
        // 记录总体loss值，以便后续生成loss图像
        $this->lossmap .= $tmp . PHP_EOL;
        // 初始化可能的上次训练结果
        unset($this->result);
        $res = array();
        for ($i = $size; $i > 0; $i--) {
            $res[$size] = $tmp;
            foreach ($this->weights as &$weight) {
                if (!empty($this->result)) {
                    foreach ($this->result as $result) {
                        if ($result['from']->name === $i) {
                            $res[$result['from']->name] += $result['loss'];
                        }
                    }
                }
                if ($weight['to']->name === $i) {
                    $weight['loss'] = $res[$weight['to']->name] * $weight['weight'];
                    $this->result[] = $weight;
                }
            }
        }
        $this->weights = $this->result;
    }
*/

    public function loss2($result, $predict)
    {
        // L2损失函数
        $loss = floatval($result - $predict);
        $loss = ($loss * $loss) / 2;
        return $loss;
    }

    public function loss1($result, $predict)
    {
        // L1损失函数
        $loss = floatval($result - $predict);
        $loss = abs($loss);
        return $loss;
    }
    public function feedback($neurons, $rate)
    {
        foreach ($neurons as $neuron){
            if(!empty($neuron->axons)){
                foreach ($neuron->axons as $axon){
                    //$fixWeight = $neuron->derivative($neuron->output) * $neuron->input *  $neuron->inputLoss[$axon->name] * $rate;
                    //$fixBias = $neuron->derivative($neuron->input) *  $neuron->inputLoss[$axon->name] * $rate;
                    $fixWeight = $neuron->derivative($axon->input) * $neuron->output *  $neuron->inputLoss[$axon->name] * $rate;
                    $fixBias = $neuron->derivative($axon->input) *  $neuron->inputLoss[$axon->name] * $rate;
                    $result = $this->findWeight($neuron, $axon);
//                    $weight = $result['weight'] - $fixWeight;
//                    $bias = $result['bias'] - $fixBias;
                    $weight = $result['weight'] + $fixWeight;
                    $bias = $result['bias'] + $fixBias;
                    $this->refreshWeight($neuron, $axon, $weight, $bias);
                }
            }
        }
    }
    /*
    public function feedback($input, $rate)
    {
        $num = 0;
        $results = array();
        foreach ($this->result as $result) {
            // 初始化
            if (is_null($result['from'])) {
                $result['from'] = new NeuronA(0);
                $result['sign'] = 2;
                if (is_array($input)) {
                    foreach ($input as $value) {
                        $result['from']->sumWeight($value, 1);
                    }
                } else {
                    $result['from']->sumWeight($input, 1);
                }

                $result['from']->output = $result['from']->input;
            }
            // 梯度下降
            $fixWeight = ($result['to']->derivative($result['from']->output) * $result['from']->output * $result['loss'] * $rate);
            $fixBias = ($result['to']->derivative($result['from']->output) * $result['loss'] * $rate);
            echo $result['loss'] . ' | ' . $fixWeight . ' | ' . $fixBias, PHP_EOL;
            if (!is_nan($fixWeight)) {
                $result['weight'] -= $fixWeight;
                $result['weight'] = round($result['weight'], $this->precision);
            }
            if (!is_nan($fixBias)) {
                $result['bias'] -= $fixBias;
                $result['bias'] = round($result['bias'], $this->precision);
            }
            if ($result['sign'] === 2) {
                $result['from'] = null;
                unset($result['sign']);
            }
            $results[] = $result;
        }
        $this->result = $results;
    }*/
}

