
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
        <!-- ##################### NOTIFICAÇÃO E LISTA DE CONVITES ########################## -->
        <div class="lista_convite_container">
            <div class="botao_icone_container" id="convite_botao_icone_container">
                <!-- A classe "flag_lista" NÃO tem função de estilo. Serve apenas para marcar quais divs NÃO devem ativar ocultar a lista ao serem clicadas (ver a função window.onclick() no fim do script) -->
                <div class="botao_lista flag_lista" onclick="mostrarConvites();"><div class="icone_botao_lista flag_lista"></div></div>
                <div class="contador_convites" id="contador_convites">0</div>
            </div>
            <div class="lista" id="lista_convites">
                <div class="cabecalio">CONVITES</div>
            </div>
        </div>
        <!-- ############################################################################### -->
        <div class="barra_usuario_ola_container">
            <p id="ola_usuario"></p>
            <!-- O campo abaixo é invisível. Criado apenas para serivir quelaquer JS que precise do ID do usuário-->
            <input type="text" value="<?php echo $dadosUsuario['id']; ?>" id="id_invisivel_usuario" style="display: none;">
            <!-- O campo abaixo é invisível. Criado apenas para serivir quelaquer JS que precise do ID Firebase do usuário-->
            <input type="text" value="<?php echo $dadosUsuario['fb_uid']; ?>" id="fbid_invisivel_usuario" style="display: none;">
            <!-- O campo abaixo é invisível. Criado apenas para serivir o covite e economizar uma consulta no Firebase -->
            <input type="text" value="Bug" id="nome_invisivel_usuario" style="display: none;">
            <!-- O campo abaixo é invisível. Criado apenas para serivir o covite e economizar uma consulta no Firebase -->
            <input type="text" value="backgrounds/profile_placeholder.png" id="pic_invisivel_usuario" style="display: none;">      
        </div>
        <div class="barra_usuario_busca_container">
            <!--<form action="#">-->
                <div class="caixa_texto"><input id="nome_novo_espaco" type="text" placeholder="buscar ou criar..." onkeyup="buscarSugestao(this.value);"></div>
                <div class="botao_listar_tudo" id="botao_listar_tudo"><button onclick="redirecionarListaTudo();">Ver tudo</button></div>
                
            <!--</form>-->
        </div>
        <div class="resultado_busca" id="div_resultado_busca"></div>
        <div class="resultado_novo_espaco" id="div_resultado_novo_espaco"></div>
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
        this.listaConvites = document.getElementById('lista_convites');
        // Event listeners
        this.signOutButton.addEventListener('click', this.signOut.bind(this));
        // Função para configurações iniciais
        this.initFirebase();
        // Carregando e ouvindo os convites enviados para o usuário
        this.loadInvitations();
    };
    // Sets up shortcuts to Firebase features and initiate firebase auth.
    le16.prototype.initFirebase = function() {
        // Shortcuts to Firebase SDK features.
        this.auth = firebase.auth();
        this.database = firebase.database();
        this.storage = firebase.storage();
        // Initiates Firebase auth and listen to auth state changes.
        this.auth.onAuthStateChanged(this.onAuthStateChanged.bind(this));
    };
    //
    le16.prototype.onAuthStateChanged = function(user) {
        if (user) { // User is signed in!
            // Pegando os dados Firebase do Usuário 
            var userFirebaseName = user.displayName;
            var nFirstBlank = userFirebaseName.search(" ");
            var firstName = userFirebaseName.substr(0,nFirstBlank);
            // Preenchendo a tag com a saudação ao usuário
            document.getElementById("ola_usuario").innerHTML = 'Olá '+firstName;
            // Preenchendo um campo invisível com o nome completo do usuário (Para usar no convite a princípio)
            document.getElementById("nome_invisivel_usuario").value = userFirebaseName;
            // Preenchendo um campo invisível com o endereço da imagem de perfil do usuário no cloudstorage (Para usar no convite tbm)
            var profilePicUrl = user.photoURL;
            document.getElementById("pic_invisivel_usuario").value = profilePicUrl;
            // Pegando a imagem de perfil do usuário
            // var profilePicUrl = user.photoURL;
            // Set the user's profile pic and name.
            // this.userPic.style.backgroundImage = 'url(' + profilePicUrl + ')';
            
            // Salvando (sobrescreve depois da primeira vez) o nome e a imagem de perfil do usuário em um child do no Firebase DB
            firebase.database().ref('users/'+user.uid).set({
                userName: user.displayName,
                userPhotoUrl : user.photoURL
            });

        }// else { User is signed Out }
    };
    // Carregando os convites (se) enviados para o usuário
    le16.prototype.loadInvitations = function() {
        // Pegando o fbuid do usuário em um campo invível no ínico da página (preenchido com a sessão (php) ao carregar a página)
        var fbuidUsuario = document.getElementById('fbid_invisivel_usuario').value;
        // Pegando a ref do usuário no banco de Convites (Se no momento da chamada a referencia não existe, então uma nova é criada)
        this.invitationsRef = this.database.ref('invitations/'+fbuidUsuario);
        // Make sure we remove all previous listeners (comentário herança do codelab)
        this.invitationsRef.off();
        // Carregando todos os convites enviados para o usuário
        var setUserInvitation = function(data) {
            var val = data.val();
            // Chamando a função para exibir o convite (O key é gerador por push ao enviar o convite)
            this.displayInvitations(data.key, val.origemId, val.origemName, val.origemPic, val.spaceId, val.spaceName, val.message);   
        }.bind(this);
        this.invitationsRef.on('child_added', setUserInvitation); 
    };
    // Template para cada convite listado
    INVITE_TEMPLATE =
            // A classe "flag_lista" NÃO tem função de estilo. Serve apenas para marcar quais divs NÃO devem ocultar a lista ao serem clicadas (ver a função window.onclick() no fim do script)        
            '<div class="convite flag_lista">' +
                '<div class="pic flag_lista"></div>' +
                // Aqui é acrescentada a função para esconder e depois remover o convite
                '<button class="descartar_btn flag_lista">Descartar</button>' +
                '<div class="nome"></div>' +
                '<div class="mensagem flag_lista"></div>' +
                // Aqui é acrescentada a função para registrar a entrada do usuário ao clicar no espaço (tbm remove o convite)
                '<div class="nome_espaco">&#9656</div>' +
            '</div>';
    // Preenche uma nova div com as informações do convite
    le16.prototype.displayInvitations = function(key, origemId, origemName, origemPic, spaceId, spaceName, message) {
        // Criando a div com um id associado à chave únnica do convite (necessário para identificar o convite no descarte)
        var container = document.createElement("div");
        container.innerHTML = INVITE_TEMPLATE;
        var rowDiv = container.firstChild;
        rowDiv.setAttribute("id", key);
        /*
         * ACRESCENTAR A FUNÇÃO PARA REDIRECIONAR PARA O PERFIL DO USUÁRIO (CRIAR O PERFIL PRIMEIRO)
         */
        // Acrescentado a função para esconder o convite e depois removê-lo do firebase
        rowDiv.querySelector('.descartar_btn').setAttribute("onclick", 'discardInvitation("'+key+'");');
        // Acrescentando a função de registro de entrado no espaço com o segundo argumento não nulo (key do convite) para remover o convite do firebase db
        rowDiv.querySelector('.nome_espaco').setAttribute("onclick", 'registrarEntradaUsuario("'+spaceId+'","'+key+'");');
        this.listaConvites.appendChild(rowDiv);
        
        // Se o usuário de origem tem uma imagem de perfil
        if (origemPic) {
            rowDiv.querySelector('.pic').style.backgroundImage = 'url(' + origemPic + ')';
        }
        // Adicionando o nome do usuário de origem
        rowDiv.querySelector('.nome').textContent = origemName;
        // Se uma mensagem adicional foi enviada
        if (message) {
            rowDiv.querySelector('.mensagem').textContent = message;
        }
        // Adicionando o nome do espaço alvo do convite
        rowDiv.querySelector('.nome_espaco').textContent = spaceName;
        
        // Atualizando o número de convites no contador da barra do usuário
        var nConvites = document.getElementById('contador_convites').innerHTML;
        document.getElementById('contador_convites').innerHTML = nConvites - (-1);
        // Mostrando o ícone da lista de convites se existir pelo menos um convite
        if (document.getElementById('contador_convites').innerHTML>0) {
            document.getElementById('convite_botao_icone_container').style.display = 'block';
        }
        
    };
    // Função chamada quando o usuário clica em sair no menu da barra do usuário
    le16.prototype.signOut = function() {
        
        // Pegando o id do usuário no campo invisível
        var idUsuario = document.getElementById("id_invisivel_usuario").value;
        // Pegando o id Firebase do usuário no campo invisível
        var fbidUsuario = document.getElementById("fbid_invisivel_usuario").value;
        // Pegando a lista de espaços (uma string) em um campo invisível na home
        var listaEspacos = document.getElementById("lista_invisivel_espacos").value;
        
        // AJAX para registrar saída (na userspaces e no Firebase) em todos os espçaos registrados
        var generalCheckoutPostman = new XMLHttpRequest();
        generalCheckoutPostman.onreadystatechange = function() {
            if (this.readyState === 4 && this.status === 200) {
                // Se existir algum espaço registrado
                if (this.responseText !== 'nolist'){
                    // Se os registros de saída na userspaces foram bem sucedidos
                    if (this.responseText !== 'false') {
                        var lista = this.responseText;
                        var listaValida = lista.substr(1); // Para remover o primeiro '&'
                        var arrayLista = listaValida.split('&'); // Passando de string para array
                        for (var i = 0; i < arrayLista.length; i++) { // Em cada elemento do array
                            var pair = arrayLista[i].split('='); // Separando a chave e o valor
                            if (pair[1]==='true') {
                                // Removendo o child do usuário na ref do espaço no Firebase DB
                                firebase.database().ref('spaces/space-'+pair[0]+'/'+fbidUsuario).remove().then( function(){ return; });
                            } else if (pair[1]==='empty') {
                                // Removendo o child com a ref do espçao no banco de mensagens
                                firebase.database().ref('messages/space-'+pair[0]).remove().then( function(){ return; });
                                // Removendo o child do usuário no banco de espaços (remove automaticament a ref do espaço, pois fica vazia)
                                firebase.database().ref('spaces/space-'+pair[0]+'/'+fbidUsuario).remove().then( function(){ return; });
                                // Removendo a ref do espaço no banco de contadores (TRANSACTION para contar mensagens)
                                firebase.database().ref('counters/space-'+pair[0]).remove().then( function(){ return; });
                            }
                        }

                    } else { // Se occoreu algum problema ao fazer os checkouts no servidor
                        window.alert('Mensagem provisória: Problema ao registrar saída em um espaço.');
                    }
                }
                // ÚLTIMA ETAPA DO PROCESSO: Sign out of Firebase.
                firebase.auth().signOut();
                // Para chamar a função de destruição da sessão e redirecionamento na loadSession.php
                window.location.assign("home.php?logout=true");
            }
        };
        generalCheckoutForm = new FormData(); // Cria um objeto do tipo formulário com codificação multipart/form-data (permite enviar arquivos)
        generalCheckoutForm.append('idUsuario', idUsuario);// Adiciona a variável 'idUsuario' como se um campo type=text (nesse caso) tivesse sido preenchido com a variável
        generalCheckoutForm.append('listaEspacos', listaEspacos);
        generalCheckoutPostman.open("POST", "../config/ajax/generalCheckout.php", true); // Chama o script para tratar os dados do formulário
        generalCheckoutPostman.send(generalCheckoutForm); // Equivalente a clicar em um submit e enviar o formulário
        
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
        // Condição para abrir e fechar a div de exibição dos resultados
        if(str.length > 1){
            document.getElementById("div_resultado_busca").style.display = 'block';
        } else {
            document.getElementById("div_resultado_busca").style.display = 'none';
        }
        // AJAX PARA FAZER A BUSCA ENQUANTO O USUÁRIO DIGITA
        if (str.length > 0) {
            var searchPostman = new XMLHttpRequest();
            searchPostman.onreadystatechange = function() {
                if (this.readyState === 4 && this.status === 200) {
                    // Se a busca não tem resultados exibe o botão para criar um novo espaço
                    if (this.responseText==='noresult') {
                        // Ajustando a largura da div para exibir o botão
                        /*
                         * PENSAR EM OUTRA FORMA DE AJUSTE, PQ COM 'width' FIXO NÃO SERÁ POSSÍVEL IMPLEMENTAR O MOBILE FIRST
                         */
                        document.getElementById("div_resultado_busca").style.width = '29%';
                        // Crinado o HTML/CSS do botão
                        var botaoNovoEspaco = '<p class="texto_novo_espaco">Nenhum espaço encontrado.</p>' +
                                              '<button class="botao_novo_espaco" onclick="criarNovoEspaco();">Criar novo</button>';
                        // Exibindo o botão
                        document.getElementById("div_resultado_busca").innerHTML = botaoNovoEspaco;
                    } else { // Se a busca encontrou resultados
                        // Ajustando a largura da div para exibir o resultado
                        document.getElementById("div_resultado_busca").style.width = '40%';
                        // Recebe a string de resultados do servidor (já com html/css) e joga na div 'div_resultado_busca'
                        document.getElementById("div_resultado_busca").innerHTML = this.responseText;
                    }
                }
            };
            // O envio pode ser feito com GET, pois o que o usuário pesquisa não é tradado como uma informação confidencial
            searchPostman.open("GET", "../config/ajax/userBarSearch.php?q="+str, true);
            searchPostman.send();
        }
    };
    
    // Função chamada quando o usuário clica em algum espaço listado no resultado da busca
    // O segundo argumento só é utilizado pela chamada em um convite
    function registrarEntradaUsuario(idEspaco,inviteKey) {
        // Pegando o id do usuário no campo invisível
        var idUsuario = document.getElementById("id_invisivel_usuario").value;
        // Pegando o id Firebase do usuário no campo invisível
        var fbidUsuario = document.getElementById("fbid_invisivel_usuario").value;
        // AJAX para registrar a entrada do usuário
        var checkInPostman = new XMLHttpRequest();
        checkInPostman.onreadystatechange = function() {
            if (this.readyState === 4 && this.status === 200) {
                // Se o registro foi bem sucedido
                if(this.responseText==='true'){
                    // Alteração Firebase ****************************************************************
                    var currentUser = firebase.auth().currentUser;
                    // Criando um child com as infos básicas do usuário na ref do espaço
                    firebase.database().ref('spaces/space-'+idEspaco+'/'+fbidUsuario).set({
                        // Por enquanto não terá o link para o perfil (pq não tem perfil)
                        userName: currentUser.displayName,
                        userPhotoUrl: currentUser.photoURL
                    });
                    // Se a função foi chamada ao aceitar um convite (o segundo argumento é informado)
                    if (inviteKey) {
                        // remove o convite no firebase (Depois de ter feito todo o processo de registro)
                        firebase.database().ref('invitations/'+fbidUsuario+'/'+inviteKey).remove().then( function(){ return; });
                    } 
                    // ***********************************************************************************
                    // Atualiza a URL para exibir o espaço clicado
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
    
    // Função chamada quando o usuário clica no botão "Novo" para criar um novo espaço
    function criarNovoEspaco() {     
        // Pegando o id do usuário no campo invisível
        var idUsuario = document.getElementById("id_invisivel_usuario").value;
        // Pegando o nome do espaço no formulário
        var nomeNovoEspaco = document.getElementById("nome_novo_espaco").value;
        // Pegando o id Firebase do usuário no campo invisível
        var fbidUsuario = document.getElementById("fbid_invisivel_usuario").value;
        // AJAX para criar o novo espaço, fazer o registro na userspaces e no Firebase, e redirecionar
        var newSpacePostman = new XMLHttpRequest();
        newSpacePostman.onreadystatechange = function() {
            if (this.readyState === 4 && this.status === 200) {
                // Se o novo espaço foi reciclado/criado com sucesso
                if (this.responseText !== 'false') {                    
                    // Alteração Firebase ****************************************************************
                    var currentUser = firebase.auth().currentUser;
                    // Criando um child com as infos básicas do usuário no banco de espaços
                    firebase.database().ref('spaces/space-'+this.responseText+'/'+fbidUsuario).set({
                        // Por enquanto não terá o link para o perfil (pq não tem perfil)
                        userName: currentUser.displayName,
                        userPhotoUrl: currentUser.photoURL
                    });
                    // Criando um child com o id do espaço no banco de contadores
                    firebase.database().ref('counters/space-'+this.responseText).set({
                        // Apenas contando mensagens
                        messages: 0
                    });
                    // ***********************************************************************************
                    // Atualiza a URL para exibir o espçao criado
                    window.location.assign('home.php?ss=sp&ids='+this.responseText);
                } else { // Se o espaço não pôde ser criado (Exibir msg explicando o motivo)
                    window.alert('Mensagem provisória: Problema no banco de dados ou limite de espaços (10)');
                }
            }
        };
        formNewSpace = new FormData(); // Cria um objeto do tipo formulário com codificação multipart/form-data (permite enviar arquivos)
        formNewSpace.append('idUsuario', idUsuario);// Adiciona a variável 'idUsuario' como se um campo type=text (nesse caso) tivesse sido preenchido com a variável
        formNewSpace.append('nomeNovoEspaco', nomeNovoEspaco);
        newSpacePostman.open("POST", "../config/ajax/createNewSpace.php", true); // Chama o script para tratar os dados do formulário
        newSpacePostman.send(formNewSpace); // Equivalente a clicar em um submit e enviar o formulário
    };
    
    // Redireciona a página para mostrar a lista de todos os espaços abertos
    function redirecionarListaTudo() {
        // Por enquanto só isso (É geral o suficiente para a introdução de filtros (tags) futuramente)
        window.location.assign('home.php?ss=lt');
    };    

    // Mostra/oculta o conteúdo do menu ao clicar no botão do menu
    function mostrarMenu() {
        document.getElementById("barra_usuario_menu_conteudo").classList.toggle("barra_usuario_menu_mostrar_conteudo");
    };
    // Mostrar/ocultar a lista de convites
    function mostrarConvites() {
        document.getElementById("lista_convites").classList.toggle("lista_convites_mostrar_conteudo");
    };
    // Descartar convites
    function discardInvitation(key) {
        // Escondendo a div do convite
        document.getElementById(key).style.display = 'none';
        // Pegando o fbuid do usuário
        var fbidUsuario = document.getElementById("fbid_invisivel_usuario").value;
        // remove o convite no firebase...
        firebase.database().ref('invitations/'+fbidUsuario+'/'+key).remove().then( function(){ return; });
        // Atualizando o número de convites na barra do usuário
        // A rigor deveria utilizar um firebase listener com "child_removed" mais essa solução simples e econômica tbm funciona
        var nConvites = document.getElementById('contador_convites').innerHTML;
        document.getElementById('contador_convites').innerHTML = nConvites - 1;
        // Escondendo o icone de convites se NÃO existir nenhum convite
        if (document.getElementById('contador_convites').innerHTML < 1) {
            document.getElementById('convite_botao_icone_container').style.display = 'none';
        }    
    }
    // Para ocultar elementos ao clicar fora deles
    window.onclick = function(event) {
        // Escondendo o menu
        if (!event.target.matches('.barra_usuario_menu_botao')) {
            var divConteudoMenu = document.getElementById("barra_usuario_menu_conteudo");
            if (divConteudoMenu.classList.contains('barra_usuario_menu_mostrar_conteudo')) {
                divConteudoMenu.classList.remove('barra_usuario_menu_mostrar_conteudo');
            }
        }
        // Escondendo a lista de convites
        if (!event.target.matches(".flag_lista")) {
            var listaDisplay = document.getElementById("lista_convites");
            if (listaDisplay.classList.contains('lista_convites_mostrar_conteudo')) {
                listaDisplay.classList.remove('lista_convites_mostrar_conteudo');
                console.log('remove');
            }
            
        }
    };
</script>
