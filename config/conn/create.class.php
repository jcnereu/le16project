<?php
/**
 * Description of create:
 * Executa uma inserção no banco de dados
 * @author JoaoCarlosNereu
 */
class create extends conn{
    
    private $tabela;
    private $dados;
    private $query;
    private $conexao;
    private $statement;
    private $resultado;
    
    /**
     * <b>fezerInsercao</b> Executa uma inserção no banco de dados.
     * O bind value é feito internamente
     * Basta informar o nome da tabela e um array associativo com nome da coluna e o valor.
     * @param STRING $tabela = Informe o nome da tabela no banco.
     * @param array $dados = Array associativo $senha = array("nome"=>"João","senha"=>"123")
     */
    public function fazerInsercao($tabela, array $dados){
        $this->tabela = (string) $tabela;
        $this->dados = $dados;
        $this->executarInsercao();
    }
    
    /**
     * <b>retornaIDinserido</b> Retorna o último ID inserido na tabela caso a inserção tenha sido feita com sucesso.
     * @return INT último ID inserido na tabela.
     */
    public function retornaIDinserido() {
        return $this->resultado;
    }
    
    /**
     * <b>retornaResultado</b> Retorna TRUE ou FALSE
     * @return Bool : TRUE se a inserção foi bem sicedida ou FALSE caso contrário
     */
    public function retornaResultado() {
        if($this->resultado!=0){
            return true;
        } else {
            return false;
        }
    }
    
    // MÉTODOS PRIVADOS
    
    private function montarQuery() {
        $colunas = implode(', ', array_keys($this->dados));//String: Pega os campos do array Dados e separa com ' ,'
        $valores = ':'.implode(', :', array_keys($this->dados));//String: Pega os campos do array Dados e separa com ', :'
        $this->query = "INSERT INTO {$this->tabela} ({$colunas}) VALUES ({$valores})";
    }
    
    private function criarStatement() {
        $this->conexao = parent::pegarConexao();//Conexão para criar a Statement (Query) através do método prepare
        $this->statement=$this->conexao->prepare($this->query);
    }
    
    private function executarInsercao() {
        $this->montarQuery();
        $this->criarStatement();
        try{
            $this->statement->execute($this->dados);
            /*
             * Quando a Query é monatada com os nomes das colunas (campos) de inserção iguais aos :bindValues
             * o método execute relaciona automaticamente os :bindValues com os campos do array informado
             */ 
            $this->resultado = $this->conexao->lastInsertId();
        } catch (PDOException $ex) {
            $this->resultado = 0;
            msgSistema("<b>Erro ao inserir no banco de dados</b>:{$ex->getMessage()}", $ex->getCode());
            die;
        }
    }
}
