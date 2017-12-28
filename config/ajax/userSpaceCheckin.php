<?php

// Pegando os dados enviados por formulário (via post) no JS registrarEntradaUsuario(idEspaco) da userBar.php
$dadosFormulario = filter_input_array(INPUT_POST,FILTER_DEFAULT);
$idUsuario = $dadosFormulario['idUsuario'];
$idEspaco = $dadosFormulario['idEspaco'];

//Registrando a entrada do usuário
require_once '../models/space.class.php'; //  Sindo da pasta ajax

$spaceIn = new space();
if($spaceIn->registrarEntradaUsuario($idUsuario,$idEspaco)=='ok'){ // DDDDDDDDDDDDDDDDD F5
    $resposta = 'true';
} else if ($spaceIn->registrarEntradaUsuario($idUsuario,$idEspaco)=='limite') { // DDDDDDDDDDDDDDDD NOVO
    $resposta = 'limite';
} else { // DDDDDDDDDDDDDDDDDD F5
    $resposta = 'erro';
}
// Resposta para o ClientSide 
echo $resposta;