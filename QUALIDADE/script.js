// Define a unidade --vh ao carregar a página para ajustar a altura em dispositivos móveis
window.addEventListener('load', () => {
  const vh = window.innerHeight * 0.01; // Calcula 1% da altura da viewport
  document.documentElement.style.setProperty('--vh', `${vh}px`); // Define a variável CSS --vh
});

// Atualiza a unidade --vh ao redimensionar a janela
window.addEventListener('resize', () => {
  let vh = window.innerHeight * 0.01; // Recalcula 1% da altura da viewport
  document.documentElement.style.setProperty('--vh', `${vh}px`); // Atualiza a variável CSS --vh
});

// Realiza consulta ao servidor com base no código de barras
function fazerConsulta() {
  document.getElementById('dados').style.display = 'none'; // Oculta a seção de dados inicialmente
  const codigoBarra = document.getElementById('codigobarra').value; // Obtém o valor do código de barras
  console.log(codigoBarra); // Loga o código no console para depuração
  const xhr = new XMLHttpRequest(); // Cria uma nova requisição AJAX
  xhr.open('POST', 'consulta.php', true); // Configura a requisição POST para consulta.php
  xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded'); // Define o tipo de conteúdo
  xhr.send('action=dados&codigoBarra=' + encodeURIComponent(codigoBarra)); // Envia os dados codificados
  xhr.onload = function () {
    if (xhr.status === 200) { // Verifica se a resposta do servidor foi bem-sucedida
      console.log(xhr.responseText); // Loga a resposta do servidor
      const result = JSON.parse(xhr.responseText); // Converte a resposta JSON em objeto
      if (result != "erro") { // Verifica se não houve erro na consulta
        document.getElementById('dados').style.display = 'grid'; // Exibe a seção de dados
        // Preenche os campos com os dados retornados
        document.getElementById('ZCB_LOTE').textContent = result.result.ZCB_LOTE;
        document.getElementById('ZCB_NUM').textContent = result.result.ZCB_NUM + " - " + result.result.ZCB_ITEM;
        document.getElementById('ZCB_SEQUEN').textContent = result.result.ZCB_SEQUEN;
        document.getElementById('ZCB_PARTE').textContent = result.result.ZCB_PARTE;
        document.getElementById('HB_CC').textContent = result.result.HB_CC;
        document.getElementById('CTT_DESC01').textContent = "- " + result.result.CTT_DESC01;
        document.getElementById('ZCB_ZDESC').textContent = result.result.ZCB_ZDESC;
        document.getElementById('ZCB_QUANT').textContent = result.result.ZCB_QUANT;
        document.getElementById('ZHF_OPERAC').value = result.result.ZHF_OPERAC;
        document.getElementById('ZHF_DESCRI').textContent = "- " + result.result.ZHF_DESCRI;
        document.getElementById('ZHF_CTRAB').textContent = result.result.ZHF_CTRAB;
        document.getElementById('HB_NOME').textContent = "- " + result.result.HB_NOME;

        sessionStorage.setItem('dadosConsulta', JSON.stringify(result.result)); // Salva os dados na sessionStorage
        atualizarDropdown(result.recursos); // Atualiza o dropdown de recursos
      }
    }
  };
}

// Atualiza os dados quando a operação é alterada
function mudaOperacao() {
  let codigoBarra = document.getElementById('codigobarra').value || document.getElementById('codigobarra').placeholder; // Obtém o código de barras do input ou placeholder
  const operacao = document.getElementById('ZHF_OPERAC').value; // Obtém o valor da operação

  const xhr = new XMLHttpRequest(); // Cria uma nova requisição AJAX
  xhr.open('POST', 'consulta.php', true); // Configura a requisição POST
  xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded'); // Define o tipo de conteúdo
  xhr.send('action=dados&codigoBarra=' + encodeURIComponent(codigoBarra) + '&operacao=' + operacao); // Envia os dados
  xhr.onload = function () {
    if (xhr.status === 200) { // Verifica se a resposta foi bem-sucedida
      console.log(xhr.responseText); // Loga a resposta
      const result = JSON.parse(xhr.responseText); // Converte a resposta em objeto
      if (result != "erro") { // Verifica se não houve erro
        // Atualiza os campos com os novos dados
        document.getElementById('ZCB_LOTE').textContent = result.result.ZCB_LOTE;
        document.getElementById('ZCB_NUM').textContent = result.result.ZCB_NUM + " - " + result.result.ZCB_ITEM;
        document.getElementById('ZCB_SEQUEN').textContent = result.result.ZCB_SEQUEN;
        document.getElementById('ZCB_PARTE').textContent = result.result.ZCB_PARTE;
        document.getElementById('HB_CC').textContent = result.result.HB_CC;
        document.getElementById('CTT_DESC01').textContent = "- " + result.result.CTT_DESC01;
        document.getElementById('ZCB_ZDESC').textContent = result.result.ZCB_ZDESC;
        document.getElementById('ZCB_QUANT').textContent = result.result.ZCB_QUANT;
        document.getElementById('ZHF_OPERAC').value = result.result.ZHF_OPERAC;
        document.getElementById('ZHF_DESCRI').textContent = "- " + result.result.ZHF_DESCRI;
        document.getElementById('ZHF_CTRAB').textContent = result.result.ZHF_CTRAB;
        document.getElementById('HB_NOME').textContent = "- " + result.result.HB_NOME;

        sessionStorage.setItem('dadosConsulta', JSON.stringify(result.result)); // Salva os novos dados
        atualizarDropdown(result.recursos); // Atualiza o dropdown
      }
    }
  };
}

// Atualiza o dropdown de recursos com os dados recebidos
function atualizarDropdown(recursos) {
  const dropdownContainer = document.getElementById('dropdownOptions'); // Container do dropdown
  dropdownContainer.innerHTML = ''; // Limpa as opções existentes

  recursos.forEach(function(recurso, index) { // Itera sobre os recursos
    const optionElement = document.createElement('div'); // Cria um novo elemento de opção
    optionElement.classList.add('option'); // Adiciona a classe 'option'
    optionElement.setAttribute('onclick', 'selectOption(this)'); // Define o evento onclick
    optionElement.setAttribute('data-value', recurso['H1_CODIGO']); // Define o valor
    optionElement.setAttribute('data-descri', recurso['H1_DESCRI']); // Define a descrição
    optionElement.innerHTML = (index + 1) + '. ' + recurso['H1_CODIGO'] + ' - ' + recurso['H1_DESCRI']; // Define o texto visível
    dropdownContainer.appendChild(optionElement); // Adiciona a opção ao container
  });
}

// Calcula a quantidade não conforme com base nos valores inseridos
function calculaNconf() {
  const insp = parseInt(document.getElementById('insp').value) || 0; // Quantidade inspecionada
  const refugo = parseInt(document.getElementById('refugo').value) || 0; // Quantidade de refugo
  const cons = parseInt(document.getElementById('cons').value) || 0; // Quantidade de conserto
  const max = parseInt(document.getElementById('ZCB_QUANT').textContent); // Quantidade máxima da OP
  const result = refugo + cons; // Calcula total não conforme

  if (max < insp) { // Verifica se a quantidade inspecionada excede o máximo
    alert("VALOR INSPECIONADO INVÁLIDO: VALOR MAIOR DO QUE O MÁXIMO DA OP!");
    document.getElementById('insp').value = ""; // Limpa o campo inspecionado
    document.getElementById('nconf').value = ""; // Limpa o campo não conforme
  } else if (!isNaN(insp) && !isNaN(refugo) && !isNaN(cons)) { // Verifica se os valores são numéricos
    if (result <= insp) { // Verifica se o total não conforme não excede o inspecionado
      document.getElementById('nconf').value = result; // Define o valor não conforme
    } else {
      document.getElementById('nconf').value = ""; // Limpa o campo se inválido
      alert("VALOR NÃO CONFORME INVÁLIDO: HÁ MAIS PEÇAS NÃO CONFORME DO QUE INSPECIONADAS!");
    }
  }
}

// Redireciona para a página de registro de inspeção, salvando dados
function registrarInspecao() {
  const operacao = document.getElementById('ZHF_OPERAC').value; // Operação
  const recurso = document.getElementById('filterInput').value; // Recurso selecionado
  const operador = document.getElementById('operador').value; // Operador
  const insp = document.getElementById('insp').value; // Quantidade inspecionada
  const refugo = document.getElementById('refugo').value; // Quantidade de refugo
  const cons = document.getElementById('cons').value; // Quantidade de conserto
  const nconf = document.getElementById('nconf').value; // Quantidade não conforme
  const codigoBarra = document.getElementById('codigobarra').value || document.getElementById('codigobarra').placeholder; // Código de barras

  const data = [operacao, recurso, operador, insp, refugo, cons, nconf, codigoBarra]; // Array com os dados
  sessionStorage.setItem('dadosSelecao', JSON.stringify(data)); // Salva na sessionStorage
  window.location.replace("RegistrarInspecao.php"); // Redireciona para a página de registro
}

// Volta para a página principal
function voltar() {
  window.location.replace("ApontarQualidade.php"); // Redireciona para a página principal
}

// Salva a inspeção no servidor
function salvarInspecao() {
  const setorq = document.getElementById('setorq').value; // Setor de qualidade
  const setornome = document.getElementById('setornome').value; // Nome do setor
  const codmot = document.getElementById('codmot').value; // Código do motivo
  const descricaodefeito = document.getElementById('descricaodefeito').value; // Descrição do defeito
  const qntddncf = document.getElementById('qntdncf').value; // Quantidade não conforme

  if (setorq && setornome && codmot && descricaodefeito && qntddncf) { // Verifica se todos os campos estão preenchidos
    const dados = {
      action: "adicionarInspecao",
      setorq: setorq,
      setornome: setornome,
      codigomotivo: codmot,
      descricaodefeito: descricaodefeito,
      qntdncf: qntddncf,
      obs: document.getElementById('text-obs').value, // Observação
      imagens: base64Images // Array de imagens em base64
    };

    const xhr = new XMLHttpRequest(); // Cria uma nova requisição AJAX
    xhr.open('POST', 'salvar.php', true); // Configura a requisição POST
    xhr.setRequestHeader('Content-Type', 'application/json'); // Define o tipo de conteúdo como JSON
    xhr.onload = function () {
      if (xhr.status === 200) { // Verifica se a resposta foi bem-sucedida
        try {
          const response = JSON.parse(xhr.responseText); // Converte a resposta em objeto
          if (response.status === "sucesso") { // Verifica se o salvamento foi bem-s sucedido
            console.log(response.mensagem); // Loga a mensagem de sucesso
            window.location.replace("ApontarQualidade.php"); // Redireciona para a página principal
          } else {
            alert("Erro: " + response.mensagem); // Exibe mensagem de erro
          }
        } catch (e) {
          alert("Erro inesperado na resposta do servidor."); // Trata erro de parsing
        }
      } else {
        alert("Erro ao salvar inspeção. Código: " + xhr.status); // Exibe erro de status
      }
    };
    xhr.send(JSON.stringify(dados)); // Envia os dados como JSON
  } else {
    alert("FALTAM CAMPOS A SEREM INFORMADOS!"); // Alerta sobre campos vazios
  }
}

// Alterna a visibilidade dos botões de uma linha específica na tabela
function mostrarBotoes(indice) {
  document.querySelectorAll('.botoes-linha').forEach(row => { // Oculta todas as linhas de botões
    if (row.id !== 'botoes-' + indice) {
      row.style.display = 'none';
    }
  });

  const botoesRow = document.getElementById('botoes-' + indice); // Linha de botões específica
  botoesRow.style.display = (botoesRow.style.display === 'none' || !botoesRow.style.display) ? 'table-row' : 'none'; // Alterna visibilidade
}

// Amplia uma imagem em um modal
function ampliarImagem(src) {
  document.getElementById("imagem-ampliada").src = src; // Define a fonte da imagem ampliada
  document.getElementById("modal-imagem").style.display = "block"; // Exibe o modal
}

// Fecha o modal de imagem
function fecharModal() {
  document.getElementById("modal-imagem").style.display = "none"; // Oculta o modal
}

// Exclui uma inspeção da sessão e da tabela
function excluirInspecao(indice) {
  if (confirm("Tem certeza que deseja excluir esta inspeção?")) { // Confirmação do usuário
    const xhr = new XMLHttpRequest(); // Cria uma nova requisição AJAX
    xhr.open('POST', 'consulta.php', true); // Configura a requisição POST
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded'); // Define o tipo de conteúdo
    xhr.onload = function () {
      if (xhr.status === 200) { // Verifica se a resposta foi bem-sucedida
        const response = JSON.parse(xhr.responseText); // Converte a resposta em objeto
        if (response.status === "sucesso") { // Verifica se a exclusão foi bem-sucedida
          // Remove as linhas da tabela
          document.getElementById('inspecao-' + indice).remove();
          document.getElementById('botoes-' + indice).remove();
        } else {
          alert("Erro ao excluir a inspeção: " + response.mensagem); // Exibe mensagem de erro
        }
      } else {
        alert("Erro ao se comunicar com o servidor."); // Exibe erro de comunicação
      }
    };
    xhr.send('action=excluirInspecao&indice=' + encodeURIComponent(indice)); // Envia o índice para exclusão
    setTimeout(() => location.reload(), 1000); // Recarrega a página após 1 segundo
  }
}

// Alterna a visibilidade de um dropdown
function toggleDropdown(element) {
  var dropdown = element.nextElementSibling; // Obtém o próximo elemento (dropdown)
  if (dropdown.style.display === "none" || dropdown.style.display === "") { // Verifica se está oculto
    dropdown.style.display = "block"; // Exibe o dropdown
    if (dropdown.getAttribute("id") == "dropdownOptions2") { // Se for o dropdown de setores
      document.getElementById("dropdownOptions3").style.display = "none"; // Oculta o dropdown de defeitos
    }
  } else {
    dropdown.style.display = "none"; // Oculta o dropdown
  }
}

// Seleciona uma opção no dropdown de recursos
function selectOption(element) {
  let input = element.closest("#customDropdown").querySelector("input"); // Obtém o input do dropdown
  sessionStorage.setItem('recurso', element.getAttribute("data-value")); // Salva o recurso selecionado
  input.value = element.getAttribute("data-value") + " - " + element.getAttribute("data-descri"); // Define o valor visível
  document.getElementById("dropdownOptions").style.display = "none"; // Oculta o dropdown
}

// Seleciona uma opção no dropdown de setores e atualiza defeitos
function selectOption2(element) {
  let input = element.closest("#customDropdown").querySelector("input"); // Obtém o input do dropdown
  input.value = element.getAttribute("data-value"); // Define o valor do setor
  document.getElementById("setornome").value = element.getAttribute("data-descri"); // Define o nome do setor

  const xhr = new XMLHttpRequest(); // Cria uma nova requisição AJAX
  xhr.open("POST", "consulta.php", true); // Configura a requisição POST
  xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded"); // Define o tipo de conteúdo
  xhr.send("action=atualizardef&setor=" + encodeURIComponent(element.getAttribute("data-value"))); // Envia o setor

  xhr.onload = function () {
    if (xhr.status === 200) { // Verifica se a resposta foi bem-sucedida
      console.log("Defeitos atualizados:", xhr.responseText); // Loga a resposta
      let defeitos = JSON.parse(xhr.responseText); // Converte a resposta em objeto
      atualizarDropdownDeDefeitos(defeitos); // Atualiza o dropdown de defeitos
      document.getElementById('codmot').value = ''; // Limpa o código do motivo
      document.getElementById('descricaodefeito').value = ''; // Limpa a descrição do defeito
    }
  };
  document.getElementById("dropdownOptions2").style.display = "none"; // Oculta o dropdown
}

// Atualiza o dropdown de defeitos com os dados recebidos
function atualizarDropdownDeDefeitos(defeitos) {
  let dropdown = document.getElementById("dropdownOptions3"); // Container do dropdown de defeitos
  dropdown.innerHTML = ""; // Limpa as opções existentes

  if (defeitos.length > 0) { // Verifica se há defeitos
    defeitos.forEach((defeito, index) => { // Itera sobre os defeitos
      let option = document.createElement("div"); // Cria uma nova opção
      option.classList.add("option"); // Adiciona a classe 'option'
      option.setAttribute("data-value", defeito.ZHN_DEFEIT); // Define o valor
      option.setAttribute("data-descri", defeito.ZHN_DESCDE); // Define a descrição
      option.innerHTML = `${index + 1}. ${defeito.ZHN_DEFEIT} - ${defeito.ZHN_DESCDE}`; // Define o texto visível
      option.onclick = function () { selectOption3(this); }; // Define o evento onclick
      dropdown.appendChild(option); // Adiciona a opção ao container
    });
  } else {
    dropdown.innerHTML = '<div class="option">Nenhum defeito encontrado</div>'; // Mensagem se não houver defeitos
  }
}

// Seleciona uma opção no dropdown de defeitos
function selectOption3(element) {
  let input = element.closest("#customDropdown").querySelector("input"); // Obtém o input do dropdown
  input.value = element.getAttribute("data-value"); // Define o código do motivo
  document.getElementById("descricaodefeito").value = element.getAttribute("data-descri"); // Define a descrição
  document.getElementById("dropdownOptions3").style.display = "none"; // Oculta o dropdown
}

// Filtra as opções do dropdown de recursos com base no texto digitado
function filterFunction() {
  const input = document.getElementById("filterInput"); // Input de filtro
  const filter = input.value.toUpperCase(); // Valor do filtro em maiúsculas
  const select = document.getElementById("dropdownOptions"); // Container do dropdown
  const options = select.getElementsByClassName("option"); // Todas as opções
  for (let i = 0; i < options.length; i++) { // Itera sobre as opções
    const txtValue = options[i].textContent || options[i].innerText; // Texto da opção
    options[i].style.display = txtValue.toUpperCase().indexOf(filter) > -1 ? "" : "none"; // Exibe ou oculta a opção
  }
}

// Verifica se o dispositivo é móvel
function isMobile() {
  return /Mobi|Android/i.test(navigator.userAgent); // Testa o user agent
}

// Configura o botão de leitura de QR code em dispositivos móveis
document.addEventListener("DOMContentLoaded", function () {
  const cameraButton = document.getElementById('lerop'); // Botão de leitura de QR
  if (cameraButton && isMobile()) { // Verifica se o botão existe e é dispositivo móvel
    cameraButton.style.display = 'block'; // Exibe o botão
  }
});

// Abre a câmera para leitura de QR code
function abrirCamera() {
  document.getElementById('camera-overlay').style.display = 'flex'; // Exibe o overlay da câmera
  document.getElementById('qr-reader').style.display = "block"; // Exibe o vídeo
  let scanner = new Instascan.Scanner({ video: document.getElementById('qr-reader') }); // Inicializa o scanner
  scanner.addListener('scan', function (content) { // Listener para quando um QR é escaneado
    document.getElementById('codigobarra').value = content; // Define o valor escaneado
    fazerConsulta(); // Realiza a consulta
    fecharCamera(); // Fecha a câmera
    scanner.stop(); // Para o scanner
  });
  Instascan.Camera.getCameras().then(function (cameras) { // Obtém as câmeras disponíveis
    if (cameras.length > 0) { // Verifica se há câmeras
      let backCamera = cameras.find(camera => camera.name.toLowerCase().includes('back')); // Busca câmera traseira
      scanner.start(backCamera || cameras[0]); // Inicia a câmera traseira ou a primeira disponível
    } else {
      console.error('Nenhuma câmera encontrada.'); // Loga erro se não houver câmeras
    }
  }).catch(function (e) {
    console.error(e); // Loga erro ao acessar câmeras
  });
}

// Fecha a câmera de leitura de QR code
function fecharCamera() {
  document.getElementById('camera-overlay').style.display = 'none'; // Oculta o overlay
  document.getElementById('qr-reader').style.display = 'none'; // Oculta o vídeo
}

// Variável global para o stream de vídeo
let videoStream = null;

// Abre a câmera para captura de fotos
function abrirCamera2() {
  const overlay = document.getElementById("camera-overlay"); // Overlay da câmera
  overlay.style.display = "flex"; // Exibe o overlay

  navigator.mediaDevices.enumerateDevices() // Lista os dispositivos de mídia
    .then(devices => {
      const cameras = devices.filter(device => device.kind === "videoinput"); // Filtra câmeras
      let cameraId = cameras.find(camera => camera.label.toLowerCase().includes("back"))?.deviceId; // Busca câmera traseira

      const constraints = { video: cameraId ? { deviceId: { exact: cameraId } } : { facingMode: "environment" } }; // Define preferência por câmera traseira
      return navigator.mediaDevices.getUserMedia(constraints); // Solicita acesso à câmera
    })
    .then(stream => {
      videoStream = stream; // Armazena o stream
      document.getElementById("video").srcObject = stream; // Define o stream no elemento de vídeo
    })
    .catch(error => {
      console.error("Erro ao acessar a câmera:", error); // Loga erro ao acessar a câmera
    });
}

// Fecha a câmera de captura de fotos
function fecharCamera() {
  const overlay = document.getElementById("camera-overlay"); // Overlay da câmera
  overlay.style.display = "none"; // Oculta o overlay

  if (videoStream) { // Verifica se há um stream ativo
    videoStream.getTracks().forEach(track => track.stop()); // Para todas as trilhas do stream
  }
}

// Variáveis globais para gerenciar imagens capturadas
let base64Images = []; // Array para armazenar imagens em base64
let currentIndex = 0; // Índice da imagem atual no carrossel

// Captura uma imagem da câmera
function capturarImagem() {
  const video = document.getElementById("video"); // Elemento de vídeo
  const canvas = document.createElement("canvas"); // Cria um canvas temporário
  const context = canvas.getContext("2d"); // Contexto 2D do canvas

  canvas.width = video.videoWidth; // Define a largura do canvas
  canvas.height = video.videoHeight; // Define a altura do canvas
  context.drawImage(video, 0, 0, canvas.width, canvas.height); // Desenha o vídeo no canvas

  const base64String = canvas.toDataURL("image/png").split(',')[1]; // Converte para base64 e remove prefixo
  base64Images.push(base64String); // Adiciona a imagem ao array

  console.log("Imagem capturada:", base64String); // Loga a imagem capturada
  console.log("Total de imagens salvas:", base64Images.length); // Loga o total de imagens

  if (base64Images.length === 1) { // Se for a primeira imagem
    document.getElementById("carousel-container").style.cursor = "pointer"; // Define cursor como clicável
    updateImage(); // Atualiza a imagem exibida
  } else {
    nextImage(); // Vai para a próxima imagem
  }
  fecharCamera(); // Fecha a câmera após captura
}

// Atualiza a imagem exibida no carrossel
function updateImage() {
  const imgElement = document.getElementById("carousel"); // Elemento de imagem do carrossel
  if (base64Images.length === 0) { // Verifica se há imagens
    console.warn("Nenhuma imagem disponível para exibir."); // Loga aviso
    imgElement.style.opacity = "0"; // Oculta a imagem
    return;
  }

  imgElement.style.opacity = "0"; // Transição de opacidade
  setTimeout(() => {
    imgElement.src = `data:image/png;base64,${base64Images[currentIndex]}`; // Define a nova imagem
    imgElement.style.opacity = "1"; // Restaura a opacidade
  }, 300); // Aguarda 300ms para a transição
}

// Avança para a próxima imagem no carrossel
function nextImage() {
  if (base64Images.length === 0) return; // Sai se não houver imagens
  currentIndex = (currentIndex + 1) % base64Images.length; // Calcula o próximo índice
  updateImage(); // Atualiza a imagem
}

// Retrocede para a imagem anterior no carrossel
function prevImage() {
  if (base64Images.length === 0) return; // Sai se não houver imagens
  currentIndex = (currentIndex - 1 + base64Images.length) % base64Images.length; // Calcula o índice anterior
  updateImage(); // Atualiza a imagem
}

// Inicia o carrossel automaticamente se houver imagens
window.onload = function () {
  if (base64Images.length > 0) { // Verifica se há imagens ao carregar
    updateImage(); // Exibe a primeira imagem
    setInterval(nextImage, 3000); // Avança automaticamente a cada 3 segundos
  }
};

// Abre a visualização ampliada da imagem
function abrirFoto() {
  if (base64Images.length > 0) { // Verifica se há imagens
    const element = document.getElementById("carousel-container"); // Container do carrossel
    const fechar = document.getElementById("fecharFoto"); // Botão de fechar
    const del = document.getElementById("deleteFoto"); // Botão de excluir
    // Define estilos para visualização ampliada
    element.style.display = "flex";
    element.style.position = "fixed";
    element.style.top = "0px";
    element.style.left = "0px";
    element.style.width = "100vw";
    element.style.height = "calc(var(--vh, 1vh)* 100)";
    element.style.backgroundColor = "rgba(0, 0, 0, 0.8)";
    element.style.zIndex = "1000";
    element.style.alignItems = "center"; // Corrige typo de 'alingItems'
    element.style.justifyContent = "center";
    element.style.cursor = "auto";
    del.style.display = "block"; // Exibe o botão de excluir
    del.style.cursor = "pointer";
    fechar.style.display = "block"; // Exibe o botão de fechar
    fechar.style.cursor = "pointer";
  }
}

// Fecha a visualização ampliada da imagem
function fecharFoto() {
  const element = document.getElementById("carousel-container"); // Container do carrossel
  const fechar = document.getElementById("fecharFoto"); // Botão de fechar
  const del = document.getElementById("deleteFoto"); // Botão de excluir
  // Restaura os estilos padrão
  element.style.display = "";
  element.style.position = "";
  element.style.top = "";
  element.style.left = "";
  element.style.width = "";
  element.style.height = "";
  element.style.backgroundColor = "";
  element.style.zIndex = "";
  element.style.alignItems = ""; // Corrige typo de 'alingItems'
  element.style.justifyContent = "";
  element.style.cursor = "pointer";
  del.style.display = "none"; // Oculta o botão de excluir
  fechar.style.display = "none"; // Oculta o botão de fechar
}

// Exclui a imagem atual do carrossel
function deleteFoto() {
  const carousel = document.getElementById("carousel"); // Elemento de imagem
  const imagem = carousel.getAttribute("src"); // Obtém a fonte da imagem
  const imagemSemPrefixo = imagem.replace('data:image/png;base64,', ''); // Remove o prefixo base64
  const indice = base64Images.indexOf(imagemSemPrefixo); // Encontra o índice da imagem
  if (indice !== -1) { // Verifica se a imagem foi encontrada
    base64Images.splice(indice, 1); // Remove a imagem do array
    nextImage(); // Avança para a próxima imagem
    if (base64Images.length === 0) { // Se não houver mais imagens
      updateImage(); // Atualiza para ocultar a imagem
    }
  }
}

// Envia a ficha de inspeção para o servidor
function enviarFicha() {
  const codigoBarra = document.getElementById('codigobarra').value || document.getElementById('codigobarra').placeholder; // Código de barras
  const operacao = document.getElementById('ZHF_OPERAC').value; // Operação
  const recursosel = sessionStorage.getItem('recurso'); // Recurso salvo
  const insp = document.getElementById('insp').value; // Quantidade inspecionada
  const refugo = document.getElementById('refugo').value; // Quantidade de refugo
  const cons = document.getElementById('cons').value; // Quantidade de conserto
  const operador = document.getElementById('operador').value; // Operador

  // Loga os valores para depuração
  console.log(codigoBarra);
  console.log(operacao);
  console.log(recursosel);
  console.log(insp);
  console.log(refugo);
  console.log(cons);
  console.log(operador);

  if (codigoBarra && operacao && recursosel && insp && refugo && cons && operador) { // Verifica se todos os campos estão preenchidos
    const xhr = new XMLHttpRequest(); // Cria uma nova requisição AJAX
    xhr.open("POST", "enviar.php", true); // Configura a requisição POST
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded"); // Define o tipo de conteúdo
    // Envia os dados codificados
    xhr.send("codigobarra=" + encodeURIComponent(codigoBarra) +
      "&operacao=" + encodeURIComponent(operacao) +
      "&recurso=" + encodeURIComponent(recursosel) +
      "&insp=" + encodeURIComponent(insp) +
      "&refugo=" + encodeURIComponent(refugo) +
      "&cons=" + encodeURIComponent(cons) +
      "&operador=" + encodeURIComponent(operador)
    );
    xhr.onload = function () {
      if (xhr.status === 200) { // Verifica se a resposta foi bem-sucedida
        console.log(xhr.responseText); // Loga a resposta
        const result = JSON.parse(xhr.responseText); // Converte a resposta em objeto
        if (result['status'] == "erro") { // Verifica se houve erro
          alert("Erro na Inspeção: " + result['mensagem']); // Exibe mensagem de erro
        } else if (result['GRVINSPRESULT']['RETORNO']) { // Verifica se o envio foi bem-sucedido
          alert("Registrado com sucesso!"); // Exibe mensagem de sucesso
          sessionStorage.clear(); // Limpa a sessionStorage
          console.log(result); // Loga o resultado
          location.reload(); // Recarrega a página
        } else {
          alert("Erro ao tentar registrar"); // Exibe mensagem de erro genérico
        }
      }
    };
  } else {
    alert('HÁ CAMPOS EM BRANCO NA FICHA DE INSPEÇÃO!'); // Alerta sobre campos vazios
  }
}

// Restaura os dados salvos ao carregar a página
function restaurarDados() {
  const dadosSalvos = sessionStorage.getItem('dadosConsulta'); // Dados da consulta
  const dadosSelecao = sessionStorage.getItem('dadosSelecao'); // Dados de seleção
  if (dadosSalvos) { // Verifica se há dados salvos
    const result = JSON.parse(dadosSalvos); // Converte os dados em objeto
    document.getElementById('dados').style.display = 'grid'; // Exibe a seção de dados
    // Preenche os campos com os dados salvos
    document.getElementById('ZCB_LOTE').textContent = result.ZCB_LOTE;
    document.getElementById('ZCB_NUM').textContent = result.ZCB_NUM + " - " + result.ZCB_ITEM;
    document.getElementById('ZCB_SEQUEN').textContent = result.ZCB_SEQUEN;
    document.getElementById('ZCB_PARTE').textContent = result.ZCB_PARTE;
    document.getElementById('HB_CC').textContent = result.HB_CC;
    document.getElementById('CTT_DESC01').textContent = "- " + result.CTT_DESC01;
    document.getElementById('ZCB_ZDESC').textContent = result.ZCB_ZDESC;
    document.getElementById('ZCB_QUANT').textContent = result.ZCB_QUANT;
    document.getElementById('ZHF_OPERAC').value = result.ZHF_OPERAC;
    document.getElementById('ZHF_DESCRI').textContent = "- " + result.ZHF_DESCRI;
    document.getElementById('ZHF_CTRAB').textContent = result.ZHF_CTRAB;
    document.getElementById('HB_NOME').textContent = "- " + result.HB_NOME;

    if (dadosSelecao) { // Verifica se há dados de seleção salvos
      const [operacao, recurso, operador, insp, refugo, cons, nconf, codigobarra] = JSON.parse(dadosSelecao); // Desestrutura os dados
      // Preenche os campos de seleção
      document.getElementById('ZHF_OPERAC').value = operacao;
      document.getElementById('filterInput').value = recurso;
      document.getElementById('operador').value = operador;
      document.getElementById('insp').value = insp;
      document.getElementById('refugo').value = refugo;
      document.getElementById('cons').value = cons;
      document.getElementById('nconf').value = nconf;
      document.getElementById('codigobarra').placeholder = codigobarra;
    }
  }
}

// Chama a função de restauração ao carregar a página
document.addEventListener("DOMContentLoaded", restaurarDados);