# YARA — Agente de Diagnóstico de Automações

A **YARA** é uma agente de inteligência artificial desenvolvida para identificar oportunidades de automação em processos empresariais.

Por meio de uma entrevista guiada, coleta informações sobre a empresa, suas atividades, ferramentas utilizadas e principais dificuldades. A partir dessas informações, gera um relatório personalizado em PDF com oportunidades de automação e envia o documento por e-mail ao visitante.

O projeto demonstra a aplicação prática de **IA generativa, automação de workflows, integração de APIs e geração automatizada de documentos**.

## Funcionalidades

* **Entrevista guiada por IA** — coleta informações relevantes sobre a empresa e seus processos.
* **Diagnóstico personalizado** — identifica oportunidades de automação a partir das respostas do visitante.
* **Geração automática de relatório** — produz um diagnóstico estruturado em PDF.
* **Envio por e-mail** — encaminha o relatório ao visitante e uma cópia para o responsável pelo projeto.
* **Controle de acesso no servidor** — as credenciais de acesso não ficam expostas no código da interface.
* **Controle de utilização** — senha normal permite um relatório; senha mestra possui limite de cinco relatórios por dia.
* **Controle de conversação** — o agente mantém o visitante dentro do fluxo necessário para o diagnóstico e encerra o atendimento após três insistências em conversas fora do escopo.
* **Proxy PHP** — intermedia a comunicação entre a interface pública e os workflows do n8n, mantendo os endpoints de backend fora da interface.

## Arquitetura

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
│  Agente YARA                 │
│  + memória + ferramentas     │
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
       n8n Data Tables
```

O workflow responsável pela conversa gerencia a entrevista, as regras de acesso e o controle do fluxo. A geração do relatório é realizada por um workflow separado, acionado pelo agente como uma ferramenta.

Essa separação permite organizar a lógica da aplicação e as integrações externas em componentes distintos.

## Tecnologias

| Tecnologia                  | Aplicação                                     |
| --------------------------- | --------------------------------------------- |
| **HTML / CSS / JavaScript** | Interface web                                 |
| **n8n**                     | Orquestração dos workflows e lógica do agente |
| **OpenAI**                  | Inteligência artificial e geração de conteúdo |
| **PDFShift**                | Geração do PDF a partir do relatório          |
| **Gmail**                   | Envio automatizado dos relatórios             |
| **PHP / cURL**              | Proxy entre frontend e backend                |
| **n8n Data Tables**         | Registro dos relatórios gerados               |

## Fluxo do atendimento

```text
Visitante
    │
    ▼
Validação de acesso
    │
    ▼
Entrevista com YARA
    │
    ├── Resposta válida ──────────────┐
    │                                 │
    └── Conversa fora do fluxo        │
            │                         │
            ├── 1ª ocorrência ───────┤
            ├── 2ª ocorrência ───────┤
            └── 3ª ocorrência → encerra
                                      │
                                      ▼
                         10 respostas válidas
                                      │
                                      ▼
                         Geração do relatório
                                      │
                         ┌────────────┴────────────┐
                         ▼                         ▼
                    PDF gerado              Registro do relatório
                         │
                         ▼
                   Envio por e-mail
```

O agente também possui regras específicas para interpretar respostas negativas ou diferentes formatos de resposta sem classificá-las indevidamente como conversas fora do fluxo.

## Controle e segurança

O projeto utiliza mecanismos de controle para proteger o uso da aplicação e evitar exposição desnecessária de informações sensíveis.

* As senhas são validadas no **servidor**, e não na interface pública.
* A senha de uso normal permite apenas um relatório.
* A senha mestra possui limite diário de cinco relatórios.
* Os endpoints do n8n não ficam diretamente expostos na interface.
* O proxy PHP intermedia as requisições entre o navegador e o backend.
* Credenciais e configurações privadas são mantidas fora da versão pública do projeto.
* Os workflows publicados foram sanitizados para remover credenciais, identificadores internos e informações específicas do ambiente original.

## Estrutura do projeto

```text
agente-diagnostico-automacao/
├── api/
│   └── yara/
│       ├── auth.php
│       ├── chat.php
│       └── config.example.php
│
├── imgs/
│   └── Yara.webp
│
├── workflows/
│   ├── Agente_YARA_Chat.json
│   └── Agente_YARA_Gerar_Relatorio.json
│
├── index.html
├── .gitignore
└── README.md
```

## Destaques técnicos

O projeto reúne diferentes componentes em uma única aplicação:

* Desenvolvimento de interface web sem frameworks.
* Integração de IA generativa com workflows automatizados.
* Agente com fluxo conversacional controlado.
* Uso de ferramentas pelo agente para executar ações externas.
* Geração dinâmica de documentos.
* Integração com serviços de e-mail.
* Comunicação entre frontend, PHP e n8n.
* Controle de acesso e limites de utilização no servidor.
* Persistência de dados relacionados aos relatórios.
* Sanitização de workflows para publicação de código.

## Sobre o projeto

A YARA foi desenvolvida como uma demonstração prática de aplicação de inteligência artificial em processos de diagnóstico e automação empresarial.

O projeto combina **desenvolvimento web, inteligência artificial e automação de processos**, mostrando como um agente pode coletar informações, processar respostas, executar ferramentas e entregar um resultado concreto de forma automatizada.
