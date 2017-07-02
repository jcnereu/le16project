<?php

/// Constantes do banco de dados 1: Instância: le16project-db1, nome do banco: teste
//$stringDSN = getenv('MYSQL_DSN');
//$stringUSER = getenv('MYSQL_USER');
//$stringPASSWORD = getenv('MYSQL_PASSWORD');
define('DB1_DSN','mysql:unix_socket=/cloudsql/le16project:us-central1:le16project-db1;dbname=teste');
define('DB1_USER','root');
define('DB1_PASSWORD','a4010b3010c2010d1');
// Carregando a classe para conexão com o banco de dados
require_once 'conn/conn.class.php';
// Carregando a classe de leitura no banco de dados
require_once 'conn/read.class.php';
// Carregando a classe de inserção no banco de dados
require_once 'conn/create.class.php';
// Carregando a classe de atualização no banco de dados
require_once 'conn/update.class.php';
// Carregando a classe de exclusão no banco de dados
require_once 'conn/delete.class.php';