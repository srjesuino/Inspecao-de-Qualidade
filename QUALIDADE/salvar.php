<?php
session_start(); // Inicia a sessão para armazenar e acessar dados persistentes entre requisições

// Recebe os dados JSON enviados no corpo da requisição via POST
$json = file_get_contents("php://input");
// Decodifica o JSON em um array associativo PHP
$data = json_decode($json, true);

// Verifica se o JSON foi decodificado corretamente e se contém a chave 'action'
if (!$data || !isset($data["action"])) {
    // Retorna uma resposta de erro em JSON se os dados forem inválidos ou 'action' estiver ausente
    echo json_encode(["status" => "erro", "mensagem" => "Dados inválidos"]);
    exit; // Encerra a execução do script
}

// Verifica se a ação recebida é para adicionar uma inspeção
if ($data["action"] === "adicionarInspecao") {
    // Cria um array com os dados da inspeção, usando valores padrão se os campos não forem enviados
    $inspecao = [
        'setorq' => $data['setorq'] ?? '', // Setor de qualidade, vazio se não informado
        'setornome' => $data['setornome'] ?? '', // Nome do setor, vazio se não informado
        'codigomotivo' => $data['codigomotivo'] ?? '', // Código do motivo, vazio se não informado
        'descricaodefeito' => $data['descricaodefeito'] ?? '', // Descrição do defeito, vazio se não informado
        'qntdncf' => $data['qntdncf'] ?? 0, // Quantidade não conforme, 0 se não informado
        'text-obs' => $data['obs'] ?? '', // Observação, vazio se não informado
        'imagens' => $data['imagens'] ?? [] // Array de imagens em base64, vazio se não informado
    ];

    // Verifica se a sessão 'ficha' ainda não existe e a inicializa como um array vazio
    if (!isset($_SESSION["ficha"])) {
        $_SESSION["ficha"] = [];
    }

    // Adiciona a nova inspeção ao array de inspeções na sessão
    $_SESSION["ficha"][] = $inspecao;

    // Retorna uma resposta de sucesso em JSON com informações adicionais
    echo json_encode([
        "status" => "sucesso", // Indica que a operação foi bem-sucedida
        "mensagem" => "Inspeção salva!", // Mensagem de confirmação
        "total_inspecoes" => count($_SESSION["ficha"]), // Número total de inspeções na sessão
        "ultima_inspecao" => $inspecao // Dados da última inspeção adicionada
    ]);
}
?>