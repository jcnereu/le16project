<!-- O script PHP para validar e pegar os dados do espaço estão no começo da home.php, pois precisa usar o header() -->
<!-- O campo abaixo é invisível. Criado apenas para servir o JS para criar a referencia firebase-->
<input type="text" value="<?php echo $idEspacoUrl; ?>" id="id_invisivel_espaco" style="display: none;">
<div class="espaco_container">
    <div class="espaco_cabecalio">
        <div class="espaco_cabecalio_menu">
            <!-- O id do espaço é atribuido ao carregar a home, por leitura direta da URL -->
            <span onclick="sairEspaco(<?php echo $idEspacoUrl; ?>);" class="espaco_cabecalio_sair">sair</span>
        </div>
        <!-- Nome do espaço, atribuido no script de validação de acesso ao espaço no início da home -->
        <p><?php echo $nomeEspaco; ?></p>
    </div>
    <div class="espaco_mensagens_container">
        <div id="messages"></div><!-- *************************** FIREBASE STYLESHEET (codelab.css) -->
    </div>
    <div class="espaco_area_texto">
        <form id="message-form" method="post">
            <input type="text" id="message-text" class="area_texto_item">
            <input type="submit" id="message-submit" value="Env" class="area_texto_item">
            <input type="submit" id="message-image" value="Img" class="area_texto_item">
        </form>
    </div>
</div>
<script>
    // ****************************** INÍCIO: FUNÇÕES FIREBASE ***************************************
    /*
     * O Firebase já é carregado na userbar. Aqui vão apenas as funções para tratar as mensagens
     * que dependem de elementos que só existem quando um espaço está aberto
     */
    function le16space() {
        // Shortcut to Firebase SDK feature.
        this.database = firebase.database();
        
        // Shortcuts to DOM Elements.
        this.messageList = document.getElementById('messages'); // Div onde são exibidas as mensagens
        this.messageForm = document.getElementById('message-form'); // Formulário para escrver e enviar mensagens
        this.messageInput = document.getElementById('message-text'); // Campo de texto onde são escritas as mensagens
        this.submitButton = document.getElementById('message-submit'); // Botão para enviar as mensagens
        
        // Eventlisteners
        this.messageForm.addEventListener('submit', this.saveMessage.bind(this));
        
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
    // A loading image GIF (Ainda não utilizado)
    le16space.LOADING_IMAGE_URL = 'https://www.google.com/images/spin-32.gif';
    // Função chamada ao exibir um espaço
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
    // Sets the URL of the given img element with the URL of the image stored in Cloud Storage.
    le16space.prototype.setImageUrl = function(imageUri, imgElement) {
        imgElement.src = imageUri;

        // TODO(DEVELOPER): If image is on Cloud Storage, fetch image URL and set img element's src.
    };
    // Displays a Message in the UI.
    le16space.prototype.displayMessage = function(key, uid, name, text, picUrl, imageUri) {
        // Criando uma instancia de usuário Firebase para pegar o ID e comparar com ID registrado em cada mensagem
        /*
         * Verificar a possibilidade de instanciar o objeto ao carregar o espçao, para não ter que crirar uma nova instância a cada mensagem
         */
        var currentUser = firebase.auth().currentUser;
        // Pegando o elemnto da mensagem (cada mensagme tem uma chave única) se ela já existir
        var msgDiv = document.getElementById(key);
        // If an element for that message does not exists yet we create it.
        if (!msgDiv) {
            if (uid==currentUser.uid) { // Se o usuário é dono da mensagem
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
    
    // Chamando a função geral (que gerencia as específicas) ao carregar a página (subpágina space.php)
    window.onload = function() {
        window.le16 = new le16(); 
        window.le16space = new le16space();        
    };
    // ******************************** FIM: FUNÇÕES FIREBASE ***************************************
    
    // Se o usuário clicar em sair no canto superio direito do espaço aberto
    function sairEspaco(idEspaco) {
        //Pegando o id do usuário em um campo invisível na barra do usuário;
        var idUsuario = document.getElementById("id_invisivel_usuario").value;
        //Pegando o id do próximo espaço em um campo invisível na home;
        var idProximoEspaco = document.getElementById("id_invisivel_proximo_espaco").value;
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
                } else if (this.responseText==='empty') { // Se era o último usuário do espaço
                    // Alteração Firebase ****************************************************
                    firebase.database().ref('messages/space-'+idEspaco).remove();
                    //************************************************************************
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
</script>