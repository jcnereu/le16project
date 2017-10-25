<?php

// Pegando os dados enviados por formulário (via post) no JS sairEspaco(idEspaco) da space.php
$dadosFormulario = filter_input_array(INPUT_POST,FILTER_DEFAULT);
$idEspaco = $dadosFormulario['idEspaco'];
$visibilidade = $dadosFormulario['visibilidade'];

require_once '../models/space.class.php';// Saindo da pasta ajax
$espaco = new space();

if($espaco->atualizarVisibilidade($idEspaco, $visibilidade)){
    $resposta = 'true';
} else { // Se o registro de saída não foi bem sucedido
    $resposta = 'false';
}
// Resposta para o clientSide
echo $resposta;

