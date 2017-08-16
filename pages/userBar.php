
<!--
 - Barra do Usuário presente em todas as páginas após o Login;
 - Pega os dados de sessão do script loadSession.php carregado no início de todas as páginas.
-->

<div class="coluna_superior">
    <div class="barra_usuario_container">
        <div class="barra_usuario_menu">
            <button class="barra_usuario_menu_botao" onclick="mostrarMenu();"></button>
            <ul class="barra_usuario_menu_conteudo" id="barra_usuario_menu_conteudo">
                <a href="">Perfil</a>
                <a href="">Conta</a>
                <a onclick="fazerLogout();">Sair</a>
            </ul>
        </div>
        <div class="barra_usuario_ola_container">
            <p>Olá <?php echo $dadosUsuario['nome'];?></p>
            <!-- O campo abaixo é invisível. Criado apenas para serivir quelaquer JS que precise do ID do usuário-->
            <input type="text" value="<?php echo $dadosUsuario['id']; ?>" id="id_invisivel_usuario" style="display: none;">
        </div>
        <div class="barra_usuario_busca_container">
            <form method="post">
                <div class="bu_caixa_texto"><input type="text" name="nomeEspaco" onkeyup="buscarSugestao(this.value)"></div>
                <div class="bu_botao_novo"><input type="submit" name="novoEspaco" value="Novo"></div>
                <!-- 
                O processamento PHP do formulário é feito na home, pois é preciso usar o header
                e a barra do usuário é carregada na home, depois de saídas HTML 
                -->
            </form>
        </div>
        <div class="resultado_busca" id="div_resultado_busca"></div>
    </div>
</div>
<!--
    SCRIPTS
-->
<script>
    
    // 
    function buscarSugestao(str) {
        /*Condição para abrir e fechar a div de exibição dos resultados*/
        if(str.length > 1){
            document.getElementById("div_resultado_busca").style.display = 'block';
        } else {
            document.getElementById("div_resultado_busca").style.display = 'none';
        }
        /*AJAX PARA FAZER A BUSCA ENQUANTO O USUÁRIO DIGITA*/
        if (str.length > 0) {
            /*document.getElementById("ResultadoBusca").innerHTML = "Digitando...";*/
            var xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    /*Recebe a string de resultados do servidor (já com html/css) e joga na div 'ResultadoBusca'*/
                    document.getElementById("div_resultado_busca").innerHTML = this.responseText;
                }
            };
            xmlhttp.open("GET", "../config/ajax/userBarSearch.php?q=" + str, true);
            xmlhttp.send();
        }
    }
    
    function registrarEntradaUsuario(idEspaco){
        var idUsuario = document.getElementById("id_invisivel_usuario").value;
        // AJAX para registrar a entrada do usuário
        var xmlregistro = new XMLHttpRequest();
            xmlregistro.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    if(this.responseText==='true'){
                        window.location.assign('home.php?ss=sp&ids='+idEspaco);
                    }// Se o registro não for bem sucedido: Não fazer nada por enquanto
                }
            };
            xmlregistro.open("GET", "../config/ajax/userSpaceCheckin.php?ide="+idEspaco+"&idu="+idUsuario, true);
            xmlregistro.send();
    }
    /* Mostra/oculta o conteúdo do menu ao clicar no botão do menu */
    function mostrarMenu() {
        document.getElementById("barra_usuario_menu_conteudo").classList.toggle("barra_usuario_menu_mostrar_conteudo");
    }
    /**/
    function fazerLogout() {
        /* INCLUIR O LOGOUT DE USUÁRIOS LOGADOS PELO FB
        if (FB.getLoginStatus().status==='connected') {
            window.location.assign("home.php?fb=true");
        
            // Função chamada quando um usuário logado pelo FB clicar em sair
            FB.logout(function(response) {
                window.location.assign("../index.php");
            });
        
        }
        */
        window.location.assign("home.php?logout=true");
    }
    /* Para ocultar o conteúdo do meno ao clicar fora */
    window.onclick = function(event) {
        if (!event.target.matches('.barra_usuario_menu_botao')) {
            var divConteudoMenu = document.getElementById("barra_usuario_menu_conteudo");
            if (divConteudoMenu.classList.contains('barra_usuario_menu_mostrar_conteudo')) {
                divConteudoMenu.classList.remove('barra_usuario_menu_mostrar_conteudo');
            }
        }
    }
</script>
