<!-- O script PHP para validar e pegar os dados do espaço estão no começo da home.php, pois precisa usar o header() -->
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
        //Pegando o id do usuário em um campo invisível na barra do usuário;
        var idUsuario = document.getElementById("id_invisivel_usuario").value;
        //Pegando o id do próximo espaço em um campo invisível na home;
        var idProximoEspaco = document.getElementById("id_invisivel_proximo_espaco").value;
        // AJAX para fechar o espaço
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function() {
            if (this.readyState === 4 && this.status === 200) {
                /*Recebe a string true manda ver*/
                if (this.responseText==='true') {
                    if (idProximoEspaco!==0){
                        window.location.assign("home.php?ss=sp&ids="+idProximoEspaco);
                    } else {
                        window.location.assign("home.php?ss=ns");
                    }
                }
            }
        };
        //xmlhttp.open("GET", "../config/ajax/fecharEspaco.php?q=" + idEspaco, true);
        xmlhttp.open("GET", '../config/ajax/userSpaceCheckout.php?ide='+idEspaco+'&idu='+idUsuario, true);
        xmlhttp.send();
    }
</script>