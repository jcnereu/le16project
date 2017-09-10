<?php

// Pegando os dados enviados por formulário (via post) no JS sairEspaco(idEspaco) da space.php
$dadosFormulario = filter_input_array(INPUT_POST,FILTER_DEFAULT);
$idUsuario = $dadosFormulario['idUsuario'];
$idEspaco = $dadosFormulario['idEspaco'];

require_once '../models/space.class.php';// Saindo da pasta ajax
$espaco = new space();

if($espaco->registrarSaidaUsuario($idUsuario,$idEspaco)){
    if($espaco->pegarEmptySpace()){ // Se era o último usuário no espaço
        $resposta = 'empty';
    } else { // Se não era o último usuário
        $resposta = 'true';
    }
} else { // Se o registro de saída não foi bem sucedido
    $resposta = 'false';
}
// Resposta para o clientSide
echo $resposta;

