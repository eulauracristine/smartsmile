const API_URL = '/SmartSmile/assets/api/auth.php'; 

// -------------------------------------------------------------
// FUNÇÕES DE UTILIDADE E MÁSCARA
// -------------------------------------------------------------

function showMessage(text) {
    const messageBox = document.getElementById('custom-message-box');
    const messageText = document.getElementById('custom-message-text');

    if (!messageBox) {
        console.error("Elemento custom-message-box não encontrado. Usando alert como fallback.");
        alert(text);
        return;
    }

    messageText.textContent = text;
    messageBox.classList.add('active');
    setTimeout(() => {
        messageBox.classList.remove('active');
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

// -------------------------------------------------------------
// EVENTOS E LÓGICA DE LOGIN/CADASTRO
// -------------------------------------------------------------

document.addEventListener('DOMContentLoaded', () => {
    // Eventos de Máscara
    const inputCpf = document.getElementById('signUpCpf');
    const inputTelefone = document.getElementById('signUpTelefone');
    if (inputCpf) inputCpf.addEventListener('input', (e) => { e.target.value = maskCPF(e.target.value); });
    if (inputTelefone) inputTelefone.addEventListener('input', (e) => { e.target.value = maskTelefone(e.target.value); });

    // Lógica de Slide
    const signUpBtn = document.querySelector("#sign-up-btn");
    const signInBtn = document.querySelector("#sign-in-btn");
    const container = document.querySelector(".container-login");
    const signInForm = document.querySelector("#signInForm");
    const signUpForm = document.querySelector("#signUpForm");

    if (signUpBtn && container) signUpBtn.addEventListener("click", (e) => { e.preventDefault(); container.classList.add("sign-up-mode"); });
    if (signInBtn && container) signInBtn.addEventListener("click", (e) => { e.preventDefault(); container.classList.remove("sign-up-mode"); });

    // LÓGICA DE CADASTRO (CREATE)
    if (signUpForm) {
        signUpForm.addEventListener("submit", async (e) => {
            e.preventDefault();

            const senha = document.getElementById('signUpSenha').value;
            const confirmSenha = document.getElementById('signUpConfirmaSenha').value;
            if (senha !== confirmSenha) { showMessage("As senhas não coincidem!"); return; }

            const data = {
                action: 'register',
                nome: document.getElementById('signUpNome').value.trim(),
                email: document.getElementById('signUpEmail').value.trim(),
                senha: senha,
                cpf: document.getElementById('signUpCpf').value.replace(/\D/g, ''), 
                telefone: document.getElementById('signUpTelefone').value.replace(/\D/g, ''), 
                data_nascimento: document.getElementById('signUpDataNascimento').value
            };

            try {
                const response = await fetch(API_URL, { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(data) });
                const result = await response.json();

                if (result.success) {
                    showMessage("Cadastro concluído com sucesso! Faça login para continuar.");
                    // Voltando para a tela de Login
                    setTimeout(() => { container.classList.remove("sign-up-mode"); signUpForm.reset(); }, 2000);
                } else {
                    showMessage(result.message || "Erro desconhecido ao cadastrar.");
                }
            } catch (error) { 
                console.error('Erro na requisição de cadastro:', error);
                showMessage("Não foi possível conectar ao servidor. Verifique a URL da API."); 
            }
        });
    }

    // LÓGICA DE LOGIN (READ)
    if (signInForm) {
        signInForm.addEventListener("submit", async (e) => {
            e.preventDefault();
            
            const data = {
                action: 'login',
                email: document.getElementById('signInEmail').value.trim(),
                senha: document.getElementById('signInSenha').value
            };

            try {
                const response = await fetch(API_URL, { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(data) });
                const result = await response.json();

                if (result.success) {
                    const role = result.role || 'paciente'; 
                    
                    // Salvando dados do usuário no localStorage
                    localStorage.setItem('userId', result.id);
                    localStorage.setItem('userName', result.name); 
                    localStorage.setItem('userRole', role);
                    
                    showMessage(`Login bem-sucedido! Acesso como ${role}. Redirecionando...`);

                    let redirectUrl;
                    switch (role) {
                        case 'administrador': redirectUrl = 'dashboard_administrador.html'; break;
                        case 'dentista': redirectUrl = 'dashboard_dentista.html'; break;
                        case 'paciente':
                        default: redirectUrl = 'dashboard_paciente.html'; break;
                    }

                    setTimeout(() => { window.location.href = redirectUrl; }, 1500);
                } else {
                    showMessage(result.message || "E-mail ou senha incorretos.");
                }
            } catch (error) { 
                console.error('Erro na requisição de login:', error);
                showMessage("Não foi possível conectar ao servidor. Verifique a URL da API."); 
            }
        });
    }
});