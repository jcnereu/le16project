<?php
/**
 * Description of conn:
 * Cria e retorna um objeto PDO para interação com o banco de dados
 * @author JoaoCarlosNereu
 */
class conn {
    
    private static $dsn = DB1_DSN; // Já inclui o nome do danco de dados. O nome do banco de dados pode ser alterado no app.yaml
    private static $user = DB1_USER;
    private static $password = DB1_PASSWORD;
    private static $conexao = NULL;
    
    // Função para criar a conexão
    private static function conectar(){
        try {
            if(self::$conexao==NULL){ // SingleTon: Apenas um objeto dessa classe intanciado na memória do servidor
                $options = [PDO::MYSQL_ATTR_INIT_COMMAND=>'SET NAMES UTF8'];//Configurando o banco com UTF8
                self::$conexao = new PDO(self::$dsn, self::$user, self::$password, $options);
            }
        } catch (PDOException $ex) {
            /* 
             * O código abaixo deve ser incorporado em uma classe específica para mensagens do sistema
             * caso esse procedimento seja repetido em outro lugar.
             * Ver exemplo em Config.in.php no projeto le16off (método 'PHPErro')
             */
            $tipo = $ex->getCode();
            $msg = $ex->getMessage();
            $arquivo = $ex->getFile();
            $linha = $ex->getLine();
            $classeCss=($tipo==E_USER_NOTICE?MSG_INFOR:($tipo==E_USER_WARNING?MSG_ALERT:($tipo==E_USER_ERROR?MSG_ERROR:$tipo)));
            echo"<p class=\"trigger {$classeCss}\">";
            echo"<b>Erro na linha {$linha}:</b> {$msg}<br>";
            echo"<small>Arquivo: {$arquivo}</small>";
            echo"<span class=\"ajax.close\"></span></p>";
            // As contantes em $classeCss (MSG_INFOR, MSG_ALERT, MSG_ERROR) são definidas no app.yaml e especificadas no geral.css
            die();
        }
        // Configuração do tipo de erro que o PDO vai trabalhar: Lançamento de excessões
        self::$conexao->setAttribute(PDO::ATTR_ERRMODE,  PDO::ERRMODE_EXCEPTION);
        return self::$conexao;
    }
    
    // Função para pegar a conexão
    public static function pegarConexao(){
        return self::conectar();//return self::pegarConexao(); gera um bug cabuloso de overmemory.
    }
}
