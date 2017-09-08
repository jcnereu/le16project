<!-- O script PHP para validar e pegar os dados do espaço estão no começo da home.php, pois precisa usar o header() -->
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
        <div id="messages"></div><!-- *************************** FIREBASE STYLESHEET -->
    </div>
    <div class="espaco_area_texto">
        <form id="message-form" method="post">
            <input type="text" id="message-text" class="area_texto_item">
            <input type="submit" id="message-submit" value="Env" class="area_texto_item">
            <input type="submit" id="message-image" value="Img" class="area_texto_item">
                <!-- 
                    O PROCESSAMENTO DO BOTÃO ENVIAR É FEITO NO home.php (NO TOPO DO SCRIPT)
                -->
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
        this.messageList = document.getElementById('messages');
        this.messageForm = document.getElementById('message-form');
        this.messageInput = document.getElementById('message-text');
        this.submitButton = document.getElementById('message-submit');
        
        // Eventlisteners
        this.messageForm.addEventListener('submit', this.saveMessage.bind(this));
        
        // Load currently existing chat messages.
        this.loadMessages();
        
        // Criar node com id do espaço e pegar a referencia. Rmover node ao fechar o espaço.
        // Firebase ler e gravar dados web
        // set() e remove();

    }
    
    // Template for messages (Não funciou na forma le16space.MESSAGE_TEMPLATE)
    MESSAGE_TEMPLATE =
        '<div class="message-container">' +
            '<div class="spacing"><div class="pic"></div></div>' +
            '<div class="message"></div>' +
            '<div class="name"></div>' +
        '</div>';
    // A loading image GIF.
    le16space.LOADING_IMAGE_URL = 'https://www.google.com/images/spin-32.gif';
    // Loads chat messages history and listens for upcoming ones.
    le16space.prototype.loadMessages = function() {
        
        // Reference to the /messages/ database path.
        this.messagesRef = this.database.ref('messages'); // PAROU AQUI: INVESTIGAR COMO CRIAR UM BANCO POR ESPAÇO
        // Make sure we remove all previous listeners.
        this.messagesRef.off();
        // Loads the last 12 messages and listen for new ones.
        var setMessage = function(data) {
            var val = data.val();
            this.displayMessage(data.key, val.name, val.text, val.photoUrl, val.imageUrl);
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
    le16space.prototype.displayMessage = function(key, name, text, picUrl, imageUri) {
        var msgDiv = document.getElementById(key);
        // If an element for that message does not exists yet we create it.
        /*
         * Acresecentar o id fairebase do usuário como entrada da função para diferenciar a exibição das
         * mensagens do usuário das restantes. Criar dois TEMPLATES para isso.
         */
        if (!msgDiv) {
            var container = document.createElement("div");
            container.innerHTML = MESSAGE_TEMPLATE;
            msgDiv = container.firstChild;
            msgDiv.setAttribute("id", key);
            this.messageList.appendChild(msgDiv);
        }
        if (picUrl) {
            msgDiv.querySelector('.pic').style.backgroundImage = 'url(' + picUrl + ')';
        }
        msgDiv.querySelector('.name').textContent = name;
        var messageElement = msgDiv.querySelector('.message');
        if (text) { // If the message is text.
            messageElement.textContent = text;
            // Replace all line breaks by <br>.
            messageElement.innerHTML = messageElement.innerHTML.replace(/\n/g, '<br>');
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
        setTimeout(function() {msgDiv.classList.add('visible')}, 1);
        this.messageList.scrollTop = this.messageList.scrollHeight;
        this.messageInput.focus();
    };
    
    // Saves a new message on the Firebase DB.
    le16space.prototype.saveMessage = function(e) {
        e.preventDefault();
        // Check that the user entered a message.
        if (this.messageInput.value) {
            var currentUser = firebase.auth().currentUser;
            // Add a new message entry to the Firebase Database.
            this.messagesRef.push({
                name: currentUser.displayName,
                text: this.messageInput.value,
                photoUrl: currentUser.photoURL || '/backgrounds/profile_placeholder.png'
            }).then(function() {
                // Clear message text field and SEND button state.
                //this.resetMaterialTextfield(this.messageInput);
                this.messageInput.value = '';
                //this.toggleButton();
            }.bind(this)).catch(function(error) {
                console.error('Error writing new message to Firebase Database', error);
            });

            }
    };
    // Resets the given MaterialTextField.
    le16space.prototype.resetMaterialTextfield = function(element) {
        element.value = '';
        //element.parentNode.MaterialTextfield.boundUpdateClassesHandler();
    };
    
    // Chamando a função geral (que gerencia as específicas) ao carregar a página (subpágina space.php)
    window.onload = function() {
        window.le16 = new le16(); 
        window.le16space = new le16space();        
    };
    // ******************************** FIM: FUNÇÕES FIREBASE ***************************************
    
    // Se o usuário clicar em sair no canto superio direito do espaço ativo
    function sairEspaco(idEspaco) {
        //Pegando o id do usuário em um campo invisível na barra do usuário;
        var idUsuario = document.getElementById("id_invisivel_usuario").value;
        //Pegando o id do próximo espaço em um campo invisível na home;
        var idProximoEspaco = document.getElementById("id_invisivel_proximo_espaco").value;
        // AJAX para fechar o espaço
        var checkOutPostman = new XMLHttpRequest();
        checkOutPostman.onreadystatechange = function() {
            if (this.readyState === 4 && this.status === 200) {
                // Recebe a string true manda ver
                if (this.responseText==='true') {              
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