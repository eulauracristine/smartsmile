// *** IMPORTANTE: AJUSTE ESTA URL CONFORME SEU CAMINHO NO SERVIDOR ***
const API_URL = '/SmartSmile/assets/api/auth.php'; 

// -------------------------------------------------------------
// FUNÇÕES DE UTILIDADE E MÁSCARA
// -------------------------------------------------------------

function showApiMessage(text, isSuccess) {
    const apiMessage = document.getElementById('apiMessage');
    if (!apiMessage) return;
    
    apiMessage.textContent = text;
    apiMessage.className = 'alert-custom ' + (isSuccess ? 'text-success' : 'text-danger');
    apiMessage.style.display = 'block';
    setTimeout(() => {
        apiMessage.style.display = 'none';
    }, 4000);
}

function maskCPF(value) {
    return value
        .replace(/\D/g, '')
        .replace(/(\d{3})(\d)/, '$1.$2')
        .replace(/(\d{3})(\d)/, '$1.$2')
        .replace(/(\d{3})(\d{1,2})/, '$1-$2')
        .replace(/(-\d{2})\d+?$/, '$1');
}

function maskTelefone(value) {
    value = value.replace(/\D/g, '');
    value = value.replace(/^(\d{2})(\d)/g, '($1) $2');
    
    if (value.length > 14) {
        value = value.replace(/(\d{5})(\d)/, '$1-$2');
        value = value.slice(0, 15);
    } else {
        value = value.replace(/(\d{4})(\d)/, '$1-$2');
    }
    return value;
}

// LENDO O ID DO USUÁRIO LOGADO SALVO NO LOCALSTORAGE (CRÍTICO)
function getUserId() {
    const userId = localStorage.getItem('userId');
    
    if (!userId) {
        window.location.href = 'index.html'; 
        return null; 
    }
    return userId;
}

// -------------------------------------------------------------
// HTML DO CONTEÚDO (Strings injetadas)
// -------------------------------------------------------------

const DASHBOARD_HTML = `
    <h1 class="h3 mb-3"><strong>Bem-vindo</strong> ao seu painel</h1>
    <div class="row stats-cards">

        <div class="col-sm-6 col-xl-3">
            <div class="card">
                <div class="card-body text-center">
                    <i data-feather="calendar" class="icon-card"></i>
                    <h5 class="card-title">Próxima Consulta</h5>
                    <h3>12/11/2025</h3>
                    <p>Dr. João • Limpeza Dental</p>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card">
                <div class="card-body text-center">
                    <i data-feather="check-circle" class="icon-card"></i>
                    <h5 class="card-title">Consultas Realizadas</h5>
                    <h3>08</h3>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card">
                <div class="card-body text-center">
                    <i data-feather="credit-card" class="icon-card"></i>
                    <h5 class="card-title">Pagamentos Pendentes</h5>
                    <h3>R$ 0,00</h3>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card">
                <div class="card-body text-center">
                    <i data-feather="message-circle" class="icon-card"></i>
                    <h5 class="card-title">Mensagens da Clínica</h5>
                    <h3>2</h3>
                </div>
            </div>
        </div>

    </div>
`;

const PERFIL_HTML = `
    <h1 class="h3 mb-3"><strong>Editar</strong> Meu Perfil</h1>
    <div class="card card-perfil">
        <div class="card-body">
            <form id="profileForm">
                <input type="hidden" id="userId" name="id">

                <div class="mb-3">
                    <label for="nome" class="form-label">Nome Completo</label>
                    <input type="text" class="form-control" id="nome" name="nome" required readonly disabled>
                </div>
                
                <div class="mb-3">
                    <label for="email" class="form-label">E-mail</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="cpf" class="form-label">CPF</label>
                        <input type="text" class="form-control" id="cpf" name="cpf" required readonly disabled>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="telefone" class="form-label">Telefone</label>
                        <input type="tel" class="form-control" id="telefone" name="telefone" required minlength="14" maxlength="15">
                    </div>
                </div>

                <div class="mb-4">
                    <label for="dataNascimento" class="form-label">Data de Nascimento</label>
                    <input type="date" class="form-control" id="dataNascimento" name="data_nascimento" required readonly disabled>
                </div>

                <button type="submit" class="btn btn-update w-100">Atualizar Dados</button>
                <div id="apiMessage" class="alert-custom" style="display: none;"></div>
            </form>
        </div>
    </div>
`;

// -------------------------------------------------------------
// LÓGICA DE NAVEGAÇÃO E INJEÇÃO
// -------------------------------------------------------------

function showSection(sectionName, clickedElement = null) {
    const userId = getUserId();
    if (!userId) return;

    const contentWrapper = document.getElementById('contentWrapper');
    if (!contentWrapper) return;

    // 1. Limpa e injeta o HTML
    contentWrapper.innerHTML = '';
    
    switch (sectionName) {
        case 'perfil':
            contentWrapper.innerHTML = PERFIL_HTML;
            loadProfile(userId); 
            break;
        case 'inicio':
        case 'dashboard':
            contentWrapper.innerHTML = DASHBOARD_HTML;
            if (typeof feather !== 'undefined') {
                 feather.replace(); 
            }
            break;
        default:
            contentWrapper.innerHTML = `<h1 class="h3 mb-3"><strong>${sectionName.toUpperCase()}</strong></h1><p>Conteúdo da seção ${sectionName} aqui.</p>`;
    }

    // 2. Atualiza o menu ativo
    document.querySelectorAll('#sidebarNavMenu .sidebar-item').forEach(item => {
        item.classList.remove('active');
    });
    if (clickedElement) {
        clickedElement.closest('.sidebar-item').classList.add('active');
    }
}


// -------------------------------------------------------------
// LÓGICA DE PERFIL (READ/UPDATE)
// -------------------------------------------------------------

async function loadProfile(userId) {
    if (!userId) return;

    document.getElementById('profileForm').addEventListener('submit', handleProfileUpdate);
    document.getElementById('telefone').addEventListener('input', (e) => { e.target.value = maskTelefone(e.target.value); });


    try {
        const response = await fetch(`${API_URL}?action=read_profile&id=${userId}`, { method: 'GET' });
        const result = await response.json();

        if (result.success) {
            const user = result.user;
            
            // Preenche os campos
            document.getElementById('userId').value = user.id_usuario;
            document.getElementById('nome').value = user.nome;
            document.getElementById('email').value = user.email;
            document.getElementById('cpf').value = maskCPF(user.cpf); 
            document.getElementById('telefone').value = maskTelefone(user.telefone);
            document.getElementById('dataNascimento').value = user.data_nascimento;
        } else {
            showApiMessage(result.message || "Não foi possível carregar os dados do perfil.", false);
        }
    } catch (error) {
        console.error('Erro ao carregar perfil:', error);
        showApiMessage("Falha na comunicação com o servidor.", false);
    }
}

async function handleProfileUpdate(e) {
    e.preventDefault();

    const userId = document.getElementById('userId').value;
    const nome = document.getElementById('nome').value; 
    const email = document.getElementById('email').value.trim();
    const telefoneRaw = document.getElementById('telefone').value;

    const data = {
        action: 'update_profile',
        id: userId,
        email: email,
        telefone: telefoneRaw.replace(/\D/g, ''),
    };

    try {
        const response = await fetch(API_URL, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });

        const result = await response.json();
        
        if (result.success) {
            showApiMessage("Dados atualizados com sucesso!", true);
            localStorage.setItem('userName', nome); 
            document.querySelector('#userNameHeader').textContent = `Olá, ${nome.split(' ')[0]} 👋`;
        } else {
            showApiMessage(result.message || "Erro ao salvar dados.", false);
        }

    } catch (error) {
        console.error('Erro ao salvar dados:', error);
        showApiMessage("Falha na comunicação com o servidor.", false);
    }
}


// -------------------------------------------------------------
// INICIALIZAÇÃO
// -------------------------------------------------------------

document.addEventListener('DOMContentLoaded', () => {
    // 1. Verifica sessão e atualiza o cabeçalho
    const userId = getUserId(); 
    const userName = localStorage.getItem('userName') || 'Paciente';
    const userNameHeader = document.querySelector('#userNameHeader');
    
    if (userNameHeader) {
        userNameHeader.textContent = `Olá, ${userName.split(' ')[0]} 👋`;
    }
    
    // 2. Garante que a seção inicial (Dashboard) seja carregada APENAS SE TIVER ID
    const initialLink = document.querySelector('[data-section="inicio"]');
    if (initialLink && userId) {
        showSection('inicio', initialLink); 
    }
});