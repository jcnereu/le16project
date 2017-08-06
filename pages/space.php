<?php
    $idEspaco = filter_input(INPUT_GET,'ids',FILTER_DEFAULT);
    require_once '../config/models/space.class.php';// Saindo da home
    $espaco = new space();
    $nomeEspaco = $espaco->pegarNomeEspaco($idEspaco);
    
    //PAROU AQUI: Implementar as funções de registro de usuário no espaço ao entrar pela busca em=sch
    // Implementar  ocódigo para somente o último usuário ao sair fechar limpar o espaço
    
?>
<div class="espaco_container">
    <div class="espaco_cabecalio">
        <span onclick="sairEspaco(<?php echo $idEspaco; ?>);" class="espaco_btn_sair">sair</span>
        <?php echo $nomeEspaco; ?>
        <!-- Nome do espaço -->
    </div>
    <div class="espaco_mensagens_container">
        <?php
            // MOSTRANDO A MENSAGEM
            if(isset($mensagemFormatada)){
                echo $mensagemFormatada;
            }
            require_once '../config/loadConn.inc.php';// Saindo da home
            $teste = new read();
            $teste->fazerBusca('SELECT * FROM spaces WHERE id = :bv', "bv={$idEspaco}");
            echo $teste->retornaResultado()[0];
        ?>
    </div>
    <div class="espaco_area_texto">
        <form method="post">
            <textarea rows="6" name="textoMensagem"></textarea>
            <div class="espaco_area_texto_comandos">
                <input type="submit" name="enviarMensagem" value="Enviar">
                <!-- 
                    O PROCESSAMENTO DO BOTÃO ENVIAR É FEITO NO home.php (NO TOPO DO SCRIPT)
                -->
            </div>
        </form>
    </div>
</div>
<script>
    // Se o usuário clicar em sair no canto superio direito do espaço ativo
    function sairEspaco(idEspaco) {
        //window.location.assign("home.php?idteste=" + idEspaco);
        // AJAX para fechar o espaço
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                /*Recebe a string true manda ver*/
                if (this.responseText==='true') {
                    window.location.assign("home.php?ss=ns");
                }
            }
        };
        //xmlhttp.open("GET", "../config/ajax/fecharEspaco.php?q=" + idEspaco, true);
        xmlhttp.open("GET", '../config/ajax/fecharEspaco.php?q=' + idEspaco, true);
        xmlhttp.send();
    }
</script>