<?php

/**
 * Description of teste
 *
 * @author JoaoCarlosNereu
 */
class teste {
    
    private $num1;
    private $num2;
    
    public function somar($num1,$num2) {
        $this->num1 = $num1;
        $this->num2 = $num2;
        return $this->num1 + $this->num2;
    }
}