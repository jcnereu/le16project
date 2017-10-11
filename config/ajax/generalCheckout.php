<?php

// Pegando os dados enviados por formulário (via post) no JS le16.prototype.signOut na userBar
$dadosFormulario = filter_input_array(INPUT_POST,FILTER_DEFAULT);
$idUsuario = $dadosFormulario['idUsuario'];
$listaEspacos = $dadosFormulario['listaEspacos'];

// Se a lista não está vazia (O usuário pode clicar em sair sem estar registrado em nenhum espaço)
if(!empty($listaEspacos)){
    // Passando a string com a lista para um array
    $arrayLista = explode('&', substr($listaEspacos, 1)); // O substr é para remover o primeiro '&'
    require_once '../models/space.class.php';// Saindo da pasta ajax
    $espaco = new space();
    $resposta = ''; 
    $erro = false;// Flag para sinalizar algum erro

    // Para cada espaço listado
    foreach ($arrayLista as $infoEspaco) {
        // Recuperando o ID do espaço
        // A lista vem no formato "id1=nome1&id2=nome2&..." (futuramente podem até vir outras informações)
        // Os nomes não são necessários aqui, mas são utilizados no "convite" que um usuário pode mandar pra outro (na lista de usuários de um espaço)
        $subArray = explode('=', $infoEspaco);
        $idEspaco = $subArray[0];
        // Fazendo o registro de saída
        if($espaco->registrarSaidaUsuario($idUsuario,$idEspaco)){
            if($espaco->pegarEmptySpace()){ // Se era o último usuário no espaço
                $resposta = $resposta . '&' .$idEspaco. '=empty';
            } else { // Se não era o último usuário
                $resposta = $resposta . '&' .$idEspaco. '=true';
            }
        } else { // Se o registro de saída não foi bem sucedido
            $erro = true;
        }
    }
    // Se houve algum erro 
    if($erro){
        $resposta = 'false';
    }
    
} else { // Se a lista está vazia
    $resposta = 'nolist';
}

// Resposta para o clientSide (retorna uma lista em string assim como a lista recebida)
echo $resposta;

