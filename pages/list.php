<!-- Script chamado na home, dentro da div "coluna_central_c2" -->
<!-- ... -->
<!-- Exemplo de campo invisível -->
<!-- <input type="text" value="</?php echo $idEspacoUrl; ?>" id="id_invisivel_espaco" style="display: none;"> -->

<!-- ... -->
<!--<div class="lista_container">--><!-- LLLLLLLLLLLLLLLLLLLL OUT -->
    <div class="lista_tudo_cabecalio"><!-- LLLLLLLLLLLLLLLLLLLL F5 -->
        <div class="texto">
            Tópicos abertos nesse momento:
        </div>
    </div>
    <div class="lista_tudo_corpo"><!-- LLLLLLLLLLLLLLLLLLLL F5 -->
        <?php
            include_once '../config/loadConn.inc.php'; // Saindo da home
            $buscaLista = new read();
            $buscaLista->fazerBusca('SELECT * FROM spaces WHERE visible = :bv1 AND status = :bv2',"bv1=yes&bv2=on"); // WHERE visible="yes" AND status="on"
            if($buscaLista->contaResultados()>0){
                $resultado = '';
                foreach ($buscaLista->retornaResultado() as $linha => $espaco) {
                    // Convertendo a timeStamp de criação do espaço em um horário legível
                    $tempoDeCriacao = $espaco['creation_date'];
                    //DDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDD
                    $tempoAgora = time();
                    // Segundos desde a meia noite de ontem
                    $horas = (int) date('H', $tempoAgora);
                    $minutos = (int) date('i', $tempoAgora);
                    $segundos = (int) date('s', $tempoAgora);
                    $secDesdeMeiaNoite = $horas*3600 + $minutos*60 + $segundos;
                    // Segundos desde a meia noite de anteontem
                    $secDesdeMeiaNoiteOntem = $secDesdeMeiaNoite + 86400; // segundos em um dia = 86400
                    // Segundos desde a criação
                    $diferenca = $tempoAgora - $tempoDeCriacao; // 2640, 4657, 92567
                    if ($diferenca<60) {
                        $dataCriacao = 'agora mesmo';
                    } elseif ($diferenca<3600) {
                        $minutosStr = (String) floor($diferenca/60);
                        $dataCriacao = 'à ' .$minutosStr. 'min';
                    } elseif ($diferenca<$secDesdeMeiaNoite) {
                        $dataCriacao = 'hoje às ' .date('H:i', $tempoDeCriacao);
                    } elseif ($diferenca<$secDesdeMeiaNoiteOntem) {
                        $dataCriacao = 'ontém às ' .date('H:i', $tempoDeCriacao);
                    } else {
                        $dataCriacao = 'em ' .date('d/m/Y', $tempoDeCriacao). ' às ' .date('H:i', $tempoDeCriacao);
                    }
                    //DDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDD
                    
                    // Criando o HTML/CSS do item
                    $resultado = $resultado . 
                    '<div class="item_resultado_busca" '. 
                    "onclick=\"registrarEntradaUsuario({$espaco['id']});\">" .
                        '<div class="item_bloco_esquerdo">' .
                            '<p class="nome_espaco">' . $espaco['name'] . '</p>' .
                            '<p class="info_criador">' .$espaco['creator_name']. ', ' .$dataCriacao. '</p>' . // INCLUIR A DATA DE CRIAÇÃO (O tal do horário legível)
                        '</div>' .
                        '<p class="item_bloco_direito">' . $espaco['nusers'] . '</p>' .
                    '</div>';
                }
            } else {
                $resultado = '<div class="nehum_resultado">Ninguém em lugar nenhum. Vamos lá, crie um novo tópico!</div>';
            }
            echo $resultado;
        ?>
    </div>
<!--</div>-->
