const API_URL = '/SmartSmile/assets/api/auth.php'; 

// Função para mostrar a mensagem customizada, substituindo alert()
function showMessage(text) {
    const messageBox = document.getElementById('custom-message-box');
    const messageText = document.getElementById('custom-message-text');

    if (!messageBox) {
        console.error("Elemento custom-message-box não encontrado. Usando console.log.");
        console.log("Mensagem: " + text);
        return;
    }

    messageText.textContent = text;
    messageBox.classList.add('active');
}

// Função de máscara de CPF (XXX.XXX.XXX-XX)
function maskCPF(value) {
    return value
        .replace(/\D/g, '') // Remove tudo o que não é dígito
        .replace(/(\d{3})(\d)/, '$1.$2') // Coloca ponto após o 3º dígito
        .replace(/(\d{3})(\d)/, '$1.$2') // Coloca ponto após o 6º dígito
        .replace(/(\d{3})(\d{1,2})/, '$1-$2') // Coloca hífen após o 9º dígito
        .replace(/(-\d{2})\d+?$/, '$1'); // Garante que tenha no máximo 11 dígitos
}


// EVENTOS DE MÁSCARA
document.addEventListener('DOMContentLoaded', () => {
    const inputCpf = document.getElementById('signUpCpf');
    const inputTelefone = document.getElementById('signUpTelefone');

    if (inputCpf) {
        inputCpf.addEventListener('input', (e) => {
            e.target.value = maskCPF(e.target.value);
        });
    }
    if (inputTelefone) {
        inputTelefone.addEventListener('input', (e) => {
            e.target.value = maskTelefone(e.target.value);
        });
    }
});


// LÓGICA DE SLIDE
const signUpBtn = document.querySelector("#sign-up-btn");
const signInBtn = document.querySelector("#sign-in-btn");
const container = document.querySelector(".container-login");
const signInForm = document.querySelector(".sign-in-form");
const signUpForm = document.querySelector(".sign-up-form");

if (signUpBtn && container) {
    signUpBtn.addEventListener("click", (e) => {
        e.preventDefault();
        container.classList.add("sign-up-mode");
    });
}

if (signInBtn && container) {
    signInBtn.addEventListener("click", (e) => {
        e.preventDefault();
        container.classList.remove("sign-up-mode");
    });
}

// -------------------------------------------------------------
// LÓGICA DE CADASTRO (SIGN UP)
// -------------------------------------------------------------
if (signUpForm) {
    signUpForm.addEventListener("submit", async (e) => {
        e.preventDefault();

        // Coleta os valores do formulário
        const nome = document.getElementById('signUpNome').value.trim();
        const email = document.getElementById('signUpEmail').value.trim();
        const senha = document.getElementById('signUpSenha').value;
        const confirmSenha = document.getElementById('signUpConfirmaSenha').value;
        const cpfRaw = document.getElementById('signUpCpf').value;
        const telefoneRaw = document.getElementById('signUpTelefone').value;
        const dataNascimento = document.getElementById('signUpDataNascimento').value;

        // Limpa CPF e Telefone de máscaras para enviar ao PHP
        const cpf = cpfRaw.replace(/\D/g, ''); 
        const telefone = telefoneRaw.replace(/\D/g, '');

        if (senha !== confirmSenha) {
            showMessage("As senhas não coincidem!");
            return;
        }

        const data = {
            action: 'register',
            nome,
            email,
            senha,
            cpf, // 11 dígitos limpos
            telefone, // 10 ou 11 dígitos limpos
            data_nascimento: dataNascimento
        };

        try {
            const response = await fetch(API_URL, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });

            if (!response.ok) {
                try {
                    const errorData = await response.json();
                    showMessage(`Erro do Servidor (${response.status}): ${errorData.message || 'Erro desconhecido.'}`);
                } catch (e) {
                    showMessage(`Erro do Servidor: ${response.statusText || 'Não foi possível processar a resposta do servidor.'}`);
                }
                return;
            }

            const result = await response.json();

            if (result.success) {
                // Redirecionamento direto para o painel do paciente após o cadastro
                showMessage("Cadastro concluído com sucesso! Redirecionando para o seu painel...");

                setTimeout(() => {
                    window.location.href = 'dashboard_paciente.html'; 
                }, 1500);

            } else {
                showMessage(result.message || "Erro desconhecido ao cadastrar.");
            }

        } catch (error) {
            console.error('Erro na requisição de cadastro:', error);
            showMessage("Não foi possível conectar ao servidor. Verifique a URL da API.");
        }
    });
}



// LÓGICA DE LOGIN (SIGN IN)
if (signInForm) {
    signInForm.addEventListener("submit", async (e) => {
        e.preventDefault();

        const email = document.getElementById('signInEmail').value.trim();
        const senha = document.getElementById('signInSenha').value;
        
        const data = {
            action: 'login',
            email,
            senha
        };

        try {
            const response = await fetch(API_URL, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });

            if (!response.ok) {
                try {
                    const errorData = await response.json();
                    showMessage(`Erro do Servidor (${response.status}): ${errorData.message || 'Erro desconhecido.'}`);
                } catch (e) {
                    showMessage(`Erro do Servidor: ${response.statusText || 'Não foi possível processar a resposta do servidor.'}`);
                }
                return;
            }

            const result = await response.json();

            if (result.success) {
                const role = result.role || 'paciente'; 
                
                showMessage(`Login bem-sucedido! Acesso como ${role}. Redirecionando...`);

                let redirectUrl;
                switch (role) {
                    case 'administrador':
                        redirectUrl = 'dashboard_administrador.html';
                        break;
                    case 'dentista':
                        redirectUrl = 'dashboard_dentista.html';
                        break;
                    case 'paciente':
                    default:
                        redirectUrl = 'dashboard_paciente.html';
                        break;
                }

                setTimeout(() => {
                    window.location.href = redirectUrl; 
                }, 1500);

            } else {
                showMessage(result.message || "Erro desconhecido ao fazer login.");
            }

        } catch (error) {
            console.error('Erro na requisição de login:', error);
            showMessage("Não foi possível conectar ao servidor. Verifique a URL da API.");
        }
    });
}