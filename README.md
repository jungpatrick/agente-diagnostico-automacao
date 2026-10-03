# YARA — Agente de Diagnóstico de Automações com IA

A **YARA** é uma agente de inteligência artificial desenvolvida para identificar oportunidades de automação em processos empresariais.

Por meio de uma entrevista guiada, coleta informações sobre a empresa, suas atividades, ferramentas utilizadas e principais dificuldades. Com base nas respostas, gera um relatório personalizado em PDF com oportunidades de automação e envia o documento por e-mail ao visitante, com uma cópia para o responsável pelo projeto.

O projeto demonstra a integração de IA generativa, automação de workflows, geração de documentos, envio de e-mails e mecanismos de controle de acesso e utilização.

## Funcionalidades

* **Entrevista guiada por IA:** coleta informações relevantes sobre a empresa e seus processos.
* **Diagnóstico personalizado:** identifica oportunidades de automação a partir das informações fornecidas pelo visitante.
* **Geração de relatório em PDF:** transforma o diagnóstico em um documento personalizado.
* **Envio automático por e-mail:** encaminha o relatório ao visitante e uma cópia para o responsável pelo projeto.
* **Controle de acesso no servidor:** valida as senhas sem expor os valores reais no código do navegador.
* **Limites de utilização:** permite um relatório por senha de uso normal e até cinco relatórios por dia para a senha mestra.
* **Controle do fluxo de conversa:** orienta a interação para o diagnóstico e encerra o atendimento quando o visitante insiste em conversas fora do fluxo definido, após três ocorrências.
* **Proteção do backend:** utiliza um proxy PHP para intermediar as requisições entre a interface e os webhooks do n8n.

## Tecnologias utilizadas

| Tecnologia             | Aplicação no projeto                              |
| ---------------------- | ------------------------------------------------- |
| HTML, CSS e JavaScript | Interface do usuário                              |
| n8n                    | Orquestração dos workflows e lógica do agente     |
| OpenAI                 | Interpretação das respostas e geração de conteúdo |
| PDFShift               | Conversão do relatório HTML em PDF                |
| Gmail                  | Envio dos relatórios por e-mail                   |
| PHP e cURL             | Proxy entre o navegador e o backend               |
| n8n Data Tables        | Persistência dos registros de relatórios          |

## Arquitetura

O projeto separa a interface pública da lógica de processamento e das integrações externas.

```text
┌──────────────────────────────┐
│       Interface Web          │
│        HTML / CSS / JS       │
└──────────────┬───────────────┘
               │
               ▼
┌──────────────────────────────┐
│          Proxy PHP           │
│   Autenticação e requisições │
└──────────────┬───────────────┘
               │
               ▼
┌──────────────────────────────┐
│             n8n              │
│                              │
│  Validação de acesso         │
│           ↓                  │
│  Controle do fluxo           │
│           ↓                  │
│  Agente de IA + memória      │
│           ↓                  │
│  Geração do relatório        │
└──────────────┬───────────────┘
               │
       ┌───────┼────────┐
       ▼       ▼        ▼
    OpenAI  PDFShift   Gmail
               │
               ▼
        Relatório em PDF
               │
               ▼
       Registro no n8n
         Data Tables
```

O workflow principal gerencia a conversa e as regras de acesso. A geração do relatório é executada por um workflow separado, chamado pelo agente como ferramenta. Essa divisão organiza as responsabilidades e facilita a manutenção das integrações.

## Controle de acesso e utilização

O projeto implementa regras de uso para reduzir abusos e controlar o consumo de recursos externos.

* **Senha normal:** permite a geração de um único relatório por senha, com validação no servidor.
* **Senha mestra:** permite até cinco relatórios por dia, com limite controlado no servidor.
* **Validação fora do navegador:** as senhas não ficam armazenadas no JavaScript público da interface.
* **Condução da conversa:** o agente prioriza as perguntas necessárias ao diagnóstico e encerra o atendimento após três insistências em conversas fora do fluxo estabelecido.
* **Separação entre interface e backend:** o navegador se comunica com o proxy PHP, que encaminha as requisições aos endpoints configurados.

Esses mecanismos complementam a proteção do projeto. Em uma implantação real, também é importante configurar HTTPS, limitar requisições abusivas e proteger credenciais, endpoints e registros de utilização.

## Estrutura do repositório

```text
agente-diagnostico-automacao/
├── index.html
├── README.md
├── imgs/
│   └── Yara.webp
├── workflows/
│   ├── Agente_YARA_Chat.json
│   └── Agente_YARA_Gerar_Relatorio.json
└── api/
    └── yara/
        ├── auth.php
        ├── chat.php
        └── config.example.php
```

## Como executar o projeto

A execução completa exige um servidor PHP com suporte a cURL e uma instância do n8n configurada.

### 1. Configurar os workflows no n8n

1. Importe os arquivos JSON disponíveis na pasta `workflows/`.
2. Configure as credenciais necessárias para OpenAI, PDFShift e Gmail.
3. Crie a Data Table `relatorios-yara`, com as colunas utilizadas pelo workflow para registrar os relatórios.
4. Vincule o workflow de geração de relatórios ao agente como ferramenta.
5. Configure as URLs permitidas para acesso aos webhooks, as regras de autenticação, os limites de utilização e o endereço de e-mail interno.
6. Revise as configurações de persistência e ative os workflows.

Os nomes das tabelas, os campos e os nós utilizados devem corresponder aos arquivos exportados incluídos no repositório.

### 2. Configurar o proxy PHP

1. Copie `api/yara/config.example.php` para `api/yara/config.php`.
2. Configure no arquivo local os endereços dos endpoints do n8n necessários ao funcionamento da aplicação.
3. Publique os arquivos da interface e da pasta `api/` em um servidor com PHP e cURL.
4. Configure HTTPS e verifique se o proxy aceita somente os destinos previstos.
5. Confirme que o arquivo `config.php` não será enviado ao GitHub.

### 3. Configurar a interface

Verifique os caminhos relativos dos arquivos, incluindo o avatar em `imgs/Yara.webp`, e confirme se as requisições da interface correspondem às rotas disponibilizadas pelo proxy PHP.

## Segurança e publicação

Este repositório contém uma versão sanitizada do projeto, preparada para apresentação pública e estudo técnico.

Este repositório contém uma versão sanitizada do projeto. Ao configurar ou adaptar a aplicação:

* Não inclua senhas reais, tokens, credenciais ou arquivos de configuração privados.
* Utilize `config.example.php` como modelo e mantenha `config.php` fora do controle de versão.
* Revise os arquivos JSON dos workflows antes de novos commits ou exportações.
* Não publique identificadores internos, URLs privadas, dados pessoais ou registros de execução desnecessários.
* Proteja os endpoints e aplique controles adicionais de requisições conforme o ambiente de implantação.
* Lembre-se de que ocultar a URL do n8n por meio de um proxy não substitui autenticação, autorização ou validação no servidor.

O arquivo `.gitignore` deve proteger os arquivos privados e outros dados que não devem ser versionados.

## Objetivos técnicos demonstrados

Este projeto reúne conhecimentos de:

* Desenvolvimento de interfaces web.
* Engenharia de prompts e integração com IA generativa.
* Automação de processos com workflows.
* Integração de APIs e serviços externos.
* Geração dinâmica de documentos PDF.
* Comunicação automatizada por e-mail.
* Desenvolvimento de endpoints e proxy com PHP.
* Controle de acesso, limites de utilização e validações no servidor.
* Organização de componentes e separação de responsabilidades.

## Sobre o projeto

A YARA foi concebida como uma demonstração prática de como agentes de IA podem ser integrados a processos empresariais para coletar informações, estruturar diagnósticos e entregar resultados concretos por meio de automações.

O projeto faz parte do portfólio de desenvolvimento web e soluções com inteligência artificial.

