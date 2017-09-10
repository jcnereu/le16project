<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>le16</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link type="text/css" rel="stylesheet" href="stylesheets/index.css">
    </head>
    <body>
        <div class="coluna_central">
            <div class="container_login_cadastro">
                <div class="subcontainer_login_cadastro">
                    <button id="sign-in">Entrar com o Google</button>
                </div>
            </div>
            <div class="container_form_busca">
                <form method="post">
                    <div class="caixa_texto_busca"><input type="text" name="content"></div>
                </form>
                <div id="msgLogin"></div>
                <br>
                <a href="pages/teste.php">Página de teste</a>
            </div>
            <p class="rodape">LE16 project. Dia 65, 14 skips, working...</p>
        </div>
        <!-- ********************************** Carregando o Firebase ************************************ -->
        <script src="https://www.gstatic.com/firebasejs/4.3.0/firebase.js"></script>
        <script>
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
            function le16index() {
                // Função para verificar se o Firebase está configurado corretamente
                this.checkSetup();
                // Shortcuts to DOM Elements.
                this.signInButton = document.getElementById('sign-in');
                //Event listeners
                this.signInButton.addEventListener('click', this.signIn.bind(this));
                // Função para fazer algumas configuralções iniciais
                this.initFirebase();
            }
            // Sets up shortcuts to Firebase features and initiate firebase auth.
            le16index.prototype.initFirebase = function() {
                // Shortcuts to Firebase SDK features.
                this.auth = firebase.auth();
                this.database = firebase.database();
                this.storage = firebase.storage();
                // Initiates Firebase auth and listen to auth state changes.
                this.auth.onAuthStateChanged(this.onAuthStateChanged.bind(this));
            }; 
            // Função chamada quando o usuário clica em "Entrar com o Google"
            le16index.prototype.signIn = function() {
                // Sign in Firebase using popup auth and Google as the identity provider.
                var provider = new firebase.auth.GoogleAuthProvider();
                //this.auth.signInWithPopup(provider);
                this.auth.signInWithRedirect(provider);
            };
            // Triggers when the auth state change for instance when the user signs-in or signs-out.
            le16index.prototype.onAuthStateChanged = function(user) {
                
                if (user) { // User is signed in!
                    
                    var userFirebaseName = user.displayName;
                    var userFirebaseId = user.uid;
                    // AJAX para verificar se o usuário já é cadastrado (se não é faz o cadastro), criar a sessão (com o identificador do firebase) e redirecionar para a home
                    var loginPostman = new XMLHttpRequest();
                    loginPostman.onreadystatechange = function() {
                        if (this.readyState == 4 && this.status == 200) {
                            // Se o usuário foi encontrado (ou cadastrado com sucesso) e a sessão foi criada
                            //******************************************************************************
                            // Se for o primeiro acesso do usuário redireciorar para uma p[agina de boas vindas
                            // Nesse caso deve vir uma resposta específica do serverside (ex: fisrtTrue)
                            //******************************************************************************
                            if(this.responseText==='true'){
                                //redireciona para a home
                                window.location.assign("pages/home.php");
                            } else {
                                window.alert('Ops! Algo errado no banco de dados. Tente outra vez.');
                            }
                        }
                    };
                    form = new FormData(); // Cria um objeto do tipo formulário com codificação multipart/form-data (permite enviar arquivos)
                    form.append('userFirebaseName',userFirebaseName);// Adiciona a variável 'userFirebaseName' como se um campo type=text (nesse caso) tivesse sido preenchido com a variável
                    form.append('userFirebaseId',userFirebaseId);
                    loginPostman.open("POST", 'config/ajax/registerAndSession.php', true); // Chama o script para tratar os dados do formulário
                    loginPostman.send(form); // Equivalente a clicar em um submit e enviar o formulário
                } else { // User is signed out!
                    window.alert('Você NÃO está logado (mensagem provisória)');
                }
            };
            // Função necessária na fase de desenvolvimento
            // Checks that the Firebase SDK has been correctly setup and configured.
            le16index.prototype.checkSetup = function() {
                if (!window.firebase || !(firebase.app instanceof Function) || !firebase.app().options) {
                    window.alert('You have not configured and imported the Firebase SDK. ' +
                        'Make sure you go through the codelab setup instructions and make ' +
                        'sure you are running the codelab using `firebase serve`');
                }
            };
            // Chamando a função geral (que gerencia as específicas) ao carregar a página
            window.onload = function() {
                // Procurando variáveis na URL
                /*
                var exe;
                var query  = window.location.search.substring(1); //Pega o que estiver escrito na URL a partir de "?"
                var vars = query.split('&');// Separa a string num array onde estiver escrito "&"
                for (var i = 0; i < vars.length; i++) { // Em cada elemento do array
                    var pair = vars[i].split('=');
                    if (decodeURIComponent(pair[0]) === 'exe') { // Se encontrar a variável "exe"
                        exe = decodeURIComponent(pair[1]);
                    }
                    // Acrescentar outras condições para procurar outras variáveis
                }
                // Se encontrou a variável "exe==noSession" (A sessão expirou e o usuário foi redirecionado para a index)
                //if (exe==='noSession') {
                    // Sign out of Firebase.
                    //this.auth.signOut();
                    //window.alert('Ops! A sua sessão expirou.. (mensagem provisória)');
                //}
                */
                // Carregando as funções do Firebase
                window.le16index = new le16index();
            };
        </script>
    </body>
</html>
