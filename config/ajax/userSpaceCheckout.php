<?php

// Pegando os dados enviados por formulário (via post) no JS sairEspaco(idEspaco) da space.php
$dadosFormulario = filter_input_array(INPUT_POST,FILTER_DEFAULT);
$idUsuario = $dadosFormulario['idUsuario'];
$idEspaco = $dadosFormulario['idEspaco'];

require_once '../models/space.class.php';// Saindo da pasta ajax
$espaco = new space();

if($espaco->registrarSaidaUsuario($idUsuario,$idEspaco)){
    $resposta = 'true';
} else {
    $resposta = 'false';
}
// Resposta para o clientSide
echo $resposta;

