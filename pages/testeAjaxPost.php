<?php

$DadosFormulario = filter_input_array(INPUT_POST,FILTER_DEFAULT);
$string = $DadosFormulario['nomeNovoEspaco'];

echo 'Você digitou: ' . $string;

