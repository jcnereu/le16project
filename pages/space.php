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

<!-- Subpágina carregada na home -->
<div class="espaco_cabecalio">
    <!-- ################################## CABEÇÁLIO DO ESPAÇO #################################### -->
    <div class="espaco_cabecalio_menu">
        <!-- O id do espaço é atribuido ao carregar a home, por leitura direta da URL -->
        <span onclick="sairEspaco(<?php echo $idEspacoUrl; ?>);" class="espaco_cabecalio_sair">sair</span>
        
        <!-- #################################### OPÇÕES BTN ####################################### -->
        <label title="Opções" onclick="mostrarOpcoes();">
            <div class="opcoes_btn" id="opcoes_btn">&#x22EF;</div>
        </label>
        
    </div>
    
    <!-- ################################### NOME DO ESPAÇO ######################################## -->
    <!-- Nome do espaço, atribuido no script de validação de acesso ao espaço no início da home -->
    <p class="titulo" id="nome_espaco" onclick="mostrarInfoEspaco();"><?php echo $nomeEspaco; ?></p>
    
    <!-- ############################ ÍCONE DE VISIBILIDADE DO ESPAÇO ############################## -->
    <?php
        // Verificando o estado de visibilidade do espaço
        $displayIconeEspacoInvisivel = ($visibilidade=='yes') ? 'none' : 'block';
    ?>
    <div class="icone_espaco_invisivel" style="display: <?php echo $displayIconeEspacoInvisivel; ?>;"></div>
    
    <!-- ######################################## CATRACA ########################################## -->
    <div class="catraca_container_geral">
        <!-- ENTRADA -->
        <div class="catraca_container" id="catraca_container">
            <div class="catraca_subcontainer">
                <div class="pic" id="catraca_user_pic"></div>
                <div class="nome" id="catraca_user_name"></div>
                <div class="sentido" style="color: #11c140;">entrou</div>
            </div>
        </div>
        <!-- SAÍDA -->
        <div class="catraca_container" id="catraca_saida_container">
            <div class="catraca_subcontainer">
                <div class="pic" id="catraca_saida_user_pic"></div>
                <div class="nome" id="catraca_saida_user_name"></div>
                <div class="sentido">saiu</div>
            </div>
        </div>
    </div>
</div>

<!-- ############################## ÁREA DE EXIBIÇÃO DA CONVERSA ################################### -->
<div class="espaco_mensagens_container" id="messages"></div>

<!-- ########################## AREA PARA DIGITAR E ENVIAR MENSAGNES ############################### -->
<div class="espaco_area_texto" id="area_input">
    <form id="message-form" action="#">
        <input type="text" id="message-text" class="area_texto_item">
        <input type="submit" id="message-submit" value="" class="area_texto_item">
    </form>
    <!-- Envio de imagens (temporáriamente removido)
    <form id="image-form" action="#">
        <input id="media-capture" type="file" accept="image/*,capture=camera" style="display: none;">
        <input type="submit" id="image-submit" value="Img" class="area_texto_item">
    </form>
    -->
</div>

<script>
    // ****************************** INÍCIO: FUNÇÕES FIREBASE ****************************************
    
    // O Firebase já é inicializado na userBar. Aqui vão apenas as funções para tratar as mensagens e as informações
    // do espaço, que dependem de elementos que só existem quando um espaço está aberto.
     
    function le16space() {
        
        // Shortcut to Firebase SDK feature.
        this.database = firebase.database();
        
        //***************************** CARREGANDO INFORMAÇÕES NO CABEÇALIO DO ESPAÇO ***********************************
        
        // 1) Data de criação do espaço (No fusorário do usuário/browser)
        
        // Transferido para o método mostarInfoEspaco()
        
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
        // DDDDDDDDDD var idEspaco = document.getElementById('id_invisivel_espaco').value;
        // Número de usuários ao carregar a página (servidor)
        // DDDDDDDDDD var nUsuarios = document.getElementById('numero_usuarios_inicial').value;
        // Inicializando a div com o número de usuários
        // DDDDDDDDDD document.getElementById('numero_usuarios').innerHTML = nUsuarios;
        // Referência com o ID do espaço em um child exclusivo (spaces) para listar os espaços abertos no Firebase DB
        // DDDDDDDDDD var spaceRef = this.database.ref('spaces/space-'+idEspaco);
        // Para remover qualquer referência anterior
        // DDDDDDDDDD spaceRef.off();
        // Contador do listener de adição (Variável adotada para que o número de usuários mostrado não dependa do listener ao recarregar a página)
        // DDDDDDDDDD var contAdd = 0;
        // Listener para atualizar (somar 1) o número de usuários se algum usuário ENTRAR no espaço
        // DDDDDDDDDD spaceRef.on('child_added', function(data) { // Callback simples
            /*
            contAdd = contAdd + 1;
            if (contAdd>nUsuarios) {
                // Atualizando o contador de usuários
                nUsuarios = nUsuarios - (-1);
                document.getElementById('numero_usuarios').innerHTML = nUsuarios;
                console.log('nUsuarios in = '+nUsuarios);
                // Paranauês para mostrar o novo usuário com fade-out (Catraca)
                document.getElementById("catraca_container").style.display = 'none';
                document.getElementById("catraca_container").style.opacity = "1";
                document.getElementById("catraca_user_pic").style.backgroundImage = 'url(' + data.val().userPhotoUrl + ')';
                document.getElementById("catraca_user_name").innerHTML = data.val().userName;
                document.getElementById("catraca_container").style.display = 'block';
                setTimeout(function(){
                    document.getElementById("catraca_container").style.opacity = "0";
                }, 2000); // Tempo de exbição da notificação de entrada
                
            }
            */
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
        //});// Não há que fazer se der errado
        // Listener para atualizar (subtrair 1) o número de usuários se algum usuário SAIR do espaço
        /*
        spaceRef.on('child_removed', function(data) { // Callback simples
            console.log('child_removed OK');
            contAdd = contAdd - 1;
            nUsuarios = nUsuarios - 1;
            document.getElementById("numero_usuarios").innerHTML = nUsuarios;
            console.log('nUsuarios out = '+nUsuarios);
            // Informar quem saiu? (usar as informações da variável "data" passada no callBack)
        });// Não há que fazer se der errado
        */
        
        // 4) Exibindo o botão para alterar as opções do espaço, caso o usuário seja o criador
        if (document.getElementById("fbid_invisivel_usuario").value === userCreatorId) {
            document.getElementById("opcoes_btn").style.display = 'block';
        }
        //*******************************************************************************************************************
        
        // Shortcuts to DOM Elements.
        this.messageList = document.getElementById('messages'); // Div onde são exibidas as mensagens
        this.messageForm = document.getElementById('message-form'); // Formulário para escrver e enviar mensagens
        this.messageInput = document.getElementById('message-text'); // Campo de texto onde são escritas as mensagens
        this.submitButton = document.getElementById('message-submit'); // Botão para enviar as mensagens
        //this.submitImageButton = document.getElementById('image-submit'); // Botão para enviar Imagens
        //this.imageForm = document.getElementById('image-form'); // Envio de imagens desabilitado
        //this.mediaCapture = document.getElementById('media-capture'); // Envio de imagens desabilitado
        //this.numeroUsuarios = document.getElementById('div_geral_numero_usuarios'); // Envio de imagens desabilitado
        this.listaUsuarios = document.getElementById('container_lista_usuarios');
        this.nomeEspaco = document.getElementById('nome_espaco');
        
        // Eventlisteners
        this.messageForm.addEventListener('submit', this.saveMessage.bind(this));
        this.nomeEspaco.addEventListener('click', this.loadUsers.bind(this)); // Sem o ".bind(this)" o this.database e a função displayUserRow() não são reconhecidos
        
        // Events for image upload. (TEMPORARIAMENTE REMOVIDO)
        /*
        this.submitImageButton.addEventListener('click', function(e) {
            e.preventDefault();
            this.mediaCapture.click();
        }.bind(this));
        this.mediaCapture.addEventListener('change', this.saveImageMessage.bind(this));
        */
        
        // Carrega as 20 últimas mensagens que (se) que estiverem registradas na referencia firebase do espaço
        this.loadMessages();
        
    }
      
    // Template para as mensagens do usuário
    MESSAGE_TEMPLATE_USER =
        '<div class="user_message_container">' +
            '<div class="cabecalio">' +
                '<div class="horario"></div>' +
            '</div>'+
            '<div class="texto"></div>' +
        '</div>';      
    // Template para as mensagens dos outros participantes do grupo
    MESSAGE_TEMPLATE_OTHERS =
        '<div class="other_user_message_container">' +
            '<div class="cabecalio">' +
                '<div class="nome"></div>' +
                '<div class="separador">&bullet;</div>' +
                '<div class="horario"></div>' +
            '</div>' +
            '<div class="texto"></div>' +
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
            this.displayMessage(data.key, val.uid, val.name, val.text, val.time, val.photoUrl, val.imageUrl);
        }.bind(this);
        this.messagesRef.limitToLast(20).on('child_added', setMessage); // Não há que fazer se der errado (enviar de novo)
        this.messagesRef.limitToLast(20).on('child_changed', setMessage); // Não há que fazer se der errado (enviar de novo)
    };
    // Displays a Message in the UI.
    le16space.prototype.displayMessage = function(key, uid, name, text, time, picUrl, imageUri) {
        // Pegando o fbid do usuário num campo invisível na userBar()
        var fbidUsuario = document.getElementById("fbid_invisivel_usuario").value; 
        // Pegando o elemento da mensagem (cada mensagme tem uma chave única) se ela já existir
        var msgDiv = document.getElementById(key);
        // If an element for that message does not exists yet we create it.
        if (!msgDiv) {
            // Se o usuário é dono da mensagem
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
                // Adicionando o nome registrado na mensagem
                msgDiv.querySelector('.nome').textContent = name;
            }
        }
        // Se a mensagem tem uma imagem de perfil registrada (REMOVIDO)
        //if (picUrl) {
            //msgDiv.querySelector('.pic').style.backgroundImage = 'url(' + picUrl + ')';
        //}
        // Adicionando o horário de envio da mensagem
        if (time) { // Apenas por precaução, pois o horário sempre é salvo
            var dataEnvio = new Date(time);
            var horas = dataEnvio.getHours();
            var minutos = dataEnvio.getMinutes();
            var strHorario = horas + ':' + minutos;
            msgDiv.querySelector('.horario').textContent = strHorario;
        }
        // Adicionando o texto ou a imagem enviada
        var messageElement = msgDiv.querySelector('.texto');
        if (text) { // If the message is text.
            messageElement.textContent = text;
            // Replace all line breaks by <br>.
            messageElement.innerHTML = messageElement.innerHTML.replace(/\n/g, '<br>');
            /*
             * Acrescentar algoritmo para quebrar palavras (qualquer conjunto de letras) grandes 
             * que possam ultrapassar o conatiner da mensagem (100 letras escritas juntas são exibidas juntas)
             * SOLUÇÃO PROVISÓRIA:
             * Foi acrescentado o css-overflow: hidden; nas divs das mensagens, para esconder a parte da string
             * que escede o max-width. Não palavras com mais de 22 letras em português, portanto isso não afeta a comunicação
             * Essa é uma medida exemplar: Maximizar os bons usuários e minimizar o porcos
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
        // Show the card fading-in. (O timeout aqui parece sem qualquer utilidade (herança codelab))
        setTimeout(function() { msgDiv.style.opacity = "1"; }, 1);
        this.messageList.scrollTop = this.messageList.scrollHeight;
        this.messageInput.focus();
    };
    // Função chamada quando o botão enviar é clicado. Saves a new message on the Firebase DB.
    le16space.prototype.saveMessage = function(e) {
        e.preventDefault();
        // Check that the user entered a message.
        if (this.messageInput.value) {
            var currentUser = firebase.auth().currentUser;
            // Pegando o timeStamp no instante
            var date = new Date();
            var ts = date.getTime();
            // Add a new message entry to the Firebase Database.
            this.messagesRef.push({ // O método push cria uma chave única automaticamente
                uid: currentUser.uid,
                name: currentUser.displayName,
                text: this.messageInput.value,
                time: ts
                //photoUrl: currentUser.photoURL || 'backgrounds/profile_placeholder.png' (REMOVIDO)
                /*
                 * Adicionar o horário de envio
                 */
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
    
    // Função chamada quando o botão enviar Imagem é clicado (Não utilizada por enquanto)
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
            imageUrl: LOADING_IMAGE_URL
            //photoUrl: currentUser.photoURL || 'backgrounds/profile_placeholder.png'
            // Adicionar a data
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
    
    // Função chamada ao clicar no nome do espaço ativo
    le16space.prototype.loadUsers = function() {
        // Pegando o id do espçao em um campo invível no ínico da página, criado ao exibir o espaço
        var spaceId = document.getElementById('id_invisivel_espaco').value;
        // Pegando referência do espaço no firebase DB
        this.userListRef = this.database.ref('spaces/space-'+spaceId);
        // Make sure we remove all previous listeners.
        //this.userListRef.off(); // spaceRef DDDDDDDDDDDDDDDDDDDD TEMP
        // LLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLL
        // Número de usuários ao carregar a página (servidor)
        var nUsuarios = document.getElementById('numero_usuarios_inicial').value;
        // Inicializando a div com o número de usuários
        document.getElementById('numero_usuarios').innerHTML = nUsuarios;
        // spaceRef = this.userListRef;
        // Contador do listener de adição (Variável adotada para que o número de usuários mostrado não dependa do listener ao recarregar a página)
        var contAdd = 0;
        // LLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLL
        // Carregando todos os usuários registrados no espaço
        var setUserRow = function(data) {
            // LLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLL
            contAdd = contAdd + 1;
            if (contAdd>nUsuarios) {
                // Atualizando o contador de usuários
                nUsuarios = nUsuarios - (-1);
                document.getElementById('numero_usuarios').innerHTML = nUsuarios;
                // Paranauês para mostrar o novo usuário com fade-out (Catraca)
                //document.getElementById("catraca_container").style.display = 'none';
                document.getElementById("catraca_container").style.opacity = "1";
                document.getElementById("catraca_user_pic").style.backgroundImage = 'url(' + data.val().userPhotoUrl + ')';
                document.getElementById("catraca_user_name").innerHTML = data.val().userName;
                document.getElementById("catraca_container").style.display = 'block';
                setTimeout(function(){
                    document.getElementById("catraca_container").style.opacity = "0";
                    document.getElementById("catraca_container").style.display = 'none';
                }, 2000); // Tempo de exbição da notificação de entrada
                
            }
            // LLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLL
            // DDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDD
            this.database.ref('users/'+data.key).once('value').then( function(snapshot) {
                var userData = snapshot.val();
                // Pegando o elemento da mensagem (cada mensagem tem uma chave única) se ela já existir
                var rowDiv = document.getElementById(snapshot.key);
                // If an element for that message does not exists yet we create it.
                if (!rowDiv) {
                    var container = document.createElement("div");
                    container.innerHTML = USER_ROW_TEMPLATE;
                    rowDiv = container.firstChild;
                    rowDiv.setAttribute("id", snapshot.key);
                    // Acrescentando a função para mostrar e preencher as infos do convite no botão (icone) do convite
                    rowDiv.querySelector('.invite_btn').setAttribute("onclick", 'mostrarConvite("'+snapshot.key+'","'+userData.userName+'");');
                    // key = fbuid, name = name
                    // Acrescentando a função para mostrar a imagem de perfil, o nome e a msg de status
                    rowDiv.querySelector('.label_pic').setAttribute("onclick", 'ampliarImgPerfil("'+userData.userPhotoUrl+'","'+userData.userName+'","'+userData.userMsgStatus+'");');
                    var listaContainer = document.getElementById('container_lista_usuarios');
                    listaContainer.appendChild(rowDiv);
                }
                // Se o usuário tem uma imagem de perfil
                if (userData.userPhotoUrl) {
                    rowDiv.querySelector('.pic').style.backgroundImage = 'url(' + userData.userPhotoUrl + ')';
                }
                // Adicionando o nome do usuário
                rowDiv.querySelector('.name').textContent = userData.userName;
                // Adicionando o texto de status do usuário
                rowDiv.querySelector('.msgStatus').textContent = userData.userMsgStatus;
            }).catch( function(error) { console.error('Dev Msg: Erro ao ler os dados do usuário.',error); });
            // DDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDD
        }.bind(this);
        this.userListRef.on('child_added', setUserRow); // Não há o que fazer se der errado (F5)
        // LLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLL
        // Carregando todos os usuários registrados no espaço
        var removeUserRow = function(data) {
            // Atualizando o número de usuários
            contAdd = contAdd - 1;
            nUsuarios = nUsuarios - 1;
            document.getElementById("numero_usuarios").innerHTML = nUsuarios;
            
            // Paranauês para informar o usuário que saiu com fade-out (Catraca)
            // document.getElementById("catraca_saida_container").style.display = 'none';
            document.getElementById("catraca_saida_container").style.opacity = "1";
            document.getElementById("catraca_saida_user_pic").style.backgroundImage = 'url(' + data.val().userPhotoUrl + ')';
            document.getElementById("catraca_saida_user_name").innerHTML = data.val().userName;
            document.getElementById("catraca_saida_container").style.display = 'block';
            setTimeout(function(){
                document.getElementById("catraca_saida_container").style.opacity = "0";
                document.getElementById("catraca_saida_container").style.display = 'none';
            }, 2000); // Tempo de exbição da notificação de entrada
            
            // Removendo a linha do usuário na lista
            var divToRemove = document.getElementById(data.key);
            divToRemove.parentNode.removeChild(divToRemove); // Long live W3 school
            
        }.bind(this);
        this.userListRef.on('child_removed', removeUserRow);
        // LLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLL
        // INCLUIR O LISTENER DE REMOVE AQUI, COM A ATUALIZAÇÃO DO NÚMERO DE USUÁRIOS E A CATRACA DE SAÍDA
    };
    // Template para a linha do usuário na lista
    USER_ROW_TEMPLATE =
        '<div class="item_lista_usuario">' +
            // O atributo "value" é iniciado vazio e preenchido quando o usuário clica no icone do convite
            '<input type="text" value="Bug" id="fbuid_invisivel_destino_convite" style="display: none;">' +
            '<label title="Ampliar" class="label_pic"><div class="pic"></div></label>' +
            '<div class="name"></div>' +
            '<div class="msgStatus"></div>' +
            // Nessa div é acrescentado o JS para exibir o formulário do convite
            '<div class="invite_btn"><div class="invite_icon"></div></div>' +
        '</div>';
    
    // Mostra a linha com as informações básicas do usuário. (NÃO UTILIZADA)
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
            // Acrescentando a função para mostrar a imagem de perfil, o nome e a msg de status
            rowDiv.querySelector('.label_pic').setAttribute("onclick", 'ampliarImgPerfil("'+picUrl+'","'+name+'","'+msgStatus+'");');
            this.listaUsuarios.appendChild(rowDiv);            
        }
        // Se o usuário tem uma imagem de perfil
        if (picUrl) {
            rowDiv.querySelector('.pic').style.backgroundImage = 'url(' + picUrl + ')';
        }
        // Adicionando o nome do usuário
        rowDiv.querySelector('.name').textContent = name;
        // Adicionando o texto de status do usuário
        rowDiv.querySelector('.msgStatus').textContent = msgStatus;
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
    // Função para mostrar a div com a lista de usuários no espaço
    function mostrarInfoEspaco() {
        // DDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDD
        
        // Mostrando a quanto tempo o espaço foi criado

        //var userTimeOffset = new Date().getTimezoneOffset(); NÃO É PRECISO, POIS OS MÉTODOS getDate()... DESCONTAM O OFFSET DA TIMEZONE DO USUÁRIO AUTOMATICAMENTE
        var utcTimeCreation = new Date(1000*(document.getElementById("data_criacao-invisivel").value));
        // Multiplica-se por 1000, pois o timeStamp do PHP é em segundos e do JS é em mili segundos
        var year = utcTimeCreation.getFullYear();
        var month = utcTimeCreation.getMonth() + 1; // ?
        var day = utcTimeCreation.getDate();
        var hour = utcTimeCreation.getHours();
        var min = utcTimeCreation.getMinutes();
        min = (min<10) ? '0'+min : min; // ... às 10:08
        // Formatando a data de criação
        var timeStampCreation = document.getElementById("data_criacao-invisivel").value;
        var dummyDate = new Date(); // Não pode ser mais utilizado depois do setHours()
        var msDesdeMeiaNoite = dummyDate.getTime() - dummyDate.setHours(0,0,0,0); // JS em milisegundos / PHP em segundos
        var msDesdeMeiaNoiteOntem = msDesdeMeiaNoite + 86400000;
        var dateNow = new Date();
        var difTimeStamp = dateNow.getTime() - (timeStampCreation*1000); // 
        //console.log('timeStamp agora JS = ' + dateNow.getTime());
        //console.log('timeStamp criação PHP*1000 = ' + timeStampCreation*1000);
        var dataCriacao = 'à um tempo atrás...';
        if (difTimeStamp<60000) {
            dataCriacao = 'Agora mesmo';
        } else if (difTimeStamp<3600000) {
            var minutosInt = Math.floor(difTimeStamp/60000);
            var minutosStr = minutosInt.toString();
            dataCriacao = 'à ' + minutosStr + 'min';
        } else if (difTimeStamp<msDesdeMeiaNoite) {
            dataCriacao = 'Hoje às ' + hour + ':' + min;
        } else if (difTimeStamp<msDesdeMeiaNoiteOntem) {
            dataCriacao = 'Ontém às ' + hour + ':' + min;
        } else {
            dataCriacao = 'Em ' + day + '/' + month + '/' + year + ' às ' + hour + ':' + min;
        }
        //var dataCriacao = day+'/'+month+'/'+year+' às '+hour+':'+min; (Forma básica, não apagar)
        document.getElementById("data-criacao").innerHTML = dataCriacao;
        
        // DDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDD
        
        document.getElementById("c2").classList.add('recuo_c2');
        document.getElementById("c3").style.display = 'block';
        document.getElementById("status_edit_container").style.display = 'none';
        document.getElementById("lista_convites").style.display = 'none';
        document.getElementById("opcoes_container").style.display = 'none';
        document.getElementById("info_espaco_container").style.display = 'block';
    };
    // Função para ocultar a lista de usuários e voltar a exibir a conversa
    function ocultarListaUsuarios() {
        document.getElementById("c3").style.display = 'none';
        document.getElementById("c2").classList.remove('recuo_c2');
        document.getElementById("info_espaco_container").style.display = 'none';
    };
    // Função para ocultar a lista de convites e exibir a conversa
    function ocultarListaConvites() {
        document.getElementById("c3").style.display = 'none';
        document.getElementById("c2").classList.remove('recuo_c2');
        document.getElementById("lista_convites").style.display = 'none';
    }
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
            window.alert('O seu convite foi enviado com sucesso.'); // DDDDDDDDDDDDDDDDDDD NOVO
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
    function ampliarImgPerfil(picUrl, name, msgStatus) {
        // Acrescentando a imagem na div
        document.getElementById("img_perfil_ampliada").style.backgroundImage = 'url(' + picUrl + ')';
        // Acrescentando o nome
        document.getElementById("nome_perfil").innerHTML = name;
        // Acrescentando a msg de status
        document.getElementById("msg_status_perfil").innerHTML = msgStatus;
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
    // Para mostrar as opçoes de configuração do espaço
    function mostrarOpcoes() {
        // Vai e vem das divs
        document.getElementById("c2").classList.add('recuo_c2');
        document.getElementById("c3").style.display = 'block';
        document.getElementById("status_edit_container").style.display = 'none';
        document.getElementById("lista_convites").style.display = 'none';
        document.getElementById("info_espaco_container").style.display = 'none';
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
        document.getElementById("c3").style.display = 'none';
        document.getElementById("c2").classList.remove('recuo_c2');
        document.getElementById("opcoes_container").style.display = 'none';
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
        window.location.reload(); // Muito simples
    };
</script>