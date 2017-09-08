<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>le16</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link type="text/css" rel="stylesheet" href="stylesheets/reset.css">
        <link type="text/css" rel="stylesheet" href="stylesheets/home.css">
    </head>
    <body>
        <h2>Ops! Sua sessão expirou...</h2>
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
            function le16wayout() {
                // Função para verificar se o Firebase está configurado corretamente
                this.checkSetup();
                // Função para fazer algumas configuralções iniciais
                this.initFirebase();
            }
            // Sets up shortcuts to Firebase features and initiate firebase auth.
            le16wayout.prototype.initFirebase = function() {
                // Shortcuts to Firebase SDK features.
                this.auth = firebase.auth();
                // Initiates Firebase auth and listen to auth state changes.
                this.auth.onAuthStateChanged(this.onAuthStateChanged.bind(this));
            }; 
            // Triggers when the auth state change for instance when the user signs-in or signs-out.
            le16wayout.prototype.onAuthStateChanged = function(user) { 
                if (user) { // User is signed in!
                    // Sign out of Firebase.
                    this.auth.signOut();
                    //redireciona para a home
                    window.location.assign("../index.php");
                } else { // User is signed out!
                    //redireciona para a home
                    window.location.assign("../index.php");
                }
            };
            // Função necessária na fase de desenvolvimento
            // Checks that the Firebase SDK has been correctly setup and configured.
            le16wayout.prototype.checkSetup = function() {
                if (!window.firebase || !(firebase.app instanceof Function) || !firebase.app().options) {
                    window.alert('You have not configured and imported the Firebase SDK. ' +
                        'Make sure you go through the codelab setup instructions and make ' +
                        'sure you are running the codelab using `firebase serve`');
                }
            };
            // Chamando a função geral (que gerencia as específicas) ao carregar a página
            window.onload = function() {
                // Carregando as funções do Firebase
                window.le16wayout = new le16wayout();
            };
        </script>
    </body>
</html>
