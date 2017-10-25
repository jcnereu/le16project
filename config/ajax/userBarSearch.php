<?php

// O envio pode ser feito com GET, pois o que o usuário pesquisa não é tradado como uma informação confidencial

$rawString = filter_input(INPUT_GET,'q',FILTER_DEFAULT);//$_GET['q'];
$string = (String) strip_tags(trim($rawString));
require_once '../loadConn.inc.php';// Sainda da pasta ajax
$resultado = '';
if(strlen($string)>1){ // Mínimo de letras para fazer a busca (Para não estressar o banco de dados)
    
    //Dividindo a string em um array de substrings separadas por espaço em branco.
    $arrayString = explode(' ', $string); 

    // Cabeçálio da query 
    $query = 'SELECT * FROM spaces WHERE'; // visible="yes" AND ...
    $bindValues = '';
    $cont = 0;
    //Em cada substring (acrescenta um item na query)
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
    // Acrescentando o filtro de espaços visíveis
    $query = $query . ' AND visible = :visible';
    $bindValues = $bindValues . "&visible=yes";
    // Acrescentando um limite no resultado da busca (Em produção deve ser maior?)
    $query = $query . ' LIMIT :limit'; // No máximo 10 resultados
    $bindValues = $bindValues . "&limit=10";
    /*
     * ACRESCENTAR O ORDER BY 'nusers'
     */
    // Executando a query final
    $busca = new readLike;    
    $busca->fazerBusca($query,$bindValues);
    // Se a busca retornar resultados
    if($busca->contaResultados()>0):
        foreach ($busca->retornaResultado() as $linha => $espaco):
                // Criando o HTML/CSS para exibir cada resultado da busca
                $resultado = $resultado . 
                    '<div class="resultado_busca_item_container" '. 
                    "onclick=\"registrarEntradaUsuario({$espaco['id']})\"" .
                    ' >' .
                        '<p class="resultado_busca_item_bloco_esquerdo">' . $espaco['name'] . '</p>' .
                        '<p class="resultado_busca_item_bloco_direito">' . $espaco['nusers'] . '</p>' .
                    '</div>';
        endforeach;
    else:
        $resultado = 'noresult'; // Flag para exibir o botão para criar um novo espaço
    endif;
}
//RESPOSTA PARA O CLIENT-SIDE (Para ser manipulado pelo JavaScript)
echo $resultado;