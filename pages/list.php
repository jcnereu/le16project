<!-- Script chamado na home, dentro da div "coluna_central_c2" -->
<!-- ... -->
<!-- Exemplo de campo invisível -->
<!-- <input type="text" value="</?php echo $idEspacoUrl; ?>" id="id_invisivel_espaco" style="display: none;"> -->

<!-- ... -->
<div class="lista_container">
    <div class="cabecalio">
        Todos os espaços abertos nesse momento:
    </div>
    <div class="corpo">
        <?php
            include_once '../config/loadConn.inc.php'; // Saindo da home
            $buscaLista = new read();
            $buscaLista->fazerBusca('SELECT * FROM spaces WHERE visible = :bv1 AND status = :bv2',"bv1=yes&bv2=on"); // WHERE visible="yes" AND status="on"
            if($buscaLista->contaResultados()>0){
                $resultado = '';
                foreach ($buscaLista->retornaResultado() as $linha => $espaco) {
                    // Convertendo a timeStamp de criação do espaço em um horário legível
                    //...
                    // Criando o HTML/CSS do item
                    $resultado = $resultado . 
                    '<div class="item_resultado_busca" '. 
                    "onclick=\"registrarEntradaUsuario({$espaco['id']});\">" .
                        '<div class="item_bloco_esquerdo">' .
                            '<p class="nome_espaco">' . $espaco['name'] . '</p>' .
                            '<p class="info_criador">' .$espaco['creator_name']. '</p>' . // INCLUIR A DATA DE CRIAÇÃO (O tal do horário legível)
                        '</div>' .
                        '<p class="item_bloco_direito">' . $espaco['nusers'] . '</p>' .
                    '</div>';
                }
            } else {
                $resultado = 'Ninguém em lugar nenhum. tasc tasc Crie um novo espaço!';
            }
            echo $resultado;
        ?>
    </div>
</div>
