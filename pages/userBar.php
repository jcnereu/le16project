
<!--
 - Barra do Usuário presente em todas as páginas após o Login;
 - Pega os dados de sessão do script loadSession.php carregado no início de todas as páginas.
-->
<div class="coluna_superior">
    <!-- CRIAR PERFIL (BÁSICO) DO USUÁRIO
    https://firebase.google.com/docs/auth/web/manage-users?hl=pt-br
    Atualizar o perfil de um usuário
    -->
    <div class="barra_usuario_container">
        
        <!-- ######################################## MENU ######################################### -->
        <div class="barra_usuario_menu">
            <div class="barra_usuario_menu_botao" id="menu_botao" onclick="mostrarMenu();"></div>
            <div class="barra_usuario_menu_conteudo" id="barra_usuario_menu_conteudo">
                <div class="triangle"></div><div class="inner_triangle"></div>
                <div class="box">
                    <div class="item_menu" onclick="mostrarEdicaoStatus();">
                        <div class="texto">Status</div>
                    </div>
                    <div class="item_menu" id="sign_out">Sair</div>
                </div>
            </div>
        </div>
        
        <!-- ########################### BOTÃO PESQUISAR NO MODO MOBILE ############################ -->
        <div class="botao_pesquisar" id="botao_pesquisar" onclick="mostrarEspacosListadosMobile();"><div class="botao_pesquisar_icone"></div></div>
        
        <!-- ######################### NOTIFICAÇÃO DE CONVITES RECEBIDOS ########################### -->
        <div class="notificacao_convites_container">
            <div class="botao_contador_container" id="convite_botao_contador_container">
                <div class="botao_lista" onclick="mostrarConvites();"><div class="icone_botao_lista"></div></div>
                <div class="contador_convites" id="contador_convites">0</div>
            </div>
            <!-- A lista é exibida na C3-->
        </div>
        
        <!-- #################################### OLÁ USUÁRIO ###################################### -->
        <div class="barra_usuario_ola_container">
            <div>Olá <b id="ola_usuario"></b></div>
            <!-- O campo abaixo é invisível. Criado apenas para serivir quelaquer JS que precise do ID do usuário-->
            <input type="text" value="<?php echo $dadosUsuario['id']; ?>" id="id_invisivel_usuario" style="display: none;">
            <!-- O campo abaixo é invisível. Criado apenas para serivir quelaquer JS que precise do ID Firebase do usuário-->
            <input type="text" value="<?php echo $dadosUsuario['fb_uid']; ?>" id="fbid_invisivel_usuario" style="display: none;">
            <!-- O campo abaixo é invisível. Criado apenas para serivir o covite e economizar uma consulta no Firebase -->
            <input type="text" value="Bug" id="nome_invisivel_usuario" style="display: none;">
            <!-- NÃO UTILIZADO --><!-- O campo abaixo é invisível. Criado apenas para serivir o covite e economizar uma consulta no Firebase -->
            <input type="text" value="backgrounds/profile_placeholder.png" id="pic_invisivel_usuario" style="display: none;">      
        </div>
        
        <!-- ####################################### BUSCA ######################################### -->
        <div class="barra_usuario_busca_container" id="barra_usuario_busca_container">
            <div class="botao_listar_tudo_mobile" id="botao_listar_tudo_mobile"><button onclick="redirecionarListaTudo();">Ver tudo</button></div>
            <div class="caixa_texto"><input id="nome_novo_espaco" type="text" placeholder="buscar ou criar..." onkeyup="buscarSugestao(this.value);"></div>
            <div class="botao_listar_tudo" id="botao_listar_tudo"><button onclick="redirecionarListaTudo();">Ver tudo</button></div>
        </div>
    </div>
</div>
<!-- ********************************** Carregando o Firebase ************************************ -->
<script src="https://www.gstatic.com/firebasejs/4.3.0/firebase.js"></script>
<!-- ************************************* Scripts da home *************************************** -->
<script>
    // ****************************** INÍCIO: FUNÇÕES FIREBASE ***************************************
    // Iniciando o SDK do Firebase
    var config = {
        /* ESCONDER AS CHAVES PARA OS BACKUPS NO GITHUB */
        apiKey: "Ops! You fail...";
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
        this.signOutButton = document.getElementById('sign_out');
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
            var profilePicUrl = user.photoURL;
            // Preenchendo a tag com a saudação ao usuário
            document.getElementById("ola_usuario").innerHTML = firstName;
            // Preenchendo o botão do menu na barra do usuário com a img de perfil do usuário
            document.getElementById("menu_botao").style.backgroundImage = 'url(' + profilePicUrl + ')';
            // As infos abaixo são guardadas para economizar uma consulta no Firebase ao carregar o convite
            // Nome completo do usuário
            document.getElementById("nome_invisivel_usuario").value = userFirebaseName;
            // Endereço da imagem de perfil do usuário no cloudstorage (string)
            document.getElementById("pic_invisivel_usuario").value = profilePicUrl;
            
            // Atualizando o nome e a imagem de perfil do usuário 
            // Única forma de manter o perfil sincronozado com o perfil do Google (Enquanto não existe meio de atualização interno)
            firebase.database().ref('users/'+user.uid).update({
                // Por enquanto não tem edição de nome e imagem de perfil (São mostrados como vem do Google)
                userName: user.displayName,
                userPhotoUrl: user.photoURL
                // NÃO atualiza o 'userMsgStatus' pq essa info não existe no perfil Google e tem um meio de atualizaççao interno
            });// Não precisa de uma promisse, pq nao tem um redirect na sequencia e ser der errado não há o que fazer (F5)

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
            '<div class="convite">' +
                '<div class="pic"></div>' +
                // Aqui é acrescentada a função para descartar o convite
                '<button class="descartar_btn">Descartar</button>' +
                '<div class="nome"></div>' +
                '<div class="mensagem"></div>' +
                // Aqui é acrescentada a função para registrar a entrada do usuário (tbm remove o convite)
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
         * POR ENQUANTO NÃO EXISTE UMA PÁGINA COM O PERFIL DO USUÁRIO. QUEM RECEBE O CONVITE
         * PODE NO MÁXIMO AMPLIAR A FOTO DE QUEM CONVIDOU (ASSIM COM NA LISTA DE UM ESPAÇO)
         */
        // Acrescentado a função para descartar o contive (remove do Firebase e esconde a div)
        rowDiv.querySelector('.descartar_btn').setAttribute("onclick", 'discardInvitation("'+key+'");');
        // Acrescentando a função de registro de entrado no espaço (Com o key do convite para removê-lo do firebase db)
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
            document.getElementById('convite_botao_contador_container').style.display = 'block';
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
                        // Formatando a lista recebida do servidor
                        var lista = this.responseText;
                        var listaValida = lista.substr(1); // Para remover o primeiro '&'
                        var arrayLista = listaValida.split('&'); // Passando de string para array
                        //Inicializando o vetor de refs para remoção
                        var updatesRemove = {};
                        for (var i = 0; i < arrayLista.length; i++) { // Em cada elemento do array
                            var pair = arrayLista[i].split('='); // Separando a chave e o valor
                            // Listando as refs que devem ser removidas
                            if (pair[1]==='true') { // Se o usuário saiu, mas ainda tem outros no espaço
                                updatesRemove['spaces/space-'+pair[0]+'/'+fbidUsuario] = null;
                            } else if (pair[1]==='empty') { // Se era o último usuário do espaço
                                //******************************************************************************************
                                // Deve-se garantir que o redirect somente seja chamado depois que todos os registros foram
                                // removidos no Firebase. Por isso o redurect é chamado dentro da promise do signOut()
                                //******************************************************************************************
                                updatesRemove['messages/space-'+pair[0]] = null;
                                updatesRemove['spaces/space-'+pair[0]+'/'+fbidUsuario] = null;
                                updatesRemove['counters/space-'+pair[0]] = null;
                            }
                        }
                        // Removendo todas as refs listadas
                        firebase.database().ref().update(updatesRemove).then( function() {
                            // ÚLTIMA ETAPA DO PROCESSO: Sign out of Firebase.
                            firebase.auth().signOut().then( function() {
                                // Para chamar a função de destruição da sessão e redirecionamento na loadSession.php
                                window.location.assign("home.php?logout=true");
                            });
                        }).catch( function(error) { console.error('Dev Msg: Erro ao remover os registros ou fazer o signout.',error); });
                    // Se occoreu algum problema ao fazer os checkouts no servidor
                    } else {
                        console.log('Mensagem provisória: Problema ao registrar saída em um espaço.');
                    }
                }
                // Se o usuário não estava em nehum espaço ao clicar em sair (signout direto)
                firebase.auth().signOut().then( function() {
                    // Para chamar a função de destruição da sessão e redirecionamento na loadSession.php
                    window.location.assign("home.php?logout=true");
                }).catch( function(error) { console.error('Dev Msg: Erro ao fazer signout.',error); });
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
        // Condição para exibir e seconder a div de exibição dos resultados
        if(str.length > 1){
            document.getElementById("lista_espacos_container").style.display = 'none';
            document.getElementById("cabecalio_resultado_busca").style.display = 'block';
            document.getElementById("div_resultado_busca").style.display = 'block';
        } else {
            document.getElementById("div_resultado_busca").style.display = 'none';
            document.getElementById("cabecalio_resultado_busca").style.display = 'none';
            document.getElementById("lista_espacos_container").style.display = 'block';
        }
        // AJAX PARA FAZER A BUSCA ENQUANTO O USUÁRIO DIGITA
        if (str.length > 0) {
            var searchPostman = new XMLHttpRequest();
            searchPostman.onreadystatechange = function() {
                if (this.readyState === 4 && this.status === 200) {
                    // Se a busca não tem resultados exibe o botão para criar um novo espaço
                    if (this.responseText==='noresult') {
                        // Crinando o HTML/CSS do botão
                        var botaoNovoEspaco = '<p class="texto_novo_espaco">Nenhum espaço encontrado.</p>' +
                                              '<button class="botao_novo_espaco" onclick="criarNovoEspaco();">Criar novo</button>';
                        // Exibindo o botão
                        document.getElementById("div_resultado_busca").innerHTML = botaoNovoEspaco;
                    } else { // Se a busca encontrou resultados
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
                    // Registro de entrada do usuário no Firebase (Em JS não é permitido declarar uma função dentro de uma condição)
                    userSnapshotAndRegister(fbidUsuario, idEspaco, inviteKey, false);
                    // ***********************************************************************************
                }
                // Se o registro não foi bem sucedido ou o usuário já está no espaço: Não faz nada, ignora silenciosamente
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
                    // Registro de entrada do usuário no Firebase (Em JS não é permitido declarar uma função dentro de uma condição)
                    userSnapshotAndRegister(fbidUsuario, this.responseText, null, true);
                    // ***********************************************************************************
                } else { // Se o espaço não pôde ser criado (Exibir msg explicando o motivo)
                    window.alert('Mensagem provisória: Problema no banco de dados ou limite de espaços atingido (10)');
                }
            }
        };
        formNewSpace = new FormData(); // Cria um objeto do tipo formulário com codificação multipart/form-data (permite enviar arquivos)
        formNewSpace.append('idUsuario', idUsuario);// Adiciona a variável 'idUsuario' como se um campo type=text (nesse caso) tivesse sido preenchido com a variável
        formNewSpace.append('nomeNovoEspaco', nomeNovoEspaco);
        newSpacePostman.open("POST", "../config/ajax/createNewSpace.php", true); // Chama o script para tratar os dados do formulário
        newSpacePostman.send(formNewSpace); // Equivalente a clicar em um submit e enviar o formulário
    };
    // Lê as infos do usuário e faz o registro de entrada no Firebase
    function userSnapshotAndRegister(fbidUsuario, idEspaco, inviteKey, creationFlag) {
        // Infos do usuário
        firebase.database().ref('users/'+fbidUsuario).once('value').then( function(snapshot) {
            var userData = snapshot.val();
            // Registro de entrada
            firebase.database().ref('spaces/space-'+idEspaco+'/'+fbidUsuario).set({
                userName: userData.userName,
                userPhotoUrl: userData.userPhotoUrl,
                userMsgStatus: userData.userMsgStatus
            }).then( function() {
                //****************************************************************************
                // As condições seguintes nunca acontecem simultaneamente, por isso
                // o redirect pode ser chamado em cada uma
                //****************************************************************************
                // Se a função foi chamada ao aceitar um convite (o id do convite é informado)
                if (inviteKey) {
                    firebase.database().ref('invitations/'+fbidUsuario+'/'+inviteKey).remove().then( function(){
                        // Atualiza a URL para exibir o espaço (Depois de todo o processo)
                        window.location.assign('home.php?ss=sp&ids='+idEspaco);//return;
                    }).catch(function(error) { console.error('Dev Msg: O convite não foi descartado.',error); });
                // Se a função foi chamada na criação de um espaço
                } else if (creationFlag) {
                    // Criando um child com o id do espaço no banco de contadores
                    firebase.database().ref('counters/space-'+idEspaco).set({ 
                        messages: 0 // Apenas contando mensagens
                    }).then( function() {
                        // Atualiza a URL para exibir o espaço (Depois de todo o processo)
                        window.location.assign('home.php?ss=sp&ids='+idEspaco);
                    }).catch(function(error) { console.error('Dev Msg: O contador de msgs não foi iniciado.',error); });
                // Ou se foi chamada ao clicar no resultado de uma busca    
                } else {
                    // Atualiza a URL para exibir o espaço (Depois de todo o processo)
                    window.location.assign('home.php?ss=sp&ids='+idEspaco);
                }
                // Obs: Chamar o redirect nas promisses foi a única forma de garantir que o registro
                // de entrada seja feito em 100% dos casos. De outras formas é bug pra todo lado.
            }).catch(function(error) { console.error('Dev Msg: O registro de entrada não foi bem sucedido.',error); }); 
        }).catch( function(error) { console.error('Dev Msg: Erro ao ler os dados do usuário.',error); });
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
        document.getElementById("c2").classList.add('recuo_c2');
        document.getElementById("c3").style.display = 'block';
        document.getElementById("info_espaco_container").style.display = 'none';
        document.getElementById("status_edit_container").style.display = 'none';
        document.getElementById("opcoes_container").style.display = 'none';
        document.getElementById("lista_convites").style.display = 'block';
    };
    // Descartar convites
    function discardInvitation(key) {
        // Escondendo a div do convite
        document.getElementById(key).style.display = 'none';
        // Pegando o fbuid do usuário
        var fbidUsuario = document.getElementById("fbid_invisivel_usuario").value;
        // remove o convite no firebase...
        firebase.database().ref('invitations/'+fbidUsuario+'/'+key).remove(); // Não precisa de promise, pq não tem redirect na sequência
        // Atualizando o número de convites na barra do usuário
        // A rigor deveria utilizar um firebase listener com "child_removed" mais essa solução simples e econômica tbm funciona
        var nConvites = document.getElementById('contador_convites').innerHTML;
        document.getElementById('contador_convites').innerHTML = nConvites - 1;
        // Escondendo o icone de convites se NÃO existir nenhum convite
        if (document.getElementById('contador_convites').innerHTML < 1) {
            document.getElementById('convite_botao_contador_container').style.display = 'none';
        }    
    };
    // Mostrar e tratar edição do status
    function mostrarEdicaoStatus() {
        // Pegando o fbid do usuário no campo invisível
        var fbidUsuario = document.getElementById("fbid_invisivel_usuario").value;
        // Lendo o status do usuário
        firebase.database().ref('users/'+fbidUsuario).once('value').then( function(snapshot) {
            var userMsgStatus = snapshot.val().userMsgStatus;
            document.getElementById("user_msg_status_textarea").value = userMsgStatus;
            // Mostrando a área de edição (se a promise for realizada)
            document.getElementById("c2").classList.add('recuo_c2');
            document.getElementById("c3").style.display = 'block';
            document.getElementById("info_espaco_container").style.display = 'none';
            document.getElementById("lista_convites").style.display = 'none';
            document.getElementById("opcoes_container").style.display = 'none';
            document.getElementById("status_edit_container").style.display = 'block';
        });
    };
    // Atualizar a mensagem de status do usuário
    function atualizarMsgStatus() {
        // Pegando o fbid do usuário no campo invisível
        var fbidUsuario = document.getElementById("fbid_invisivel_usuario").value;
        // Pegando o conteúdo da área de texto na da edição do status
        var strNovoStatus = document.getElementById("user_msg_status_textarea").value;
        // Atualizando
        firebase.database().ref('users/'+fbidUsuario).update({
            userMsgStatus: strNovoStatus
        }).then( function() {
            // Escondendo a área de edição
            document.getElementById("c3").style.display = 'none';
            document.getElementById("c2").classList.remove('recuo_c2');
            document.getElementById("status_edit_container").style.display = 'none';
            
            // Mensagem de confimação
            window.alert('Sua mensagem de status foi atualizada com sucesso!');
        }).catch( function(error) {
            // Mensagens de erro
            console.error('Dev Msg: Erro ao atualizar a msg de status.',error);
            window.alert('Ops! Ocorreu um erro ao atualizar o seu status. Por favor tente novamente.');
        });
    }; 
    // Cancelar edição do status
    function cancelarEdicaoStatus() {
        // Escondendo a área de edição
        document.getElementById("c3").style.display = 'none';
        document.getElementById("c2").classList.remove('recuo_c2');
        document.getElementById("status_edit_container").style.display = 'none';
    };
    // Para alternar a tela para a lista de espaços no modo mobile
    function mostrarEspacosListadosMobile() {
        // Escondendo
        document.getElementById("c2").style.display = 'none';
        document.getElementById("c3").style.display = 'none';
        document.getElementById("botao_pesquisar").style.display = 'none';
        // Mostrando
        document.getElementById("c1").style.display = 'block';
        document.getElementById("barra_usuario_busca_container").style.display = 'block';
        // Alerendo para o layout mobile
        document.getElementById("botao_listar_tudo").style.display = 'none';
        document.getElementById("botao_listar_tudo_mobile").style.display = 'inline-block';
        document.getElementById("cabecalio_lista_espacos").innerHTML = 'Conversas';
    };
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
        /* Deixado com exemplo JS (as divs não devem reagir tem que ter a classe flag_lista)
        if (!event.target.matches(".flag_lista")) {
            var listaDisplay = document.getElementById("lista_convites");
            if (listaDisplay.classList.contains('lista_convites_mostrar_conteudo')) {
                listaDisplay.classList.remove('lista_convites_mostrar_conteudo');
                console.log('remove');
            }
            
        }
        */
    };
</script>
