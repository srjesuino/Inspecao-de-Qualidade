# 📌 Sistema de Registro e Apontamento de Inspeção de Qualidade

## 📋 Descrição
Este projeto é uma solução robusta e inovadora desenvolvida para otimizar o processo de registro e apontamento de inspeções de qualidade em um ambiente industrial. Construído com uma combinação poderosa de PHP, JavaScript, HTML, CSS e integração com serviços SOAP, o sistema oferece uma interface intuitiva e funcionalidades avançadas que demonstram um alto nível de habilidade técnica e atenção aos detalhes.

O sistema foi projetado para atender às necessidades de controle de qualidade, permitindo que os usuários realizem consultas em tempo real a partir de códigos de barras, registrem inspeções detalhadas e apontem dados diretamente para um ERP via API SOAP. A aplicação destaca-se por sua capacidade de gerenciar dados complexos, incluindo captura de imagens via câmera, integração com QR codes e validações rigorosas para garantir a integridade das informações.

![Apontar_Qualidade](/Screenshots-Video/Apontar_Qualidade.png)

## 🚀 Principais Recursos
- **Consulta Dinâmica:** Integração com um banco de dados SQL Server para consultas detalhadas baseadas em códigos de barras, exibindo informações como lote, operação, recurso e quantidade de forma imediata e precisa. ![Leitura do QrCode](/Screenshots-Video/QrCode_Lido.png)
- **Registro de Inspeções:** Interface amigável para registrar defeitos, quantidades não conformes e observações, com suporte a dropdowns dinâmicos atualizados via AJAX e persistência em sessão. ![Tela de Registro de Erros](/Screenshots-Video/Tela_Registro_Inspecao.png)
- **Captura e Gerenciamento de Imagens:** Funcionalidade avançada de captura de fotos via câmera do dispositivo, com um carrossel interativo para visualização, ampliação e exclusão de imagens, tudo armazenado em base64 e enviado ao servidor.
- **Integração SOAP:** Comunicação eficiente com um serviço web SOAP para envio de dados de inspeção ao ERP, garantindo sincronização em tempo real com sistemas corporativos.
- **Design Responsivo:** Estilização moderna com CSS personalizado e Bootstrap, assegurando uma experiência consistente em desktops e dispositivos móveis, com ajustes dinâmicos de altura usando variáveis CSS.
- **Validações e Feedback:** Lógica sofisticada para validar quantidades (inspecionadas, refugo e conserto) e fornecer alertas ao usuário, prevenindo erros durante o processo.

![Video de Apresentacao](/Screenshots-Video/DropDown.gif)

## 🛠️ Tecnologias Utilizadas
- **Back-end:** PHP com classes orientadas a objetos para consultas ao banco de dados (SQL Server via sqlsrv) e integração SOAP.
- **Front-end:** HTML5, CSS3 (com media queries para responsividade) e JavaScript puro para interatividade e manipulação do DOM.
- **Bibliotecas:** Bootstrap 5 para componentes visuais e Instascan para leitura de QR codes em dispositivos móveis.
- **Persistência:** Uso de sessionStorage e $_SESSION para gerenciamento de estado entre páginas.

## 🔄 Futuras Melhorias (em desenvolvimento)
- Geração automática de relatórios analíticos com gráficos de defeitos por setor.
- Implementação de fluxo para revisão e aprovação de inspeções realizadas.
- Melhoria na usabilidade do carrossel de imagens, permitindo edição e marcação de áreas defeituosas.
- Migração do projeto para React, preparando o sistema para integração futura em uma plataforma mais ampla

## 📞 Contato
Se tiver alguma dúvida ou quiser saber mais sobre meu trabalho, entre em contato via jesuinodeoliveira97@gmail.com ou pelo meu LinkedIn www.linkedin.com/in/gabriel-j-2157671a1.

---
Feito por Gabriel Jesuino de Oliveira

