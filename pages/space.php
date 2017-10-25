<!-- O script PHP para validar e pegar os dados do espaço está no começo da home.php, pois precisa usar o header() -->
<!-- ... -->
<!-- O campo abaixo é invisível. Criado apenas para servir o JS (loadMessages()) para criar a referencia firebase-->
<input type="text" value="<?php echo $idEspacoUrl; ?>" id="id_invisivel_espaco" style="display: none;">
<!-- O campo abaixo é invisível. Criado apenas para servir o JS (le16space()) que mostra a data de criação do espçao-->
<input type="text" value="<?php echo $dataCriacao; ?>" id="data_criacao-invisivel" style="display: none;">
<!-- Identificador Firebase do usário que criou o espaço. Também invisível, para mostrar no cabecçalio do espaço-->
<input type="text" value="<?php echo $criadorEspaco; ?>" id="fbuid_criador_invisivel" style="display: none;">
<!-- Número de usuários no espaço ao carregar a página. Também invisível, para verificar se é o primeiro acesso do criador-->
<input type="text" value="<?php echo $numeroUsuarios; ?>" id="numero_usuarios_inicial" style="display: none;">
<!-- Estado da visibilidade do espçao ('yes' ou 'no') -->
<input type="text" value="<?php echo $visibilidade; ?>" id="estado_visibilidade" style="display: none;">
<!-- ... -->
<div class="espaco_container">
    <div class="espaco_cabecalio">
        <div class="espaco_cabecalio_menu">
            <!-- O id do espaço é atribuido ao carregar a home, por leitura direta da URL -->
            <span onclick="mostrarInfoEspaco();" class="espaco_cabecalio_sair" id="mostrar_info_link">mais</span>
            <span onclick="sairEspaco(<?php echo $idEspacoUrl; ?>);" class="espaco_cabecalio_sair">sair</span>
        </div>
        <!-- Nome do espaço, atribuido no script de validação de acesso ao espaço no início da home -->
        <p class="titulo"><?php echo $nomeEspaco; ?></p>
        <?php
            // Verificando o estado de visibilidade do espaço
            $displayIconeEspacoInvisivel = ($visibilidade=='yes') ? 'none' : 'block';
        ?>
        <div class="icone_espaco_invisivel" style="display: <?php echo $displayIconeEspacoInvisivel; ?>;"></div><!-- F5 -->
        <div class="espaco_info" id="espaco_info">
            <div class="usuario_criador">
                <!-- <img class="perfil" src="stylesheets/backgrounds/profile_placeholder.png"> -->
                <div class="pic" id="user_creator_photo"></div><!-- novo -->
                <div class="nome" id="user_creator_name"></div>
                <div class="data" id="data-criacao"></div>
            </div>
            <div class="outros_usuarios" id="div_geral_numero_usuarios" onclick="mostrarListaUsuarios();">
                <!--<img class="icone" src="stylesheets/backgrounds/people3.png">-->
                <!--<p class="numero" id="numero_usuarios"><\?php echo $numeroUsuarios; ?></p>-->
                <div class="numero" id="numero_usuarios"></div>
                <div class="texto">ver lista</div>
            </div>
            <!-- *********************************** CATRACA *************************************** -->
            <div class="catraca_container" id="catraca_container">
                    <div class="pic" id="catraca_user_pic"></div>
                    <div class="nome" id="catraca_user_name"></div>
                    <div class="sentido" id="catraca_sentido">entrou</div>
            </div>
            <!-- ################################# OPÇÕES BTN ###################################### -->
            <div class="menu_opcoes_container" id="menu_opcoes_container">
                <label title="Opções" onclick="mostrarOpcoes();">
                    <div class="opcoes_btn"><div class="opcoes_icone">&#8942;</div></div>
                </label>
            </div>
        </div>
    </div>
    <div class="espaco_mensagens_container" id="mensagens_container">
        <div id="messages"></div><!-- *************************** FIREBASE STYLESHEET (codelab.css) -->
    </div>
    <!-- ############################### Formulário do convite ################################### -->
    <div class="convite_conatainer" id="convite_container">
        <div class="campo">
            <div class="nome_campo">Convidar:</div>
            <div class="nome_convidado" id="convite_nome_convidado"></div>
        </div>
        <div class="campo">
            <div class="nome_campo">Para:</div>
            <select id="convite_lista_espacos"></select>
        </div>
        <div class="campo">
            <div class="nome_campo">Mensagem:</div>
            <textarea id="convite_msg"></textarea>
        </div>
        <div class="campo">
            <button onclick="enviarConvite();">ENVIAR</button>
            <button onclick="cancelarConvite();">CANCELAR</button>
        </div>
    </div>
    <!-- ############################## LISTA DE USUÁRIOS NO ESPAÇO ################################ -->
    <div class="espaco_lista_usuarios" id="lista_usuarios_container">
        <!-- ######################### IMAGEM DE PERFIL AMPLIADA ########################### -->
        <div class="img_perfil_ampliada_container" id="img_perfil_ampliada_container">
            <div class="fechar_img_ampliada" id="fechar_img_ampliada" onclick="fecharImgPerfilAmpliada();">&times;</div>
            <div class="img_perfil_ampliada" id="img_perfil_ampliada"></div>
        </div>
        <div class="cabecalio">
            <span onclick="ocultarListaUsuarios();" class="voltar_link" id="lista_voltar_link">voltar</span>
            <p class="titulo">Nesse momento</p>
        </div>
        <div id="container_lista_usuarios"></div>  
    </div>
    <!-- ####################################### OPÇÕES ######################################### -->
    <div class="opcoes_container" id="opcoes_container">
        <div class="cabecalio">
            <span class="voltar_link" onclick="esconderOpcoes();">voltar</span>
            <div class="titulo">Opções:</div>
        </div>
        <div class="item_opcoes">
            <div class="texto">Tornar o espaço visível?</div>
            <div class="radios">
                <label><input type="radio" name="visibilidade" id="visible_Y" onchange="mostrarConfirmaOpcao();"> Sim </label> &ensp;
                <label><input type="radio" name="visibilidade" id="visible_N" onchange="mostrarConfirmaOpcao();"> Não </label>
            </div>
        </div>
        <div class="confirma_opcao_container" id="confirma_opcao_container">
            <div class="confirmar_btn" onclick="atualizarOpcao();">Confirmar</div>
            <div class="cancelar_btn" onclick="cancelarOpcoes();">Cancelar</div>
        </div>
    </div>
    <!-- ######################### AREA PARA DIGITAR E ENVIAR MENSAGNES ########################### -->
    <div class="espaco_area_texto" id="area_input">
        <form id="message-form" action="#">
            <input type="text" id="message-text" class="area_texto_item">
            <input type="submit" id="message-submit" value="Env" class="area_texto_item">
        </form>
        <form id="image-form" action="#">
            <input id="media-capture" type="file" accept="image/*,capture=camera" style="display: none;">
            <input type="submit" id="image-submit" value="Img" class="area_texto_item">
        </form>
    </div>
</div>
<script>
    // ************************************ INÍCIO: FUNÇÕES FIREBASE *********************************************
    
    // O Firebase já é inicializado na userBar. Aqui vão apenas as funções para tratar as mensagens e as informações
    // do espaço, que dependem de elementos que só existem quando um espaço está aberto.
     
    function le16space() {
        
        // Shortcut to Firebase SDK feature.
        this.database = firebase.database();
        
        //***************************** CARREGANDO INFORMAÇÕES NO CABEÇALIO DO ESPAÇO ***********************************
        
        // 1) Data de criação do espaço (No fusorário do usuário/browser)

        //var userTimeOffset = new Date().getTimezoneOffset(); NÃO É PRECISO, POIS OS MÉTODOS getDate()... DESCONTAM O OFFSET DA TIMEZONE DO USUÁRIO AUTOMATICAMENTE
        var utcTimeCreation = new Date(1000*(document.getElementById("data_criacao-invisivel").value));
        var year = utcTimeCreation.getFullYear();
        var month = utcTimeCreation.getMonth() + 1;
        var day = utcTimeCreation.getDate();
        var hour = utcTimeCreation.getHours();
        var min = utcTimeCreation.getMinutes();
        /*
         * Acrescentar algoritmo para saber se é 'Hoje', 'Ontém' ou antes (mostrar data normal) 
         */
        var userTimeCreation = day+'/'+month+'/'+year+' às '+hour+':'+min;
        document.getElementById("data-criacao").innerHTML = userTimeCreation;
        
        // 2) Nome e imagem de perfil do criador do espaço
        
        // Pegando o ID Firebase do usuário criador do espaço em um campo invisível no início do script
        var userCreatorId = document.getElementById('fbuid_criador_invisivel').value;
        // Pegando a referêcia do usuário no child 'users' no Firebase DB
        var userCreatorRef = this.database.ref('users/'+userCreatorId);
        // Lendo (uma única vez) as infos na ref do usuário criador
        userCreatorRef.once('value').then(function(snapshot) {
            var userName = snapshot.val().userName;
            var creatorPhotoUrl = snapshot.val().userPhotoUrl;
            // Preenchendo as divs com Imagem de perfil e nome no usuário criador
            document.getElementById('user_creator_photo').style.backgroundImage = 'url('+creatorPhotoUrl+')';
            document.getElementById('user_creator_name').innerHTML = userName;
        }).catch(function(error) { console.error('Dev Msg: Erro ao carregar as infos de criação', error); }); 
        
        // 3) Quantidade de usuários no espaço (atualizado dinâmicamente)

        // Pegando o id do espaço aberto em um campo invisível no início do script
        var idEspaco = document.getElementById('id_invisivel_espaco').value;
        // Número de usuários ao carregar a página (servidor)
        var nUsuarios = document.getElementById('numero_usuarios_inicial').value;
        // Inicializando a div com o número de usuários
        document.getElementById('numero_usuarios').innerHTML = nUsuarios;
        // Referência com o ID do espaço em um child exclusivo (spaces) para listar os espaços abertos no Firebase DB
        var spaceRef = this.database.ref('spaces/space-'+idEspaco);
        // Para remover qualquer referência anterior
        spaceRef.off();
        // Contador do listener de adição (Variável adotada para que o número de usuários mostrado não dependa do listener ao recarregar a página)
        var contAdd = 0;
        // Listener para atualizar (somar 1) o número de usuários se algum usuário ENTRAR do espçao
        spaceRef.on('child_added', function(data) { // Callback simples
            contAdd = contAdd + 1;
            if (contAdd>nUsuarios) {
                // Atualizando o contador de usuários
                nUsuarios = nUsuarios - (-1);
                document.getElementById('numero_usuarios').innerHTML = nUsuarios;
                // Paranauês para mostrar o novo usuário com fade-out
                document.getElementById("catraca_container").style.display = 'none';
                document.getElementById("catraca_container").style.opacity = "1";
                document.getElementById("catraca_user_pic").style.backgroundImage = 'url(' + data.val().userPhotoUrl + ')';
                document.getElementById("catraca_user_name").innerHTML = data.val().userName;
                document.getElementById("catraca_container").style.display = 'block';
                setTimeout(function(){ 
                    document.getElementById("catraca_container").style.opacity = "0";
                }, 2000);
                
            }
            /* TESTAR COM MAIS DE 2 USUÁRIOS ANTES DE APAGAR
            var numeroUsuariosAtual = document.getElementById('numero_usuarios').innerHTML;
            document.getElementById('numero_usuarios').innerHTML = numeroUsuariosAtual - (-1);
            // Informar quem entrou? numero_usuarios_inicial
            
            if ((numeroUsuariosAtual - (-1)) > nUsuarios) {
                nUsuarios = nUsuarios - (-1);
                
                document.getElementById("catraca_container").style.display = 'none';
                document.getElementById("catraca_container").style.opacity = "1";

                document.getElementById("catraca_user_pic").style.backgroundImage = 'url(' + data.val().userPhotoUrl + ')';
                document.getElementById("catraca_user_name").innerHTML = data.val().userName;

                document.getElementById("catraca_container").style.display = 'block';

                setTimeout(function(){ 
                    document.getElementById("catraca_container").style.opacity = "0";
                    //document.getElementById("catraca_user_name").innerHTML = "";
                }, 2000);
            }
            */
            
        });// Não há que fazer se der errado
        // Listener para atualizar (subtrair 1) o número de usuários se algum usuário SAIR do espaço
        spaceRef.on('child_removed', function(data) { // Callback simples
            contAdd = contAdd - 1;
            nUsuarios = nUsuarios - 1;
            //##var numeroUsuariosAtual = document.getElementById('numero_usuarios').innerHTML;
            //##document.getElementById("numero_usuarios").innerHTML = numeroUsuariosAtual - 1;
            document.getElementById("numero_usuarios").innerHTML = nUsuarios;
            // Informar quem saiu?
        });// Não há que fazer se der errado
        
        // 4) Exibindo o botão para alterar as opções do espaço, caso o usuário seja o criador

        if (document.getElementById("fbid_invisivel_usuario").value === userCreatorId) {
            document.getElementById("menu_opcoes_container").style.display = 'block';
        }
        //*******************************************************************************************************************
        
        // Shortcuts to DOM Elements.
        this.messageList = document.getElementById('messages'); // Div onde são exibidas as mensagens
        this.messageForm = document.getElementById('message-form'); // Formulário para escrver e enviar mensagens
        this.messageInput = document.getElementById('message-text'); // Campo de texto onde são escritas as mensagens
        this.submitButton = document.getElementById('message-submit'); // Botão para enviar as mensagens
        this.submitImageButton = document.getElementById('image-submit'); // Botão para enviar Imagens
        this.imageForm = document.getElementById('image-form');
        this.mediaCapture = document.getElementById('media-capture');
        this.numeroUsuarios = document.getElementById('div_geral_numero_usuarios');
        this.listaUsuarios = document.getElementById('container_lista_usuarios');
        
        // Eventlisteners
        this.messageForm.addEventListener('submit', this.saveMessage.bind(this));
        this.numeroUsuarios.addEventListener('click', this.loadUsers.bind(this)); // Sem o ".bind(this)" o this.database e a função displayUserRow() não são reconhecidos
        
        // Events for image upload.
        this.submitImageButton.addEventListener('click', function(e) {
            e.preventDefault();
            this.mediaCapture.click();
        }.bind(this));
        this.mediaCapture.addEventListener('change', this.saveImageMessage.bind(this));
        
        // Carrega as 12 últimas mensagens que (se) que estiverem registradas na referencia firebase do espaço
        this.loadMessages();
        
    }
      
    // Template para as mensagens do usuário
    MESSAGE_TEMPLATE_USER =
        '<div class="message-container-user">' +
            '<div class="spacing"><div class="pic"></div></div>' +
            '<div class="message"></div>' +
            '<div class="name"></div>' +
        '</div>';
    // Template para as mensagens dos outros participantes do grupo
    MESSAGE_TEMPLATE_OTHERS =
        '<div class="message-container-others">' +
            '<div class="spacing"><div class="pic"></div></div>' +
            '<div class="message"></div>' +
            '<div class="name"></div>' +
        '</div>';
    // A loading image GIF
    LOADING_IMAGE_URL = 'https://www.google.com/images/spin-32.gif';
    // Função chamada ao criar/entrar em um espaço
    le16space.prototype.loadMessages = function() {
        // Pegando o id do espçao em um campo invível no ínico da página, criado ao exibir o espaço
        var spaceId = document.getElementById('id_invisivel_espaco').value;
        // Pegando referência do espaço no firebase DB (Se no momento da chamada a referencia não existe, então uma nova é criada)
        this.messagesRef = this.database.ref('messages/space-'+spaceId);
        // Make sure we remove all previous listeners.
        this.messagesRef.off();
        
        //TRANSACTION (Para contar mensagens)
        this.countMsgsRef = this.database.ref('counters/space-'+spaceId+'/messages');
        this.countMsgsRef.off();
        
        // Loads the last 12 messages and listen for new ones.
        var setMessage = function(data) {
            var val = data.val();
            // Chamando a função para exibir as mensagens carrregadas
            this.displayMessage(data.key, val.uid, val.name, val.text, val.photoUrl, val.imageUrl);
        }.bind(this);
        this.messagesRef.limitToLast(12).on('child_added', setMessage); // Não há que fazer se der errado (enviar de novo)
        this.messagesRef.limitToLast(12).on('child_changed', setMessage); // Não há que fazer se der errado (enviar de novo)
    };
    // Displays a Message in the UI.
    le16space.prototype.displayMessage = function(key, uid, name, text, picUrl, imageUri) {
        // Pegando o fbid do usuário num campo invisível na userBar()
        var fbidUsuario = document.getElementById("fbid_invisivel_usuario").value; 
        // Pegando o elemento da mensagem (cada mensagme tem uma chave única) se ela já existir
        var msgDiv = document.getElementById(key);
        // If an element for that message does not exists yet we create it.
        if (!msgDiv) {
            // Criando uma instancia de usuário Firebase para pegar o ID e comparar com ID registrado em cada mensagem
            /*
             * PEGAR O fbuid DO CAMPO INVISÍVEL (eficiência)
             */
            //if (uid==firebase.auth().currentUser.uid) { // Se o usuário é dono da mensagem
            if (uid===fbidUsuario) {
                var container = document.createElement("div");
                container.innerHTML = MESSAGE_TEMPLATE_USER;
                msgDiv = container.firstChild;
                msgDiv.setAttribute("id", key);
                this.messageList.appendChild(msgDiv);
            } else { // Se não é
                var container = document.createElement("div");
                container.innerHTML = MESSAGE_TEMPLATE_OTHERS;
                msgDiv = container.firstChild;
                msgDiv.setAttribute("id", key);
                this.messageList.appendChild(msgDiv);
            }
        }
        // Se a mensagem tem uma imagem de perfil registrada
        if (picUrl) {
            msgDiv.querySelector('.pic').style.backgroundImage = 'url(' + picUrl + ')';
        }
        // Adicionando o nome registrado na mensagem
        msgDiv.querySelector('.name').textContent = name;
        // Adicionando o texto ou a imagem enviada
        var messageElement = msgDiv.querySelector('.message');
        if (text) { // If the message is text.
            messageElement.textContent = text;
            // Replace all line breaks by <br>.
            messageElement.innerHTML = messageElement.innerHTML.replace(/\n/g, '<br>');
            /*
             * Acrescentar algoritmo para quebrar palavras (qualquer conjunto de letras) grandes 
             * que possam ultrapassar o conatiner da mensagem (100 letras escritas juntas são exibidas juntas)
             */
        } else if (imageUri) { // If the message is an image.
            var image = document.createElement('img');
            image.addEventListener('load', function() {
                this.messageList.scrollTop = this.messageList.scrollHeight;
            }.bind(this));
            this.setImageUrl(imageUri, image);
            messageElement.innerHTML = '';
            messageElement.appendChild(image);
        }
        // Show the card fading-in.
        setTimeout(function() {msgDiv.classList.add('visible');}, 1);
        this.messageList.scrollTop = this.messageList.scrollHeight;
        this.messageInput.focus();
    };
    // Função chamada quando o botão enviar é clicado. Saves a new message on the Firebase DB.
    le16space.prototype.saveMessage = function(e) {
        e.preventDefault();
        // Check that the user entered a message.
        if (this.messageInput.value) {
            var currentUser = firebase.auth().currentUser;
            // Add a new message entry to the Firebase Database.
            this.messagesRef.push({ // O método push cria uma chave única automaticamente
                uid: currentUser.uid,
                name: currentUser.displayName,
                text: this.messageInput.value,
                photoUrl: currentUser.photoURL || 'backgrounds/profile_placeholder.png'
            }).then(function() {
                
                // TRANSACTION (para contar mensagens)
                this.countMsgsRef.transaction(function (current_value) {
                    return (current_value || 0) + 1;
                });
                
                // Clear message text field
                this.messageInput.value = '';
            }.bind(this)).catch(function(error) {
                console.error('Error writing new message to Firebase Database', error);
            });

        }
    };
    
    // Função chamada quando o botão enviar Imagem é clicado
    // Saves a new message containing an image URI in Firebase. This first saves the image in Firebase storage.
    le16space.prototype.saveImageMessage = function(event) {
        event.preventDefault();
        var file = event.target.files[0];

        // Clear the selection in the file picker input.
        this.imageForm.reset();

        // Check if the file is an image.
        if (!file.type.match('image.*')) {
            window.alert('Por favor selecione uma imagem.');
            return; // Para encerrar o método aqui mesmo
            /*
            var data = {
                message: 'You can only share images',
                timeout: 2000
            };
            this.signInSnackbar.MaterialSnackbar.showSnackbar(data);
            return;
            */
        }

        // We add a message with a loading icon that will get updated with the shared image.
        var currentUser = firebase.auth().currentUser;
        this.messagesRef.push({
            uid: currentUser.uid,
            name: currentUser.displayName,
            imageUrl: LOADING_IMAGE_URL,
            photoUrl: currentUser.photoURL || 'backgrounds/profile_placeholder.png'
        }).then(function(data) {

            /*
             * CONFIGURAÇÃO INICIAL DO FIREBASE/STORAGE/RULES
             service firebase.storage {
                 match /b/{bucket}/o {
                     match /{allPaths=**} {
                         allow read, write: if false;
                     }
                 }
             }
            * FOI ALTERADO PARA A CONFIGURAÇÃO ATUAL COM O EXEMPLO DO WEB CODELAB
            */
            // Upload the image to Cloud Storage.
            var filePath = currentUser.uid + '/' + data.key + '/' + file.name;
            return firebase.storage().ref(filePath).put(file).then(function(snapshot) {
                // Get the file's Storage URI and update the chat message placeholder.
                var fullPath = snapshot.metadata.fullPath;
                return data.update({imageUrl: firebase.storage().ref(fullPath).toString()});
            }.bind(this));
        }.bind(this)).catch(function(error) {
            console.error('There was an error uploading a file to Cloud Storage:', error);
        });

    };
    // Sets the URL of the given img element with the URL of the image stored in Cloud Storage.
    le16space.prototype.setImageUrl = function(imageUri, imgElement) {
        // If the image is a Cloud Storage URI we fetch the URL.
        if (imageUri.startsWith('gs://')) {
            imgElement.src = LOADING_IMAGE_URL; // Display a loading image first.
            firebase.storage().refFromURL(imageUri).getMetadata().then(function(metadata) {
                imgElement.src = metadata.downloadURLs[0];
            });
        } else {
            imgElement.src = imageUri;
        }
    };
    
    // Função chamada ao criar/entrar em um espaço
    le16space.prototype.loadUsers = function() {
        // Pegando o id do espçao em um campo invível no ínico da página, criado ao exibir o espaço
        var spaceId = document.getElementById('id_invisivel_espaco').value;
        // Pegando referência do espaço no firebase DB
        this.userListRef = this.database.ref('spaces/space-'+spaceId);
        // Make sure we remove all previous listeners.
        this.userListRef.off();
        // Carregando todos os usuários registrados no espaço
        var setUserRow = function(data) {
            var val = data.val();
            // Chamando a função para exibir as informações do usuário
            this.displayUserRow(data.key, val.userName, val.userPhotoUrl, val.userMsgStatus);
        }.bind(this);
        this.userListRef.on('child_added', setUserRow); // Não há o que fazer se der errado (F5)
    };  
    // Template para a linha do usuário na lista
    USER_ROW_TEMPLATE =
        '<div class="item-lista-usuario">' +
            // O atributo "value" é iniciado vazio e preenchido quando o usuário clica no icone do convite
            '<input type="text" value="Bug" id="fbuid_invisivel_destino_convite" style="display: none;">' +
            '<label title="Ampliar" class="label_pic"><div class="pic"></div></label>' +
            '<div class="name"></div>' +
            '<div class="msgStatus"></div>' +
            // Nessa div é acrescentado o JS para exibir o formulário do convite
            '<div class="invite_btn"><div class="invite_icon"></div></div>' +
        '</div>';
    
    // Mostra a linha com as informações básicas do usuário.
    le16space.prototype.displayUserRow = function(key, name, picUrl, msgStatus) {
        
        // Pegando o elemento da mensagem (cada mensagem tem uma chave única) se ela já existir
        var rowDiv = document.getElementById(key);
        // If an element for that message does not exists yet we create it.
        if (!rowDiv) {
            var container = document.createElement("div");
            container.innerHTML = USER_ROW_TEMPLATE;
            rowDiv = container.firstChild;
            rowDiv.setAttribute("id", key);
            // Acrescentando a função para mostrar e preencher as infos do convite no botão (icone) do convite
            rowDiv.querySelector('.invite_btn').setAttribute("onclick", 'mostrarConvite("'+key+'","'+name+'");');
            // key = fbuid, name = name
            // Acrescentando a função para mostrar a imagem de perfil em um tamanho maior
            rowDiv.querySelector('.label_pic').setAttribute("onclick", 'ampliarImgPerfil("'+picUrl+'");');
            this.listaUsuarios.appendChild(rowDiv);            
        }
        // Se o usuário tem uma imagem de perfil
        if (picUrl) {
            rowDiv.querySelector('.pic').style.backgroundImage = 'url(' + picUrl + ')';
        }
        // Adicionando o nome do usuário
        rowDiv.querySelector('.name').textContent = name;
        // Adicionando o texto de status do usuário
        rowDiv.querySelector('.msgStatus').textContent = msgStatus;//"\"Aqui vai o status...\"";
        // Show the card fading-in.
        //setTimeout(function() {msgDiv.classList.add('visible');}, 1);
        //this.messageList.scrollTop = this.messageList.scrollHeight;
        //this.messageInput.focus();
    };
    
    // Chamando a função geral (que gerencia as específicas) ao carregar a página (subpágina space.php)
    window.onload = function() {
        window.le16 = new le16();
        window.le16space = new le16space();       
    };
    // ************************************* FIM: FUNÇÕES FIREBASE *******************************************
    
    // Se o usuário clicar em sair no canto superior direito do espaço aberto
    function sairEspaco(idEspaco) {
        // Pegando o id do usuário em um campo invisível na barra do usuário;
        var idUsuario = document.getElementById("id_invisivel_usuario").value;
        // Pegando o id do próximo espaço em um campo invisível na home;
        var idProximoEspaco = document.getElementById("id_invisivel_proximo_espaco").value;
        // Pegando o id Firebase do usuário em um campo invisível na barra do usuário;
        var fbidUsuario = document.getElementById("fbid_invisivel_usuario").value;
        // AJAX para fechar o espaço
        var checkOutPostman = new XMLHttpRequest();
        checkOutPostman.onreadystatechange = function() {
            if (this.readyState === 4 && this.status === 200) {
                // Recebe a resposta do serverSide e manda ver
                if (this.responseText==='true') { // Se o usuário saiu, mas ainda tem outros no espaço
                    // Alteração Firebase ****************************************************************************
                    // Removendo o child do usuário no banco de espaços
                    firebase.database().ref('spaces/space-'+idEspaco+'/'+fbidUsuario).remove().then( function(){ 
                        if (idProximoEspaco!==0){ // Se existir algum espaço na lista
                            window.location.assign("home.php?ss=sp&ids="+idProximoEspaco);
                        } else {// Se não existir
                            window.location.assign("home.php?ss=ns");
                        }
                    }).catch( function(error) { console.error('Dev Msg: Erro ao remover o registro.',error); });
                    //************************************************************************************************
                } else if (this.responseText==='empty') { // Se era o último usuário do espaço
                    // Alteração Firebase ****************************************************************************
                    // O update e o remove são assíncronos por isso deve-se garantir que o redirect
                    // seja feito somente depois que tudo foi finalizado. Exemplo da estrutura em:
                    // https://firebase.google.com/docs/database/web/read-and-write?hl=pt-br
                    var updatesRemove = {};
                    updatesRemove['messages/space-'+idEspaco] = null;
                    updatesRemove['spaces/space-'+idEspaco+'/'+fbidUsuario] = null;
                    updatesRemove['counters/space-'+idEspaco] = null;
                    firebase.database().ref().update(updatesRemove).then( function() {
                        if (idProximoEspaco!=0) { // Se existir algum espaço na lista
                            window.location.assign("home.php?ss=sp&ids="+idProximoEspaco);
                        } else {// Se não existir
                            window.location.assign("home.php?ss=ns");
                        }
                    }).catch( function(error) { console.error('Dev Msg: Erro ao remover os registros.',error); });
                    //***********************************************************************************************
                }// Acho que falta um else aqui
            }
        };        
        formCheckOut = new FormData(); // Cria um objeto do tipo formulário com codificação multipart/form-data (permite enviar arquivos)
        formCheckOut.append('idUsuario',idUsuario);// Adiciona a variável 'idUsuario' como se um campo type=text (nesse caso) tivesse sido preenchido com a variável
        formCheckOut.append('idEspaco',idEspaco);
        checkOutPostman.open("POST", '../config/ajax/userSpaceCheckout.php', true); // Chama o script para tratar os dados do formulário
        checkOutPostman.send(formCheckOut); // Equivalente a clicar em um submit e enviar o formulário
    };
    // Função para mostrar a div com informações sobre o espaço
    function mostrarInfoEspaco() {
        document.getElementById("espaco_info").classList.toggle("espaco_mostrar_info");
        if(document.getElementById("mostrar_info_link").innerHTML === "mais"){
            document.getElementById("mostrar_info_link").innerHTML = "menos";
        } else {
            document.getElementById("mostrar_info_link").innerHTML = "mais";
        }
    };
    // Função para mostrar a div com a lista de usuários no espaço
    function mostrarListaUsuarios() {
        document.getElementById("area_input").style.display = 'none';
        document.getElementById("mensagens_container").style.display = 'none';
        document.getElementById("opcoes_container").style.display = 'none'; /* #################### NOVO */
        document.getElementById("lista_usuarios_container").style.display = 'block'; 
    };
    // Função para ocultar a lista de usuários e voltar a exibir a conversa
    function ocultarListaUsuarios() {
        document.getElementById("lista_usuarios_container").style.display = 'none';
        document.getElementById("mensagens_container").style.display = 'block';
        document.getElementById("area_input").style.display = 'block';
    };
    // Mostrar e preencher as infos e opções do convite
    function mostrarConvite(fbuid,nome) {
        // Mostrando a div com o formulário do convite
        document.getElementById("convite_container").style.display = 'block';
        // Preenchendo o nome do convidado
        document.getElementById("convite_nome_convidado").innerHTML = nome;
        // Preechendo o fduid do convidado em um campo invisível
        document.getElementById("fbuid_invisivel_destino_convite").value = fbuid;
        // Pegando a lista de espaços em um campo invisível na home
        var listaEspacos = document.getElementById("lista_invisivel_espacos").value;
        // Se a lista não está vazia
        if(listaEspacos){
            // Criando o HTML com cada espaço listado em uma tag "option'
            var strLista = listaEspacos.substr(1); // Para remover o primeiro "&"
            var arrayLista = strLista.split("&");
            var htmlLista = '';
            for (var i = 0; i < arrayLista.length; i++) {
                var par = arrayLista[i].split('=');
                //console.log("id="+pair[0]);
                htmlLista = htmlLista + '<option value="' + par[0]+ '">' + par[1] + '</option>';
            }
        } else { // Se a lista está vazia (pela lógica essa condição nunca será acessada)
            var htmlLista = 'Nenhum espaço para fazer o convite. Entre em um existente ou crie um novo, depois volte aqui.';
        }
        // Acrescentando a lista na tag "select"
        document.getElementById("convite_lista_espacos").innerHTML = htmlLista;
    };
    // Função para registrar o convite no Firebase
    function enviarConvite() {
        // Pegando o fbuid do rementente do convite
        var fbuidOrigem = document.getElementById("fbid_invisivel_usuario").value;
        // Pegando o nome do remetente
        var nomeOrigem = document.getElementById("nome_invisivel_usuario").value;
        // Pegando o endereço coludstorage da imagem de perfíl do remetente
        var picUrlOrigem = document.getElementById("pic_invisivel_usuario").value;
        // Pegando o fbuid do destinatário
        var fbuidDestino = document.getElementById("fbuid_invisivel_destino_convite").value;
        // Pegando o ID do espaço alvo do convite
        var idEespaco = document.getElementById("convite_lista_espacos").value;
        // Nome do espaço selecionado
        var selectTag = document.getElementById("convite_lista_espacos");
        var nomeEspaco = selectTag.options[selectTag.selectedIndex].text;
        // Pegando a mensagem enviada junto com o convite
        var msgConvite = document.getElementById("convite_msg").value;
        
        // Alteração Firebase ******************************************************************************
        // Criando um child com ID único no parent do convidado com as infos do convite no banco de convites 
        firebase.database().ref('invitations/'+fbuidDestino).push({
            origemId: fbuidOrigem,
            origemName: nomeOrigem,
            origemPic: picUrlOrigem,
            spaceId: idEespaco,
            spaceName: nomeEspaco,
            message: msgConvite
        }).then( function(){
            document.getElementById("convite_container").style.display = 'none';
            document.getElementById("convite_nome_convidado").innerHTML = '';
            document.getElementById("convite_msg").value = '';
            //#################################################################################
            // ACRESCENTAR MENSAGEM DE CONFIRMAÇÃO (SNACKBAR?) .. PESQUISAR
            //#################################################################################
        }).catch(function(error) {
            console.error('Dev Msg: Erro ao enviar o convite', error);
            // Acrescentar uma mensagem "userfriendly"
            window.alert('Erro ao enviar o convite. Tente novamente.');
        });
        // *************************************************************************************************
        
    };
    // Função para ocultar o convite e limpar o campo de menssagem
    function cancelarConvite() {
        document.getElementById("convite_container").style.display = 'none';
        document.getElementById("convite_nome_convidado").innerHTML = '';
        document.getElementById("convite_msg").value = '';
    };
    // Função para mostrar a foto de perfil em uma div separada com tamanho maior
    function ampliarImgPerfil(picUrl) {
        // Acrescentando a imagem na div
        document.getElementById("img_perfil_ampliada").style.backgroundImage = 'url(' + picUrl + ')';
        // mostrando a div container
        document.getElementById("img_perfil_ampliada_container").style.display = 'block';
    };
    // Fechar (econder) a imagem de perfil ampliada
    function fecharImgPerfilAmpliada() {
        // Escondendo a div container
        document.getElementById("img_perfil_ampliada_container").style.display = 'none';
        // Resetando a div da imagem ampliada
        document.getElementById("img_perfil_ampliada").style.backgroundImage = 'none';
    };
    // ########################################################################################################
    // Para mostrar as opçoes de configuração do espaço
    function mostrarOpcoes() {
        // Vai e vem das divs
        document.getElementById("area_input").style.display = 'none';
        document.getElementById("mensagens_container").style.display = 'none';
        document.getElementById("lista_usuarios_container").style.display = 'none';
        document.getElementById("opcoes_container").style.display = 'block';
        // Carregando o estado atual da visibilidade do estado
        var visibilidade = document.getElementById("estado_visibilidade").value;
        if (visibilidade==='yes') {
            document.getElementById("visible_Y").checked = true;
            document.getElementById("visible_N").checked = false;
        } else { // ==='no'
            document.getElementById("visible_N").checked = true;
            document.getElementById("visible_Y").checked = false;
        }
    };
    // Para esconder as opcoes e mostrar a conversa
    function esconderOpcoes() {
        document.getElementById("opcoes_container").style.display = 'none';
        document.getElementById("mensagens_container").style.display = 'block';
        document.getElementById("area_input").style.display = 'block';
    };
    // Para mostrar botões de confirmaçao de mudança de opções
    function mostrarConfirmaOpcao() {
        // Carregando o estado atual da visibilidade do estado
        var visibilidade = document.getElementById("estado_visibilidade").value;
        // Lendo o radiobutton "Sim"
        var radioY = document.getElementById("visible_Y").checked;
        // Deduzindo a configuração dos radios
        var newVisibleValue = (radioY) ? "yes" : "no";
        // Verificando se a opção mudou
        if (newVisibleValue===visibilidade) {
            document.getElementById("confirma_opcao_container").style.display = 'none';
        } else {
            document.getElementById("confirma_opcao_container").style.display = 'block';
        }
    };
    // Para fazer a mudança de opção
    function atualizarOpcao() {
        // Pegando o id do espaço
        var idEspaco = document.getElementById("id_invisivel_espaco").value;
        // Lendo o radiobutton "Sim"
        var radioY = document.getElementById("visible_Y").checked;
        // Deduzindo a configuração dos radios
        var newVisibleValue = (radioY) ? "yes" : "no";
        // AJAX para fazer a atualização
        var visibilityPostman = new XMLHttpRequest();
        visibilityPostman.onreadystatechange = function() {
            if (this.readyState === 4 && this.status === 200) {
                // Recebe a resposta do serverSide e manda ver
                if (this.responseText==='true') { // Se a atualização foi bem sucedida
                    // Menssagem informando que atualzação foi bem sucedida
                    window.location.reload(); 
                } else { // Se não foi
                    // Menssagem informando que atualzação NÃO foi bem sucedida (Tentar de novo)
                    console.log('Erro no servidor ao tentar atualizar a visibilidade do espaço.');
                }
            }
        };        
        formVisibility = new FormData(); // Cria um objeto do tipo formulário com codificação multipart/form-data (permite enviar arquivos)
        formVisibility.append('idEspaco',idEspaco);// Adiciona a variável 'idEspaco' como se um campo type=text (nesse caso) tivesse sido preenchido com a variável
        formVisibility.append('visibilidade',newVisibleValue);
        visibilityPostman.open("POST", '../config/ajax/visibilityUpdate.php', true); // Chama o script para tratar os dados do formulário
        visibilityPostman.send(formVisibility); // Equivalente a clicar em um submit e enviar o formulário
    }
    // Para cancelar a mudança de opção
    function cancelarOpcoes() {
        window.location.reload(); 
    };
    // ########################################################################################################
</script>