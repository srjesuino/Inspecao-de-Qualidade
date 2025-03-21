<?php
require 'consulta.php'; // Inclui arquivo de consultas ao banco de dados

// Verifica se usuário está autenticado via sessão
if (!isset($_SESSION['cid'])) {
    header("Location: ../../index.php"); // Redireciona para login se não autenticado
    exit();
}
?>

<html lang="pt-br">
<head>
    <title>Apontamento de Qualidade</title>
    <meta charset="UTF-8"> <!-- Define codificação de caracteres -->
    <link rel="stylesheet" type="text/css" href="apontarqualidade.css"> <!-- Estilo personalizado -->
    <!-- Importação do Bootstrap via CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Responsividade -->
    <script src="script.js"></script> <!-- Script JavaScript personalizado -->
    <script src="instascan.min.js"></script> <!-- Biblioteca para leitura de QR Code -->
</head>

<body>
    <div class="bloco"> <!-- Container principal -->
        <h1 id="titulo">Apontamento Qualidade</h1> <!-- Título da página -->

        <div id="dadosup"> <!-- Seção superior de dados -->
            <div id="leitor"> 
                <!-- Campo para leitura do QR Code com evento de consulta -->
                <input type="text" id="codigobarra" placeholder="Leia o QrCode" onchange="fazerConsulta()" maxlength="24">
            </div>
            <div id="dados" style="display: none;"> <!-- Container de dados, oculto inicialmente -->
                <div>
                    <h5 id="h5">LOTE:</h5>
                    <label id="ZCB_LOTE"></label> <!-- Exibe número do lote -->
                </div>
                <div>
                    <h5 id="h5">OP - ITEM:</h5>
                    <label id="ZCB_NUM"></label> <!-- Número da OP e item -->
                </div>
                <div>
                    <h5 id="h5">SEQUENCIA:</h5>
                    <label id="ZCB_SEQUEN"></label> <!-- Sequência da operação -->
                </div>
                <div>
                    <h5 id="h5">OPERAÇÃO:</h5>
                    <div style="margin-top: 28px;">
                        <!-- Campo editável para operação com descrição -->
                        <input style="font-size: small;" type="number" id="ZHF_OPERAC" onchange="mudaOperacao()" class="dado"></input>
                        <label id="ZHF_DESCRI"></label>
                    </div>
                </div>
                <div>
                    <h5 id="h5">RECURSO:</h5>
                    <div id="customDropdown"> <!-- Dropdown personalizado para recursos -->
                        <!-- Campo de filtro com eventos de clique e digitação -->
                        <input type="text" id="filterInput" onkeyup="filterFunction()" onclick="toggleDropdown(this)" 
                            placeholder="Digite para filtrar" class="dado">
                        <div id="dropdownOptions" class="dropdown-options">
                            <!-- Loop PHP para listar recursos da sessão -->
                            <?php foreach ($_SESSION['recursos'] as $index => $recurso) { ?>
                                <div class="option" onclick="selectOption(this)" 
                                    data-value="<?php echo htmlspecialchars($recurso['H1_CODIGO']); ?>" 
                                    data-descri="<?php echo htmlspecialchars($recurso['H1_DESCRI']); ?>">
                                    <?php echo ($index + 1) . '. ' . htmlspecialchars($recurso['H1_CODIGO']) . ' - ' . htmlspecialchars($recurso['H1_DESCRI']); ?>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                <div>
                    <h5 id="h5">QUANTIDADES:</h5>
                    <div id="quantidades"> <!-- Seção de entrada de quantidades -->
                        <div class="quantidade">
                            <label style="font-size: 13px;">Insp</label>
                            <!-- Campo para quantidade inspecionada -->
                            <input style="width: 100%;" type="number" id="insp" onchange="calculaNconf()">
                        </div>
                        <div class="quantidade">
                            <label style="font-size: 13px;">Refugo</label>
                            <input style="width: 100%;" type="number" id="refugo" onchange="calculaNconf()">
                        </div>
                        <div class="quantidade">
                            <label style="font-size: 13px;">Conserto</label>
                            <input style="width: 100%;" type="number" id="cons" onchange="calculaNconf()">
                        </div>
                        <div class="quantidade">
                            <label style="font-size: 13px;">Ñ Conf</label>
                            <!-- Campo somente leitura para não conformes -->
                            <input readonly style="width: 100%; background-color: #ebebeb;" type="number" id="nconf">
                        </div>
                    </div>
                </div>
                <div>
                    <h5 id="h5">OPERADOR:</h5>
                    <!-- Campo obrigatório para nome do operador -->
                    <input required id="operador" type="text" placeholder="Digite o operador">
                </div>
                <div>
                    <h5 id="h5">PARTE:</h5>
                    <label id="ZCB_PARTE"></label> <!-- Código da parte -->
                </div>
                <div>
                    <h5 id="h5">QUANTIDADE:</h5>
                    <label id="ZCB_QUANT"></label> <!-- Quantidade total -->
                </div>
                <div>
                    <h5 id="h5">CENTRO DE CUSTO:</h5>
                    <div>
                        <label id="HB_CC"></label> <!-- Código do centro de custo -->
                        <label id="CTT_DESC01"></label> <!-- Descrição do centro de custo -->
                    </div>
                </div>
                <div>
                    <h5 id="h5">PRODUTO:</h5>
                    <label id="ZCB_ZDESC"></label> <!-- Descrição do produto -->
                </div>
                <div>
                    <h5 id="h5">CENTRO DE TRABALHO:</h5>
                    <div>
                        <label id="ZHF_CTRAB"></label> <!-- Código do centro de trabalho -->
                        <label id="HB_NOME"></label> <!-- Nome do centro de trabalho -->
                    </div>
                </div>
            </div>
        </div>
        <div id="opcoes"> <!-- Botões de ação -->
            <button id="lerop" class="btn btn-secondary" style="display:none;" onclick="abrirCamera()">Ler OP</button>
            <button id="registrar" class="btn btn-secondary" onclick="registrarInspecao()">Registrar Problema</button>
            <button id="enviar" class="btn btn-success" onclick="enviarFicha()">Enviar</button>
        </div>
        <div id="footer"> <!-- Rodapé com tabela de inspeções -->
            <div id="inspecoes">
                <div id="tabela">
                    <table> <!-- Tabela para exibir inspeções registradas -->
                        <thead>
                            <th>SEQUENCIA</th>
                            <th>SETOR</th>
                            <th>SETOR DO PROBLEMA</th>
                            <th>CODIGO MOTIVO</th>
                            <th>MOTIVO</th>
                            <th>QNTD</th>
                            <th>OBSERVAÇÃO</th>
                            <th>IMAGENS</th>
                        </thead>
                        <tbody>
                            <!-- Verifica se há inspeções na sessão -->
                            <?php if (!empty($_SESSION["ficha"])): ?>
                                <!-- Loop para exibir cada inspeção -->
                                <?php foreach ($_SESSION["ficha"] as $indice => $inspecao): ?>
                                    <tr id="inspecao-<?php echo $indice; ?>" onclick="mostrarBotoes(<?php echo $indice; ?>)">
                                        <td><?php echo $indice; ?></td>
                                        <td><?php echo htmlspecialchars($inspecao['setorq']); ?></td>
                                        <td><?php echo htmlspecialchars($inspecao['setornome']); ?></td>
                                        <td><?php echo htmlspecialchars($inspecao['codigomotivo']); ?></td>
                                        <td><?php echo htmlspecialchars($inspecao['descricaodefeito']); ?></td>
                                        <td><?php echo htmlspecialchars($inspecao['qntdncf']); ?></td>
                                        <td><?php echo htmlspecialchars($inspecao['text-obs']); ?></td>
                                        <td><?php echo htmlspecialchars(count($inspecao['imagens'])) ?></td>
                                    </tr>
                                    <tr id="botoes-<?php echo $indice; ?>" class="botoes-linha" style="display: none;">
                                        <td>
                                            <!-- Botão para excluir inspeção -->
                                            <button id="excluir" class="btn btn-danger" 
                                                onclick="excluirInspecao(<?php echo $indice; ?>)">Excluir</button>
                                        </td>
                                        <!-- Loop para exibir imagens associadas -->
                                        <?php foreach ($inspecao['imagens'] as $indiceImg => $imagem): ?>
                                            <td>
                                                <img id="imagem-linha" onclick="ampliarImagem(this.src)" 
                                                    src="data:image/png;base64,<?php echo htmlspecialchars($imagem) ?>">
                                            </td>
                                        <?php endforeach; ?>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- Modal para exibir imagens ampliadas -->
        <div id="modal-imagem" class="modal" onclick="fecharModal()">
            <span class="fechar">×</span>
            <img class="modal-conteudo" id="imagem-ampliada">
        </div>
        <!-- Overlay para câmera de QR Code -->
        <div id="camera-overlay" 
            style="display:none; position: fixed; top: 0; left: 0; width: 100vw; height: calc(var(--vh, 1vh) *100); background-color: rgba(0, 0, 0, 0.8); z-index: 1000; align-items: center; justify-content:center">
            <button id="fecharCam" type="button" onclick="fecharCamera()" class="btn btn-danger">Sair da camera</button>
            <video id="qr-reader" style="width: 480px; height: 480px;"></video> <!-- Vídeo para leitura de QR -->
        </div>
    </div>
</body>
</html>