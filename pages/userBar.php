
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
                <a href="">Sair</a>
            </ul>
        </div>
        <div class="barra_usuario_ola_container">
            <p>Olá <?php echo $dadosUsuario['nome'];?></p>
        </div>
    </div>
</div>
<!--
    SCRIPTS
-->
<script>
    /* Mostra/oculta o conteúdo do menu ao clicar no botão do menu */
    function mostrarMenu() {
        document.getElementById("barra_usuario_menu_conteudo").classList.toggle("barra_usuario_menu_mostrar_conteudo");
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
