<?php
require 'consulta.php'; // Inclui o arquivo de consultas ao banco de dados

// Verifica se o usuário está autenticado via sessão
if (!isset($_SESSION['cid'])) {
    header("Location: ../../index.php"); // Redireciona para a página de login se não autenticado
    exit(); // Encerra a execução do script
}
?>

<html lang="pt-br"> <!-- Define o idioma como português brasileiro -->
<head>
    <title>Registrar Inspeção</title> <!-- Título da página -->
    <meta charset="UTF-8"> <!-- Define a codificação de caracteres como UTF-8 -->
    <link rel="stylesheet" type="text/css" href="apontarqualidade.css"> <!-- Inclui o arquivo CSS personalizado -->
    <!-- Inclui o Bootstrap 5.3.3 via CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Configura a responsividade -->
    <script src="script.js"></script> <!-- Inclui o arquivo JavaScript personalizado -->
</head>

<body>
    <div class="bloco"> <!-- Container principal da interface -->
        <h1 id="titulo">Registrar Inspeção</h1> <!-- Título da seção -->
        <div id="dadosinsp"> <!-- Seção para entrada de dados de inspeção -->
            <div id="forms"> <!-- Container dos formulários -->
                <div id="text"> <!-- Seção de texto e campos -->
                    <div class="rows"> <!-- Linha de campos -->
                        <div> <!-- Campo Setor Qualidade -->
                            <label>SETOR QUALIDADE</label> <!-- Rótulo do campo -->
                            <div id="customDropdown"> <!-- Dropdown personalizado -->
                                <!-- Input readonly para exibir o setor selecionado -->
                                <input style="height: 25px; width: 40px;" id="setorq" onclick="toggleDropdown(this)" type="text" readonly>
                                <div style="display: none; width: auto;" id="dropdownOptions2" class="dropdown-options"> <!-- Opções do dropdown, ocultas por padrão -->
                                    <?php foreach ($setores as $index => $setor) { ?> <!-- Loop pelos setores disponíveis -->
                                        <div class="option" onclick="selectOption2(this)" 
                                            data-value="<?php echo htmlspecialchars($setor['X5_CHAVE']); ?>" 
                                            data-descri="<?php echo htmlspecialchars($setor['X5_DESCRI']); ?>"> <!-- Opção clicável -->
                                            <?php echo ($index + 1) . '. ' . htmlspecialchars($setor['X5_CHAVE']) . ' - ' . htmlspecialchars($setor['X5_DESCRI']); ?> <!-- Exibe índice, chave e descrição -->
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                        <div> <!-- Campo Nome do Setor -->
                            <label>NOME SETOR</label> <!-- Rótulo do campo -->
                            <input id="setornome" type="text" readonly> <!-- Input readonly para exibir o nome do setor -->
                        </div>
                    </div>
                    <div class="rows"> <!-- Linha de campos -->
                        <div> <!-- Campo Código do Motivo -->
                            <label>CODIGO DO MOTIVO</label> <!-- Rótulo do campo -->
                            <div id="customDropdown"> <!-- Dropdown personalizado -->
                                <!-- Input readonly para exibir o código do motivo -->
                                <input style="height: 25px; width: 40px;" id="codmot" onclick="toggleDropdown(this)" type="text" readonly>
                                <div style="display: none; width:auto;" id="dropdownOptions3" class="dropdown-options"> <!-- Opções do dropdown, ocultas por padrão -->
                                    <?php if (!empty($_SESSION['defeitos'])) { // Verifica se há defeitos na sessão
                                        foreach ($_SESSION['defeitos'] as $index => $defeito) { ?> <!-- Loop pelos defeitos -->
                                            <div class="option" onclick="selectOption3(this)" 
                                                data-value="<?php echo htmlspecialchars($defeito['ZHN_DEFEIT']); ?>" 
                                                data-descri="<?php echo htmlspecialchars($defeito['ZHN_DESCDE']); ?>"> <!-- Opção clicável -->
                                                <?php echo ($index + 1) . '. ' . htmlspecialchars($defeito['ZHN_DEFEIT']) . ' - ' . htmlspecialchars($defeito['ZHN_DESCDE']); ?> <!-- Exibe índice, código e descrição -->
                                            </div>
                                        <?php }
                                    } ?>
                                </div>
                            </div>
                        </div>
                        <div> <!-- Campo Descrição do Defeito -->
                            <label>DESCRIÇÃO DO DEFEITO</label> <!-- Rótulo do campo -->
                            <input id="descricaodefeito" type="text" readonly> <!-- Input readonly para exibir a descrição -->
                        </div>
                    </div>
                    <div class="rows"> <!-- Linha de campos -->
                        <div> <!-- Campo Quantidade -->
                            <label>QUANTIDADE</label> <!-- Rótulo do campo -->
                            <input id="qntdncf" type="number"> <!-- Input numérico para quantidade não conforme -->
                        </div>
                        <div> <!-- Campo Observação -->
                            <label>OBSERVAÇÃO:</label> <!-- Rótulo do campo -->
                            <input id="text-obs" type="text" maxlength="50"> <!-- Input de texto com limite de 50 caracteres -->
                        </div>
                    </div>
                </div>
            </div>
            <div style="display: flex; flex-direction: column;"> <!-- Container para imagens e botão -->
                <div id="imagens"> <!-- Seção do carrossel de imagens -->
                    <div id="carousel-container"> <!-- Container do carrossel -->
                        <button onclick="prevImage()" id="prev-btn">‹</button> <!-- Botão para imagem anterior -->
                        <img onclick="abrirFoto()" id="carousel"> <!-- Imagem principal do carrossel -->
                            <!-- As imagens serão adicionadas aqui dinamicamente via JS -->
                        </img>
                        <button onclick="nextImage()" id="next-btn">›</button> <!-- Botão para próxima imagem -->
                        <!-- Botão para fechar a visualização ampliada -->
                        <img onclick="fecharFoto()" id="fecharFoto" 
                            style="width:50px; display:none; position:absolute; top:10; right:10;" 
                            src="images/excluir.png">
                        <!-- Botão para excluir a imagem -->
                        <img onclick="deleteFoto()" id="deleteFoto" 
                            style="width:50px; display:none; position:absolute; bottom:10; right:10;" 
                            src="images/delete.png">
                    </div>
                </div>
                <!-- Botão para abrir a câmera e tirar foto -->
                <button type="button" class="btn btn-secondary" id="tirar-foto" onclick="abrirCamera2()">Tirar Foto</button>
            </div>
        </div>
        <div id="buttons"> <!-- Container dos botões de ação -->
            <!-- Botão para voltar à página anterior -->
            <button type="button" class="btn btn-danger" id="voltar" onclick="voltar()">Voltar</button>
            <!-- Botão para salvar a inspeção -->
            <button type="button" class="btn btn-success" id="salvar" onclick="salvarInspecao()">Salvar</button>
        </div>
    </div>
    <!-- Overlay para exibir a câmera -->
    <div id="camera-overlay" 
        style="display:none; position: fixed; top: 0; left: 0; width: 100vw; height: calc(var(--vh, 1vh) *100); background-color: rgba(0, 0, 0, 0.8); z-index: 1000; align-items: center; justify-content:center">
        <div id="botoes"> <!-- Container dos botões da câmera -->
            <!-- Botão para fechar a câmera -->
            <button id="fecharCam" type="button" onclick="fecharCamera()" class="btn btn-danger">Sair da camera</button>
            <!-- Botão para capturar a imagem -->
            <button class="btn btn-secondary" id="capture" onclick="capturarImagem()">Capturar Imagem</button>
        </div>
        <!-- Elemento de vídeo para exibir a câmera -->
        <video id="video" style="width: 480px; height: 480px;" autoplay></video>
    </div>
</body>

</html>