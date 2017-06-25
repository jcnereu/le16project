<?php

/**
 * Description of update:
 * Executa uma atualização simples no banco de dados
 * @author JoaoCarlosNereu
 */
class update extends conn{
    
    private $tabela;
    private $linhas;
    private $colunas;
    private $bindValues;
    private $resultado;
    private $conexao;
    private $query;
    private $statement;
    
    /**
     * <b>fazerAtualizacao</b>Executa uma atualização simples no banco de dados.
     * @param String $tabela : Informe o nome da tabela no banco.
     * @param Array $colunas : Array associativo para indicar quais colunas terão itens aluatizados
     *                         ex: array('nome'=>'dummy','senha'=>'dummy')
     *                           : Apenas os indices importam, os dummys não serão utilizados
     * @param String $linhas : String em formato de array para indicar as linhas que devem ser atualizadas
     *                         ex: "id={$This->ID}" ou "cidade={$This->cidade}"
     * @param Array $bindValues   : String para informar o novo valor indicado pelo array $DadosDepois
     *                         ex: "nome=$NovoNome&senha=$NovaSenha"
     */
    public function fazerAtualizacao($tabela, array $colunas, $linhas, $bindValues){
        $this->tabela = (String) $tabela;
        $this->colunas = $colunas;
        $this->linhas = (String) $linhas;
        // Coversão da String 'bindValues' no array 'bindValues'.
        parse_str($bindValues, $this->bindValues);
        $this->executarAtualizacao();
    }
    
    /**
     * <b>retornaResultado</b> Retorna um booleano informando se a atualização foi bem sucedida.
     * @return True ou False.
     */
    public function retornaResultado() {
        return $this->resultado;
    }
    
    /**
     * <b>contaResultados</b> Retorna quantas linhas foram alteradas pela atualização.
     * @return Int Quantidade de linhas alteradas
     */
    public function contaResultados() {
        return $this->statement->rowCount();
    }
    
    // MÉTODOS PRIVADOS
    
    private function montarQuery() {
        foreach ($this->colunas as $indice => $valor):
            // Criando um array com elementos no formato [bv1 =: bv1,bv2 =: bv2,...]
            $Array[] = $indice .' =:'.$indice;
            //A variável $Valor não é utilizada (dummy variable), pois precisamos apenas pegar os índices do array
        endforeach;
        // Criando uma string com o formato "bv1 =: bv1,bv2 =: bv2,..."
        $termosComBindValues = implode($Array,', ');
        $this->query = "UPDATE {$this->tabela} SET {$termosComBindValues} WHERE {$this->linhas} ";// O espaço no final é necessário
    }
    
     private function prepararStatement() {
        $this->conexao = parent::pegarConexao();
        $this->statement = $this->conexao->prepare($this->query);
    }
    
    private function executarAtualizacao() {
        $this->montarQuery();
        $this->prepararStatement();
        try {
            $this->statement->execute(array_merge($this->colunas,$this->bindValues));
            //Os arrays $colunas e $bindValues precisam ser mesclados para criar um único array com os links necessários
            $this->resultado = true;
        } catch (PDOException $ex) {
            $this->resultado = false;
            WSErro("<b>Erro ao fazer a atualização no banco de dados</b>:{$ex->getMessage()}", $ex->getCode());
            die;
        }
    } 
}
