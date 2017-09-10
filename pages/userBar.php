
<!--
 - Barra do Usuário presente em todas as páginas após o Login;
 - Pega os dados de sessão do script loadSession.php carregado no início de todas as páginas.
-->

<div class="coluna_superior">
    <div class="barra_usuario_container">
        <div class="barra_usuario_menu">
            <button class="barra_usuario_menu_botao" onclick="mostrarMenu();"></button>
            <ul class="barra_usuario_menu_conteudo" id="barra_usuario_menu_conteudo">
                <a href="">Status</a>
                <a id="sign-out">Sair</a>
            </ul>
        </div>
        <div class="barra_usuario_ola_container">
            <p id="ola_usuario"></p>
            <!-- O campo abaixo é invisível. Criado apenas para serivir quelaquer JS que precise do ID do usuário-->
            <input type="text" value="<?php echo $dadosUsuario['id']; ?>" id="id_invisivel_usuario" style="display: none;">
        </div>
        <div class="barra_usuario_busca_container">
            <form method="post">
                <div class="bu_caixa_texto"><input name="nome_novo_espaco" type="text" onkeyup="buscarSugestao(this.value)"></div>
                <div class="bu_botao_novo"><input type="submit" value="Novo" name="submit_novo_espaco" id="new-space-submit"></div>
                <!-- 
                O processamento PHP do formulário é feito na home, pois é preciso usar o header
                e a barra do usuário é carregada na home, depois de saídas HTML
                -->
            </form>
        </div>
        <div class="resultado_busca" id="div_resultado_busca"></div>
    </div>
</div>
<!-- ********************************** Carregando o Firebase ************************************ -->
<script src="https://www.gstatic.com/firebasejs/4.3.0/firebase.js"></script>
<!-- ************************************* Scripts da home *************************************** -->
<script>
    // ****************************** INÍCIO: FUNÇÕES FIREBASE ***************************************
    // Iniciando o SDK do Firebase
    var config = {
        apiKey: "AIzaSyCMgLyuJrzW_mFobJR8Vy-yYcMi6i4n0hA",
        authDomain: "le16project.firebaseapp.com",
        databaseURL: "https://le16project.firebaseio.com",
        projectId: "le16project",
        storageBucket: "le16project.appspot.com",
        messagingSenderId: "288102999150"
    };
    firebase.initializeApp(config);
    // Função geral, cahamada ao carregar a página para gerenciar as funções específicas
    function le16() {
        this.checkSetup();
        // Shortcuts to DOM Elements.
        this.signOutButton = document.getElementById('sign-out');
        // Event listeners
        this.signOutButton.addEventListener('click', this.signOut.bind(this));
        // Função para configurações iniciais
        this.initFirebase();
    }
    // Sets up shortcuts to Firebase features and initiate firebase auth.
    le16.prototype.initFirebase = function() {
        // Shortcuts to Firebase SDK features.
        this.auth = firebase.auth();
        //this.database = firebase.database();
        this.storage = firebase.storage();
        // Initiates Firebase auth and listen to auth state changes.
        this.auth.onAuthStateChanged(this.onAuthStateChanged.bind(this));
    };
    //
    le16.prototype.onAuthStateChanged = function(user) {
        if (user) { // User is signed in!
            // Pegando os dados Firebase do Usuário
            var userFirebaseName = user.displayName;
            // Preenchendo a tag com a saudação ao usuário
            document.getElementById("ola_usuario").innerHTML = 'Olá '+userFirebaseName;
            // Pegando a imagem de perfil do usuário
            //*var profilePicUrl = user.photoURL;
            // Set the user's profile pic and name.
            //*this.userPic.style.backgroundImage = 'url(' + profilePicUrl + ')';

        }// else { User is signed Out }
    };
    // Função chamada quando o usuário clica em sair no menu da barra do usuário
    le16.prototype.signOut = function() {
        // Sign out of Firebase.
        this.auth.signOut();
        // Para chamar a função de destruição da sessão e redirecionamento na loadSession.php
        window.location.assign("home.php?logout=true");
    };
    
    // Função necessária para a fase de desenvolvimento
    // Checks that the Firebase SDK has been correctly setup and configured.
    le16.prototype.checkSetup = function() {
        if (!window.firebase || !(firebase.app instanceof Function) || !firebase.app().options) {
            window.alert('You have not configured and imported the Firebase SDK. ' +
                'Make sure you go through the codelab setup instructions and make ' +
                'sure you are running the codelab using `firebase serve`');
        }
    };
    // Chamando a função geral (que gerencia as específicas) ao carregar a página
    window.onload = function() {
        window.le16 = new le16();     
    };
    // ******************************** FIM: FUNÇÕES FIREBASE ***************************************
    
    // Função chamada quando o usuário digita no campo de pesquisa da barra do usuário
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
            var searchPostman = new XMLHttpRequest();
            searchPostman.onreadystatechange = function() {
                if (this.readyState === 4 && this.status === 200) {
                    /*Recebe a string de resultados do servidor (já com html/css) e joga na div 'div_resultado_busca'*/
                    document.getElementById("div_resultado_busca").innerHTML = this.responseText;
                }
            };
            // O envio pode ser feito com GET, pois o que o usuário pesquisa não é tradado como uma informação confidencial
            searchPostman.open("GET", "../config/ajax/userBarSearch.php?q="+str, true);
            searchPostman.send();
        }
    };
    // Função chamada quando o usuário clica em algum espaço listado no resultado da busca
    function registrarEntradaUsuario(idEspaco){
        // Pegando o id do usuário no campo invisível
        var idUsuario = document.getElementById("id_invisivel_usuario").value;
        var spaceRef = 'space-'+String(idEspaco);
        // AJAX para registrar a entrada do usuário
        var checkInPostman = new XMLHttpRequest();
        checkInPostman.onreadystatechange = function() {
            if (this.readyState === 4 && this.status === 200) {
                // Se o registro foi bem sucedido
                if(this.responseText==='true'){
                    // Atualiza a URL para exibir o espçao clicado
                    window.location.assign('home.php?ss=sp&ids='+idEspaco);
                } // Se o registro não foi bem sucedido ou o usuário já está no espaço: Não faz nada, apenas não entra
            }
        };
        formCheckIn = new FormData(); // Cria um objeto do tipo formulário com codificação multipart/form-data (permite enviar arquivos)
        formCheckIn.append('idUsuario',idUsuario);// Adiciona a variável 'idUsuario' como se um campo type=text (nesse caso) tivesse sido preenchido com a variável
        formCheckIn.append('idEspaco',idEspaco);
        checkInPostman.open("POST", "../config/ajax/userSpaceCheckin.php", true); // Chama o script para tratar os dados do formulário
        checkInPostman.send(formCheckIn); // Equivalente a clicar em um submit e enviar o formulário
    };
    // Mostra/oculta o conteúdo do menu ao clicar no botão do menu
    function mostrarMenu() {
        document.getElementById("barra_usuario_menu_conteudo").classList.toggle("barra_usuario_menu_mostrar_conteudo");
    };
    /* Para ocultar o conteúdo do meno ao clicar fora */
    window.onclick = function(event) {
        if (!event.target.matches('.barra_usuario_menu_botao')) {
            var divConteudoMenu = document.getElementById("barra_usuario_menu_conteudo");
            if (divConteudoMenu.classList.contains('barra_usuario_menu_mostrar_conteudo')) {
                divConteudoMenu.classList.remove('barra_usuario_menu_mostrar_conteudo');
            }
        }
    };
</script>
