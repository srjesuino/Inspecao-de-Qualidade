<?php

class DatabaseQuery
{
    // Credenciais de conexão com o banco de dados
    private $servername = "192.168.10.***"; // Endereço do servidor SQL Server
    private $username = "consulta"; // Nome de usuário para autenticação
    private $password = "******"; // Senha para autenticação
    private $dbname = "DADOS"; // Nome do banco de dados
    private $conn; // Variável para armazenar a conexão

    // Construtor da classe - inicializa a conexão com o banco
    public function __construct()
    {
        $connectionOptions = array(
            "Database" => $this->dbname, // Nome do banco de dados
            "Uid" => $this->username, // Usuário
            "PWD" => $this->password // Senha
        );
        $this->conn = sqlsrv_connect($this->servername, $connectionOptions); // Estabelece a conexão

        // Verifica se a conexão falhou e exibe erros
        if ($this->conn === false) {
            die(print_r(sqlsrv_errors(), true));
        }
    }

    // Executa a primeira consulta baseada em um código de barras
    public function executeQuery($var1)
    {
        // Extrai partes específicas do código de barras
        $ZCB_LOTE = substr($var1, 1, 6); // Lote (6 caracteres)
        $ZCB_NUM = substr($var1, 7, 6); // Número da OP (6 caracteres)
        $ZCB_ITEM = substr($var1, 13, 2); // Item (2 caracteres)
        $ZCB_SEQUEN = substr($var1, 15, 3); // Sequência (3 caracteres)
        $ZCB_PARTE = substr($var1, 18, 3); // Parte (3 caracteres)
        $ZHF_OPERAC = substr($var1, 21, 2); // Operação (2 caracteres)

        // Query SQL com joins para buscar dados relacionados
        $sql = "
        SELECT ZCB_LOTE, ZCB_NUM, ZCB_ITEM, ZCB_SEQUEN, ZCB_PARTE, HB_CC, CTT_DESC01, ZCB_ZDESC, ZCB_QUANT, ZHF_OPERAC, ZHF_DESCRI, ZHF_CTRAB, HB_NOME 
        FROM ZCB020 ZCB 
        LEFT JOIN ZHF020 ZHF 
            ON ZHF_FILIAL = ZCB_FILIAL 
            AND ZHF_OP = ZCB_NUM + ZCB_ITEM + ZCB_SEQUEN
            AND ZHF.D_E_L_E_T_ = '' 
        LEFT JOIN SHB020 SHB 
            ON HB_FILIAL = ZHF_FILIAL 
            AND HB_COD = ZHF_CTRAB
            AND SHB.D_E_L_E_T_ = '' 
        LEFT JOIN CTT020 CTT 
            ON CTT_FILIAL = '' 
            AND CTT_CUSTO = ZHF_CC
            AND CTT.D_E_L_E_T_ = ''
        WHERE 
            ZCB_LOTE = ?
            AND ZCB_NUM = ?
            AND ZCB_ITEM = ?
            AND ZCB_SEQUEN = ?
            AND ZCB_PARTE = ?
            AND ZHF_OPERAC = ?
            AND ZCB.D_E_L_E_T_ = ''";
        
        // Parâmetros para a query preparada
        $params = array($ZCB_LOTE, $ZCB_NUM, $ZCB_ITEM, $ZCB_SEQUEN, $ZCB_PARTE, $ZHF_OPERAC);
        $stmt = sqlsrv_query($this->conn, $sql, $params); // Executa a consulta

        // Verifica se a execução falhou
        if ($stmt === false) {
            die(print_r(sqlsrv_errors(), true));
        }

        // Obtém o resultado como array associativo
        $result = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
        if ($result) {
            // Atribui valores do resultado às variáveis
            $HB_CC = $result['HB_CC'];
            $CTT_DESC01 = $result['CTT_DESC01'];
            $ZCB_ZDESC = $result['ZCB_ZDESC'];
            $ZCB_QUANT = $result['ZCB_QUANT'];
            $ZHF_DESCRI = $result['ZHF_DESCRI'];
            $ZHF_CTRAB = $result['ZHF_CTRAB'];
            $HB_NOME = $result['HB_NOME'];
        } else {
            // Define valores como null se não houver resultado
            $ZHF_CTRAB = $HB_NOME = $ZHF_DESCRI = $ZCB_QUANT = $ZCB_ZDESC = $CTT_DESC01 = $HB_CC = null;
        }

        // Retorna um array com todos os dados
        return [
            'ZCB_LOTE' => $ZCB_LOTE,
            'ZCB_NUM' => $ZCB_NUM,
            'ZCB_ITEM' => $ZCB_ITEM,
            'ZCB_SEQUEN' => $ZCB_SEQUEN,
            'ZCB_PARTE' => $ZCB_PARTE,
            'HB_CC' => $HB_CC,
            'CTT_DESC01' => $CTT_DESC01,
            'ZCB_ZDESC' => $ZCB_ZDESC,
            'ZCB_QUANT' => $ZCB_QUANT,
            'ZHF_OPERAC' => $ZHF_OPERAC,
            'ZHF_DESCRI' => $ZHF_DESCRI,
            'ZHF_CTRAB' => $ZHF_CTRAB,
            'HB_NOME' => $HB_NOME
        ];
    }

    // Executa a segunda consulta para buscar recursos associados a um centro de trabalho
    public function executeSecondQuery($ctrab)
    {
        $sql = "
        SELECT H1_CODIGO, H1_DESCRI 
        FROM SH1020 
        WHERE D_E_L_E_T_ = '' AND H1_CTRAB = ?
        ";
        $ctrab = trim($ctrab); // Remove espaços do parâmetro
        $stmt = sqlsrv_query($this->conn, $sql, [$ctrab]); // Executa a consulta

        // Verifica se a execução falhou
        if ($stmt === false) {
            die(print_r(sqlsrv_errors(), true));
        }

        // Armazena os resultados em um array
        $data = [];
        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            $data[] = [
                'H1_CODIGO' => $row['H1_CODIGO'],
                'H1_DESCRI' => $row['H1_DESCRI'],
            ];
        }
        return $data; // Retorna array com recursos
    }

    // Executa a terceira consulta para buscar setores
    public function executeThirdyQuery()
    {
        $sql = "SELECT X5_CHAVE, X5_DESCRI FROM SX5020 WHERE X5_TABELA='ZB' AND D_E_L_E_T_ = '' ORDER BY X5_TABELA, X5_CHAVE";
        $stmt = sqlsrv_query($this->conn, $sql); // Executa a consulta

        $data = [];
        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            $data[] = [
                'X5_CHAVE' => $row['X5_CHAVE'],
                'X5_DESCRI' => $row['X5_DESCRI'],
            ];
        }
        return $data; // Retorna array com setores
    }

    // Executa a quarta consulta para buscar defeitos por setor
    public function executeFourthyQuery($var1)
    {
        $sql = "SELECT ZHN_DEFEIT, ZHN_DESCDE FROM ZHN020 WHERE ZHN_SETOR = ?";
        $stmt = sqlsrv_query($this->conn, $sql, [$var1]); // Executa a consulta com parâmetro

        $data = [];
        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            $data[] = [
                'ZHN_DEFEIT' => $row['ZHN_DEFEIT'],
                'ZHN_DESCDE' => $row['ZHN_DESCDE'],
            ];
        }
        return $data; // Retorna array com defeitos
    }

    // Fecha a conexão com o banco de dados
    public function closeConnection()
    {
        sqlsrv_close($this->conn);
    }
}

// Instancia a classe e executa a consulta de setores
$dbQuery = new DatabaseQuery();
$setores = $dbQuery->executeThirdyQuery();
$dbQuery->closeConnection(); // Fecha a conexão após uso

session_start(); // Inicia a sessão

// Processa requisições POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Atualiza defeitos com base no setor
    if ($_POST['action'] == "atualizardef") {
        $dbQuery = new DatabaseQuery();
        $defeitos = $dbQuery->executeFourthyQuery($_POST['setor']);
        // Converte todos os valores para UTF-8
        array_walk_recursive($defeitos, function (&$item) {
            $item = utf8_encode($item);
        });
        $_SESSION['defeitos'] = $defeitos; // Armazena na sessão
        echo json_encode($_SESSION['defeitos']); // Retorna como JSON
    }

    // Exclui uma inspeção da sessão
    if ($_POST["action"] == "excluirInspecao") {
        if (isset($_POST["indice"]) && isset($_SESSION["ficha"])) {
            $indice = intval($_POST["indice"]); // Converte índice para inteiro

            // Verifica se o índice existe na sessão
            if (array_key_exists($indice, $_SESSION["ficha"])) {
                unset($_SESSION["ficha"][$indice]); // Remove o item
                $_SESSION["ficha"] = array_values($_SESSION["ficha"]); // Reindexa o array
                echo json_encode(["status" => "sucesso"]); // Resposta de sucesso
            } else {
                echo json_encode(["status" => "erro", "mensagem" => "Índice não encontrado."]); // Erro se índice inválido
            }
        } else {
            echo json_encode(["status" => "erro", "mensagem" => "Dados inválidos."]); // Erro se dados ausentes
        }
    }

    // Processa dados do código de barras
    if ($_POST['action'] == 'dados') {
        $var1 = $_POST['codigoBarra']; // Recebe o código de barras
        $dbQuery = new DatabaseQuery();
        if (isset($_POST['operacao'])) {
            $var1 = substr_replace($var1, $_POST['operacao'], -3, 2); // Substitui operação no código
        }
        $result = $dbQuery->executeQuery($var1); // Executa a consulta principal
        if ($result['ZHF_CTRAB'] == null) {
            $erro = "erro";
            echo json_encode($erro); // Retorna erro se centro de trabalho não encontrado
        } else {
            $_SESSION['recursos'] = $dbQuery->executeSecondQuery($result['ZHF_CTRAB']); // Busca recursos
            echo json_encode(['result' => $result, 'recursos' => $_SESSION['recursos']]); // Retorna dados e recursos
        }
    }
}

?>
