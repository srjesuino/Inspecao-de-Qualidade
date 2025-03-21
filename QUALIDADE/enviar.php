<?php
session_start(); // Inicia a sessão para acessar variáveis de sessão

// Função para enviar dados de inspeção via SOAP
function enviar($cbarras, $operacao, $recurso, $insp, $ref, $cons, $operador)
{
    try {
        // Cria uma instância do cliente SOAP para conectar ao serviço web
        $conn = new SoapClient("http://192.168.10.***:****/ws0201/MADWS006.apw?WSDL");
    } catch (Exception $e) {
        // Encerra a execução e exibe mensagem de erro se a conexão falhar
        die("Erro ao conectar ao serviço: " . $e->getMessage());
    }

    $seqsInsp = []; // Array para armazenar itens de inspeção

    // Verifica se existem inspeções na sessão
    if (isset($_SESSION["ficha"])) {
        foreach ($_SESSION["ficha"] as $inspecao) {
            $imagens = []; // Array para armazenar imagens associadas à inspeção

            // Verifica se há imagens na inspeção
            if (!empty($inspecao['imagens'])) {
                foreach ($inspecao['imagens'] as $imagemBase64) {
                    // Adiciona cada imagem como um item no array de imagens
                    $imagens[] = [
                        "DESCRPICT" => "Imagem de inspeção", // Descrição fixa da imagem
                        "TEXTOPICT" => $imagemBase64 // Conteúdo da imagem em base64
                    ];
                }
            }

            // Estrutura base de um item de inspeção
            $itemInsp = [
                "COD_MOTIVO" => rtrim($inspecao['codigomotivo']), // Código do motivo, sem espaços à direita
                "OBS_ITEM" => $inspecao['text-obs'], // Observação do item
                "QTD_NCONF" => $inspecao['qntdncf'], // Quantidade não conforme
                "SETOR_QUA" => rtrim($inspecao['setorq']), // Setor de qualidade, sem espaços à direita
            ];

            // Adiciona imagens ao item de inspeção, se existirem
            if (!empty($imagens)) {
                $itemInsp["PICTSITM"] = [
                    "PICTITEM" => [
                        "PICTSITEM" => $imagens // Estrutura aninhada para imagens
                    ]
                ];
            }

            $seqsInsp[] = $itemInsp; // Adiciona o item ao array de inspeções
        }
    }

    // Estrutura base dos parâmetros para envio ao serviço SOAP
    $params = [
        "TOKEN" => "_******_", // Token fixo para autenticação
        "DADOSINSP" => [
            "CBARRAS" => $cbarras, // Código de barras
            "OPERACAO" => $operacao, // Operação realizada
            "RECURSO" => $recurso, // Recurso utilizado
            "OBSCABEC" => "TESTE VIA PORTAL", // Observação fixa do cabeçalho
            "INSPETOR" => $_SESSION['cid'], // ID do inspetor da sessão
            "QTD_REFUGO" => $ref, // Quantidade de refugo
            "QTD_CONSERT" => $cons, // Quantidade de conserto
            "QTD_INSPEC" => $insp, // Quantidade inspecionada
            "OPERADOR" => $operador // Nome do operador
        ]
    ];

    // Adiciona itens de inspeção aos parâmetros, se existirem
    if (!empty($seqsInsp)) {
        $params["DADOSINSP"]["SEQSINSP"] = [
            "ITEMINSP" => $seqsInsp // Estrutura aninhada para itens de inspeção
        ];
    }

    try {
        // Chama o método GRVINSP do serviço SOAP com os parâmetros
        $stmt = $conn->GRVINSP($params);
        $result = $stmt; // Armazena o resultado da chamada

        return $result; // Retorna o resultado
    } catch (Exception $e) {
        // Encerra a execução e exibe mensagem de erro se a chamada falhar
        die("Erro ao apontar: " . $e->getMessage());
    }
}

// Verifica se a requisição é do tipo POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Captura os dados enviados via POST
    $codigoBarra = $_POST['codigobarra']; // Código de barras
    $operacao = $_POST['operacao']; // Operação
    $recurso = $_POST['recurso']; // Recurso
    $usuario = $_SESSION['cid']; // ID do usuário da sessão
    $insp = $_POST['insp']; // Quantidade inspecionada
    $refugo = $_POST['refugo']; // Quantidade de refugo
    $cons = $_POST['cons']; // Quantidade de conserto
    $operador = $_POST['operador']; // Nome do operador

    // Chama a função enviar com os dados recebidos
    $result = enviar($codigoBarra, $operacao, $recurso, $insp, $refugo, $cons, $operador);

    // Verifica se o envio foi bem-sucedido e limpa a sessão
    if (isset($result->GRVINSPRESULT) && isset($result->GRVINSPRESULT->RETORNO) && $result->GRVINSPRESULT->RETORNO) {
        unset($_SESSION['ficha']); // Remove os dados de inspeção da sessão
    }

    // Retorna o resultado como JSON
    echo json_encode($result);
}
?>
