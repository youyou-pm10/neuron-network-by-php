<?php
abstract class Neurons{
    public $input = 0;
    public $output = 0;
    public $dendrites = [];
    public $axons = [];
    public $name = null;
    public function __construct($name){
        $this->name = $name;
    }
    public function sumWeight($inputs, $weights, $bias=0){
        $this->input += floatval($weights) * floatval($inputs) + floatval($bias);
    }
    public function connection($neurons){
        if(is_array($neurons)){
            foreach ($neurons as $neuron) {
                $this->connection($neuron);
            }
        }else{
                $this->dendrites[] = $neurons;
                $neurons->axons[] = $this;
        }
    }
    abstract function active();
}
class NeuronA extends Neurons{
    public function active(){
        $this->output = tanh($this->input);
        return $this->output;
    }
}
class NeuronB extends Neurons{
    public function active()
    {
        $this->output = $this->input;
        return $this->output;
    }
}
class NeuronC extends Neurons{
    public function active()
    {
        if($this->input <= 0){
            $this->output = 0;
        }else{
            $this->output = $this->input;
        }
        return $this->output;
    }
}

class Network{
    public $reflex = 0;
    public $predict = 0;
    public $network = Array();
    public $result = Array();
    public $weights = Array();
    public function excite($neuron, $input){
        if(!empty($this->network)){
            foreach ($this->network as $network){
                if($network === $neuron){
                    unset($network);
                }
            }
        }
        $neuron->sumWeight($input, $this->findWeight(null, $neuron)['weight'], $this->findWeight(null, $neuron)['bias']);
        $this->network[] = $neuron;
        $output = $neuron->active();
        // 如果神经元参与突触结构，则传递兴奋
        if(!empty($neuron->dendrites)){
            foreach ($neuron->dendrites as $dendrite){
                $dendrite->sumWeight($output, $this->findWeight($neuron, $dendrite)['weight'], $this->findWeight(null, $neuron)['bias']);
                // 神经递质开始积累
                foreach ($dendrite->axons as $key => $axon){
                    if($axon === $neuron){
                        unset($dendrite->axons[$key]);
                        $dendrite->axons = array_values($dendrite->axons);
                    }
                }
                // 轴突已经受到所有刺激，释放神经递质
                if(empty($dendrite->axons)){
                    if(empty($dendrite->dendrites)){
                        // 本神经网络完成反射，进行标记
                        $this->reflex = 1;
                        $this->predict = round($dendrite->output, 6);
                        //echo '预测结果: '.$this->predict,PHP_EOL;
                    }
                    $this->excite($dendrite, $output);
                }
            }
        }
    }
    public function refreshWeight($from, $to, $weight, $bias=0){
        $this->weights[] = array('from'=>$from, 'to'=>$to, 'weight'=>$weight, 'bias'=>$bias);
    }
    public function findWeight($from, $to){
        foreach ($this->weights as $weights){
            if($weights['from'] === $from and $weights['to'] === $to){
                return $weights;
            }
        }
    }
    public function train($result, $predict)
    {
        $tmp = $this->loss($result, $predict, 1);
        $this->text .= $tmp.PHP_EOL;
        unset($this->result);
        for ($i = 7; $i > 0; $i--) {
            $res7 = $tmp;
            foreach ($this->weights as $weight) {
                if ($weight['from']->name === $i) {
                    ${'res'.$i} += $weight['loss'];
                }
                if ($weight['to']->name === $i) {
                    $weight['loss'] =  ${'res'.$i} * $weight['weight'];
                    $this->result[] = $weight;
                }
            }
        }
    }
    public function loss($result, $predict, $weight){
        // 损失函数
        $loss = floatval($result - $predict);
        $loss = ($loss * $loss) / 2;
        $loss = $weight * $loss;
        return $loss;
    }
    public function feedback($input, $rate)
    {
        $num = 0;
        $results = Array();
        foreach ($this->result as $result) {
            // 初始化
            if (is_null($result['from'])) {
                $result['from'] = new NeuronA(0);
                $result['sign'] = 2;
                if(is_array($input)){
                    foreach ($input as $value) {
                        $result['from']->sumWeight($value, 1);
                    }
                }else{
                    $result['from']->sumWeight($input, 1);
                }

                $result['from']->output = $result['from']->input;
            }
            // 反向传播
            //$fixWeight = ($this->thDerivative($result['to']->input) * $result['from']->output * $result['loss'] * $rate);
            //$fixBias = ($this->thDerivative($result['to']->input) * $result['loss'] * $rate);
            $fixWeight = ($this->thDerivative($result['from']->output) * $result['from']->output * $result['loss'] * $rate);
            $fixBias = ($this->thDerivative($result['from']->output) * $result['loss'] * $rate);
            //$fix = ($this->thDerivative($result['to']->input) * $rate);
            //echo $result['loss'] . ' | ' . $fixWeight. ' | ' .$fixBias, PHP_EOL;
            if(!is_nan($fixWeight)){
                $result['weight'] -= $fixWeight;
                $result['weight'] = round($result['weight'], 6);
            }
            if(!is_nan($fixBias)){
                $result['bias'] -= $fixBias;
                $result['bias'] = round($result['bias'], 6);
            }
            if ($result['sign'] === 2) {
                $result['from'] = null;
                unset($result['sign']);
            }
            $results[] = $result;
        }
        $this->result = $results;
    }
    public function thDerivative($input){
        $input = $input * $input;
        $input = 1 - $input;
        return $input;
    }
}

