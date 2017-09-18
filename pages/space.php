<!-- O script PHP para validar e pegar os dados do espaço está no começo da home.php, pois precisa usar o header() -->
<!-- ... -->
<!-- O campo abaixo é invisível. Criado apenas para servir o JS (loadMessages()) para criar a referencia firebase-->
<input type="text" value="<?php echo $idEspacoUrl; ?>" id="id_invisivel_espaco" style="display: none;">
<!-- O campo abaixo é invisível. Criado apenas para servir o JS (le16space()) que mostra a data de criação do espçao-->
<input type="text" value="<?php echo $dataCriacao; ?>" id="data_criacao-invisivel" style="display: none;">
<!-- Identificador Firebase do usário que criou o espaço. Também invisível, para mostrar no cabecçalio do espaço-->
<input type="text" value="<?php echo $criadorEspaco; ?>" id="fbuid_criador_invisivel" style="display: none;">
<!-- (NÃO ESTÁ SENDO UTILIZADO) Número de usuários no espaço ao carregar a página. Também invisível, para verificar se é o primeiro acesso do criador-->
<input type="text" value="<?php echo $numeroUsuarios; ?>" id="numero_usuarios_inicial" style="display: none;">
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
        <div class="espaco_info" id="espaco_info">
            <div class="usuario_criador">
                <!-- <img class="perfil" src="stylesheets/backgrounds/profile_placeholder.png"> -->
                <div class="pic" id="user-creator-photo"></div><!-- novo -->
                <p class="nome" id="user-creator-name"></p>
                <p class="data" id="data-criacao"></p>
            </div>
            <div class="outros_usuarios" onclick="listarUsuarios();">
                <!--<img class="icone" src="stylesheets/backgrounds/people3.png">-->
                <!-- 
                    ACRESCENTAR O + ANTES DO NÚMERO
                -->
                <!--<p class="numero" id="numero_usuarios"><\?php echo $numeroUsuarios; ?></p>-->
                <p class="numero" id="numero_usuarios"></p>
                <p class="texto">ver lista</p>
            </div>
        </div>
    </div>
    <div class="espaco_mensagens_container" id="mensagens_container">
        <div id="messages"></div><!-- *************************** FIREBASE STYLESHEET (codelab.css) -->
    </div>
    <div class="espaco_lista_usuarios" id="lista_usuarios_container">
        <div class="cabecalio">
            <!-- novo -->
            <span onclick="ocultarListaUsuarios();" class="voltar_link" id="lista_voltar_link">voltar</span>
            <p class="titulo">Nesse momento</p>
        </div>
        Lista aqui...
    </div>
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
    
    // O Firebase já é inicializado na userbar. Aqui vão apenas as funções para tratar as mensagens e as informações
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
            document.getElementById('user-creator-photo').style.backgroundImage = 'url('+creatorPhotoUrl+')';
            document.getElementById('user-creator-name').innerHTML = userName;
        });
        
        // 3) Quantidade de usuários no espaço (atualizado dinâmicamente)

        // Pegando o id do espaço aberto em um campo invisível no início do script
        var idEspaco = document.getElementById('id_invisivel_espaco').value;
        // Referência com o ID do espaço em um child exclusivo (spaces) para listar os espaços abertos no Firebase DB
        var spaceRef = this.database.ref('spaces/space-'+idEspaco);
        // Para remover qualquer referência anterior
        spaceRef.off();
        // Listener para atualizar (somar 1) o número de usuários se algum usuário ENTRAR do espçao
        spaceRef.on('child_added', function(data) {
            var numeroUsuariosAtual = document.getElementById('numero_usuarios').innerHTML;
            document.getElementById('numero_usuarios').innerHTML = numeroUsuariosAtual - (-1);
        });
        // Listener para atualizar (subtrair 1) o número de usuários se algum usuário SAIR do espçao
        spaceRef.on('child_removed', function(data) {
            var numeroUsuariosAtual = document.getElementById('numero_usuarios').innerHTML;
            document.getElementById("numero_usuarios").innerHTML = numeroUsuariosAtual - 1;
        });
        // Pode-se pegar os dados do child adicionado (exemplo: data.val().userName OU data.key): https://firebase.google.com/docs/database/web/lists-of-data?hl=pt-br
        //*******************************************************************************************************************
        
        // Shortcuts to DOM Elements.
        this.messageList = document.getElementById('messages'); // Div onde são exibidas as mensagens
        this.messageForm = document.getElementById('message-form'); // Formulário para escrver e enviar mensagens
        this.messageInput = document.getElementById('message-text'); // Campo de texto onde são escritas as mensagens
        this.submitButton = document.getElementById('message-submit'); // Botão para enviar as mensagens
        this.submitImageButton = document.getElementById('image-submit'); // Botão para enviar Imagens
        this.imageForm = document.getElementById('image-form');
        this.mediaCapture = document.getElementById('media-capture');
        
        // Eventlisteners
        this.messageForm.addEventListener('submit', this.saveMessage.bind(this));
        
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
        // Loads the last 12 messages and listen for new ones.
        var setMessage = function(data) {
            var val = data.val();
            // Chamando a função para exibir as mensagens carrregadas
            this.displayMessage(data.key, val.uid, val.name, val.text, val.photoUrl, val.imageUrl);
        }.bind(this);
        this.messagesRef.limitToLast(12).on('child_added', setMessage);
        this.messagesRef.limitToLast(12).on('child_changed', setMessage);
    };
    // Displays a Message in the UI.
    le16space.prototype.displayMessage = function(key, uid, name, text, picUrl, imageUri) {
        // Pegando o elemento da mensagem (cada mensagme tem uma chave única) se ela já existir
        var msgDiv = document.getElementById(key);
        // If an element for that message does not exists yet we create it.
        if (!msgDiv) {
            // Criando uma instancia de usuário Firebase para pegar o ID e comparar com ID registrado em cada mensagem
            if (uid==firebase.auth().currentUser.uid) { // Se o usuário é dono da mensagem
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
                photoUrl: currentUser.photoURL || '/backgrounds/profile_placeholder.png'
            }).then(function() {
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
            photoUrl: currentUser.photoURL || '/backgrounds/profile_placeholder.png'
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
    
    // Chamando a função geral (que gerencia as específicas) ao carregar a página (subpágina space.php)
    window.onload = function() {
        window.le16 = new le16();
        window.le16space = new le16space();       
    };
    // ************************************* FIM: FUNÇÕES FIREBASE *******************************************
    
    // Se o usuário clicar em sair no canto superio direito do espaço aberto
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
                    if (idProximoEspaco!==0){ // Se existe algum espaço na lista
                        window.location.assign("home.php?ss=sp&ids="+idProximoEspaco);
                    } else {// Se não existe
                        window.location.assign("home.php?ss=ns");
                    }
                    // Alteração Firebase ***********************************************************************************
                    // Removendo o child do usuário na ref do espaço no Firebase DB
                    firebase.database().ref('spaces/space-'+idEspaco+'/'+fbidUsuario).remove();
                    //******************************************************************************************************* 
                } else if (this.responseText==='empty') { // Se era o último usuário do espaço
                    // Alteração Firebase ***********************************************************************************
                    // Removendo o child com a ref do espçao no banco de mensagens
                    firebase.database().ref('messages/space-'+idEspaco).remove();
                    // Removendo o child do usuário na ref do espaço (remove automaticament a ref do espaço, pois fica vazia)
                    firebase.database().ref('spaces/space-'+idEspaco+'/'+fbidUsuario).remove();
                    //*******************************************************************************************************
                    if (idProximoEspaco!==0){ // Se existe algum espaço na lista
                        window.location.assign("home.php?ss=sp&ids="+idProximoEspaco);
                    } else {// Se não existe
                        window.location.assign("home.php?ss=ns");
                    }
                }
            }
        };        
        formCheckOut = new FormData(); // Cria um objeto do tipo formulário com codificação multipart/form-data (permite enviar arquivos)
        formCheckOut.append('idUsuario',idUsuario);// Adiciona a variável 'idUsuario' como se um campo type=text (nesse caso) tivesse sido preenchido com a variável
        formCheckOut.append('idEspaco',idEspaco);
        checkOutPostman.open("POST", '../config/ajax/userSpaceCheckout.php', true); // Chama o script para tratar os dados do formulário
        checkOutPostman.send(formCheckOut); // Equivalente a clicar em um submit e enviar o formulário
    }
    // Função para mostrar a div com informações sobre o espaço
    function mostrarInfoEspaco(){
        document.getElementById("espaco_info").classList.toggle("espaco_mostrar_info");
        if(document.getElementById("mostrar_info_link").innerHTML === "mais"){
            document.getElementById("mostrar_info_link").innerHTML = "menos";
        } else {
            document.getElementById("mostrar_info_link").innerHTML = "mais";
        }
    }
    // Função para buscar e mostrar os usuários no espaço
    function listarUsuarios(){
        document.getElementById("area_input").style.display = 'none';
        document.getElementById("mensagens_container").style.display = 'none';
        document.getElementById("lista_usuarios_container").style.display = 'block';
    }
    // Função para ocultar a lista de usuários e exibir a conversa
    function ocultarListaUsuarios(){
        document.getElementById("lista_usuarios_container").style.display = 'none';
        document.getElementById("mensagens_container").style.display = 'block';
        document.getElementById("area_input").style.display = 'block';
    }
</script>