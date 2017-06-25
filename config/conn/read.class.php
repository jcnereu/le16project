<?php
/**
 * Description of read:
 * Executa uma leitura simples no banco de dados 'teste'
 * Ver definições do banco em 'config/loadConn.inc.php'
 * @author JoaoCarlosNereu
 */
class read extends conn{
    
    private $bindValues;
    private $query;
    private $conexao;
    private $statement;
    private $resultado;

    /**
     * <b>fazerBusca</b> Executa uma leitura simples no banco de dados. Não retorna nada.
     * Para pegar o resultado utilizar o méto RetornaResultado()
     * @param String $query : A query completa ex:
     * SELECT * FROM tabela WHERE nome = :bv1 AND senha = :bv2
     * @param String $bindValues : Parâmetros da busca, ex: "bv1=Joao&bv2=123"
     * IMPORTANTE: Os parâmetros SQL 'LIMIT' E 'OFFSET' devem ser informados com os bind values :limit e :offset 
     */
    public function fazerBusca($query, $bindValues=null){
        //Converte a string recebida num array com os dados para leitura
        if(!empty($bindValues)):
            parse_str($bindValues, $this->bindValues);
        endif;
        // Recebendo a string da Query
        $this->query = $query;
        $this->executarLeitura();
    }
    
    /**
     * <b>retornaResultado</b> Retorna os dados encontados na tabela caso a leitura seja efetuada com sucesso.
     * @return Array com os dados encontrados pela busca (Array de arrays).
     */
    public function retornaResultado() {
        return $this->resultado;
    }
    
    /**
     * <b>contaResultados</b> Retorna quantas linhas (resultados) foram encontradas pela busca.
     * @return Int Quantidade de linhas (resultados) encontradas
     */
    public function contaResultados() {
        return $this->statement->rowCount();// É com o statement mesmo
    }
    
    // MÉTODOS PRIVADOS
    
     private function criarStatement() {
        $this->conexao = parent::pegarConexao();
        $this->statement = $this->conexao->prepare($this->query);
        // Configura o modo como a leitura é retornada (array)
        $this->statement->setFetchMode(PDO::FETCH_ASSOC);
     }
     
     private function preencherStatement() {
        if($this->bindValues):
            foreach ($this->bindValues as $campo => $valor):
                // Se o parâmetro informado representar as propriedades LIMIT ou OFFSET
                if($campo=='limit' || $campo=='offset'):
                    $valor = (int) $valor;
                endif;
                $this->statement->bindValue(":{$campo}", $valor, (is_int($valor)?PDO::PARAM_INT:PDO::PARAM_STR));
                // método bindValue(nome do campo, valor a ser lido, tipo de dado do valor(int ou string))
            endforeach;
        endif;
    }
    
    private function executarLeitura() {
        $this->criarStatement();
        $this->preencherStatement();
        try{
            $this->statement->execute();
            // A variável $this->resultado armazena um array (FETCH_ASSOC) com os resultados
            $this->resultado = $this->statement->fetchAll();
        } catch (PDOException $ex) {
            $this->resultado = null;
            msgSistema("<b>Erro ao fazer a leitura no banco de dados</b>:{$ex->getMessage()}", $ex->getCode());
            die;
        }
    }
    
}
