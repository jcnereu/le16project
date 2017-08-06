<?php

/**
 * Verificar a segurança de se usar o GET para isso
 */

$rawString = $_GET['q'];
$string = (String) strip_tags(trim($rawString));
require_once '../loadConn.inc.php';// Sainda da pasta ajax
$resultado = '';
if(strlen($string)>1){ // Mínimo de letras para fazer a busca (Para não estressar o banco de dados)
    
    //Dividindo a string em um array de substrings separadas por espaço em branco.
    $arrayString = explode(' ', $string); 

    //Em cada substring
    $query = 'SELECT * FROM spaces WHERE';
    $bindValues = '';
    $cont = 0;
    foreach ($arrayString as $substring) {
        $cont = $cont + 1;
        $bvnum = (String) $cont;
        if($cont==1){
            $query = $query . " name LIKE :bv{$bvnum}";
            $bindValues = $bindValues . "bv{$bvnum}={$substring}";
        } else {
            $query = $query . " AND name LIKE :bv{$bvnum}";
            $bindValues = $bindValues . "&bv{$bvnum}={$substring}";
        }
    }
    /*
     * Acrescentar um ORDER BY pela quantidade de usuários no espaço
     */
    $query = $query . ' LIMIT 10'; // No máximo 10 resultados
    $busca = new readLike;
    /// Verificar a segurança de declarar o LIMIT sem bind value
    ///$tres = 2;
    ///$busca->fazerBusca('SELECT * FROM spaces WHERE name LIKE :bv1 LIMIT :bv2',"bv1={$string}&bv2={$tres}");
    ///$busca->fazerBusca('SELECT * FROM spaces WHERE name LIKE :bv1 LIMIT 2',"bv1={$string}");
    
    $busca->fazerBusca($query,$bindValues);
    
    if($busca->contaResultados()>0):
        foreach ($busca->retornaResultado() as $linha => $espaco):

                // Criando o HTML/CSS para exibir cada resultado da busca
                $resultado = $resultado . 
                '<a href="home.php?ss=sp&id=' .$espaco['id']. '&em=sch">' .
                    '<div class="resultado_busca_item_container">' .
                        '<p class="resultado_busca_item_bloco_esquerdo">' . $espaco['name'] . '</p>' .
                        '<p class="resultado_busca_item_bloco_direito">' . $espaco['nusers'] . '</p>' .
                    '</div>' .
                '</a>';
        endforeach;
        //$resultado = 'Algum resultado.';
    else:
        $resultado = 'Nenhum resultado encontrado.';
    endif;
    //echo 'Você digitou: ' . $string;
//else:
    //$resultado = 'Letras insuficientes para a busca.';
}

//RESPOSTA PARA O CLIENT-SIDE (Para ser manipulado pelo JavaScript)
echo $resultado;

