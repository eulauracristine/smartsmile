# 🦷 SmartSmile - Sistema de Gestão Odontológica

Este projeto implementa a solução digital integrada para a **Clínica Odontológica Smart Smile**, visando modernizar os processos administrativos e clínicos. O foco inicial é no módulo de **Autenticação e Perfil (CRUD)**, utilizando uma arquitetura **PHP/MySQL** com chamadas **AJAX** para a comunicação com o frontend.

---

## 🌟 Visão Geral e Benefícios

O objetivo principal do projeto é migrar os controles manuais (agendamentos e prontuários em papel) para uma plataforma unificada e digital, garantindo maior eficiência e segurança dos dados (em conformidade com a LGPD).

| Diagnóstico Atual | Benefícios Implementados |
| :--- | :--- |
| Agendamentos marcados apenas pelo WhatsApp. | O sistema permite **agendamento de consultas online**, aumentando a organização e a produtividade. |
| Prontuários e históricos mantidos em papel. | **Registro digital** de todos os atendimentos, reduzindo o risco de perda de informações. |
| Controle manual de pagamentos e desistências. | Envio automático de lembretes (SMS/WhatsApp/e-mail) e gestão digital dos pagamentos. |

---

## 🔒 Módulo de Autenticação e CRUD (Requisitos Funcionais)

O sistema possui diferentes níveis de acesso: `administrativo`, `dentista` e `paciente`.

| Módulo | Operação CRUD | Requisito Funcional |
| :--- | :--- | :--- |
| **Login/Cadastro** | **C**reate (Cadastro) | Novo paciente registra conta, cumprindo a RN001 (CPF válido e único). |
| **Login/Autenticação** | **R**ead (Login) | Verifica credenciais e redireciona o usuário para o dashboard correto (`dashboard_paciente.html`, `dashboard_dentista.html`, etc.). |
| **Meu Perfil (Paciente)** | **R**ead (Carregar Dados) | Busca e exibe dados pessoais e de contato do paciente logado. |
| **Meu Perfil (Paciente)** | **U**pdate (Atualização) | Permite ao paciente alterar **E-mail** e **Telefone**. (Nome, CPF e Data de Nascimento são somente leitura). |
| **Dashboard ADM** | **D**elete (Excluir) | Administrador pode excluir dados cadastrais de pacientes (conforme RN004, que permite ao ADM editar/excluir). |
| **Logout** | — | Encerra a sessão, limpando o `localStorage` do navegador. |

### Regras de Negócio (RN) Relevantes:

* **RN001:** Cada paciente deve possuir um CPF válido e único no cadastro.
* **RN004:** Somente usuários com perfil de administrador podem excluir ou editar dados cadastrais de pacientes.
* **RN008:** O prontuário do paciente deve ser preenchido imediatamente após o atendimento.

---

## ⚙️ Configuração e Instalação Local (XAMPP)

Para rodar este projeto em seu computador, siga os passos de configuração do ambiente:

### 1. Preparação do Ambiente

1.  Instale e inicie os serviços **Apache** e **MySQL** no seu XAMPP Control Panel.
2.  Clone ou extraia o conteúdo deste projeto para a pasta `C:\xampp\htdocs\` e nomeie a pasta como **`SmartSmile`**.

### 2. Configuração do Banco de Dados

1.  Acesse o **phpMyAdmin** (geralmente via `http://localhost/phpmyadmin`).
2.  Crie um novo banco de dados chamado **`bdSmartSmile`**.
3.  Execute o script SQL para criar as tabelas necessárias (`tbUsuario`, `tbAdministrador`, `tbDentista`, etc.).

### 3. Ajuste da Conexão PHP

Configure as credenciais do MySQL no arquivo de conexão:

* Edite o arquivo `assets/api/db_connect.php` e insira sua senha do MySQL (se houver).

### 4. Ajuste da URL da API (JavaScript)

O JavaScript precisa do caminho correto para o backend. Certifique-se de que a `API_URL` esteja correta em seus arquivos `.js` (ex: `assets/js/login.js`):

```javascript
// A URL deve refletir o nome da sua pasta no htdocs
const API_URL = '/SmartSmile/assets/api/auth.php';

5. Execução do Projeto
Abra seu navegador e acesse a tela de login:

http://localhost/SmartSmile/login.html

Equipe

Líder/Documentação	Yasmin Julia Oliveira da Silva	
Desenvolvedora	Laura Cristine Silva	
Desenvolvedora	Maria Eduarda Silva Souza	
Analista	Manuela de Almeida Gonçalves	
Analista/Testadora	Geovanna Silva Laurentino
Analista/Testador	Bruno Macedo Medrades