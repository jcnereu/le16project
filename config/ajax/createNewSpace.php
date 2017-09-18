<?php

// Pegando os dados enviados por formulário (via post) no JS criarNovoEspaco() da userBar.php
$dadosFormulario = filter_input_array(INPUT_POST,FILTER_DEFAULT);
$idUsuario = $dadosFormulario['idUsuario'];
$nomeNovoEspaco = $dadosFormulario['nomeNovoEspaco'];

// Carregando a classe com os métodos do espaço
require_once '../models/space.class.php'; //  Sindo da pasta ajax
// Instanciando um objeto para utilizar os métodos
$espaco = new space();
// Se o novo espaço pôde ser alocado (vaga reciclada ou criada)
if($espaco->alocarEspaco($nomeNovoEspaco, $idUsuario)){
    // Retorna o novo ID para atualizar a URL e abrir o novo espaço
    $resposta = $espaco->pegarIDespaco();
} else {
    // Se o ocorreu algum erro no caminho ou o usuário está no limite de 10 espaços (FALTA PEGAR A MENSAGEM RETORNADA NESSE CASO)
    $resposta = 'false';
}

// Resposta para o ClientSide 
echo $resposta;