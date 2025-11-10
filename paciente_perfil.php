<?php
session_start();
include('conexao.php'); // Assumindo que este arquivo estabelece $conn

// 1. Verifica se o usuário está logado
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

// Obtém o ID do usuário logado
$id_usuario = (int)$_SESSION['id_usuario'];

// Busca os dados do usuário logado
$sql = "SELECT nome, email, telefone, data_nascimento, cpf FROM tbUsuario WHERE id_usuario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$result = $stmt->get_result();
$usuario = $result->fetch_assoc();

$stmt->close();
$conn->close();

if (!$usuario) {
    header("Location: dashboard_paciente.html");
    exit();
}
?>

<div class="container-perfil">
    <h2>Meu Perfil</h2>
    <p>Atualize suas informações pessoais abaixo.</p>

    <form action="atualizar_perfil.php" method="POST" class="form-perfil">
        <input type="hidden" name="id_usuario" value="<?php echo $id_usuario; ?>">
        
        <input type="hidden" name="nome" value="<?php echo htmlspecialchars($usuario['nome']); ?>">
        <input type="hidden" name="cpf" value="<?php echo htmlspecialchars($usuario['cpf']); ?>">
        <input type="hidden" name="data_nascimento" value="<?php echo htmlspecialchars($usuario['data_nascimento']); ?>">


        <div class="campo-form">
            <label for="nome_display">Nome completo</label>
            <input type="text" id="nome_display" value="<?php echo htmlspecialchars($usuario['nome']); ?>" required readonly disabled>
        </div>

        <div class="campo-form">
            <label for="email">E-mail</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($usuario['email']); ?>" required>
        </div>

        <div class="campo-form">
            <label for="telefone">Telefone</label>
            <input type="text" id="telefone" name="telefone" value="<?php echo htmlspecialchars($usuario['telefone']); ?>" required>
        </div>

        <div class="campo-form">
            <label for="cpf_display">CPF</label>
            <input type="text" id="cpf_display" value="<?php echo htmlspecialchars($usuario['cpf']); ?>" disabled>
        </div>

        <div class="campo-form">
            <label for="data_nascimento_display">Data de Nascimento</label>
            <input type="date" id="data_nascimento_display" value="<?php echo htmlspecialchars($usuario['data_nascimento']); ?>" required disabled>
        </div>

        <div class="campo-form">
            <label for="nova_senha">Nova Senha (opcional)</label>
            <input type="password" id="nova_senha" name="nova_senha" placeholder="••••••" maxlength="6">
        </div>

        <div class="botoes-form">
            <button type="submit" class="btn-salvar">Salvar Alterações</button>
        </div>
    </form>
</div>

<style>
/* Paleta base do projeto (Se necessário, defina as variáveis aqui) */
:root {
    --roxo-escuro: #271753;
    --lilas: #b084cc;
    --lilas-claro: #d4afed;
    --branco: #ffffff;
    --cinza-claro: #f5f5f5;
    --btn-hover: #a88cf0;
    --sombra: rgba(0, 0, 0, 0.15);
}

/* 1. Estrutura e Container (O White Box Central) */
.container-perfil {
    background-color: var(--branco);
    padding: 30px 40px; /* Mais padding nas laterais, como na imagem */
    border-radius: 15px;
    box-shadow: 0 4px 15px var(--sombra);
    max-width: 650px; /* Ligeiramente mais largo */
    margin: 40px auto; 
}

/* Títulos */
.container-perfil h2 {
    font-size: 2.2rem;
    font-weight: 700;
    color: var(--roxo-escuro);
    margin-bottom: 5px;
}

.container-perfil p {
    color: #666;
    margin-bottom: 30px; /* Aumenta a margem após a descrição */
}

/* 2. Formulário e Campos */
.form-perfil .campo-form {
    margin-bottom: 25px; /* Mais espaço entre os campos */
}

.form-perfil label {
    display: block;
    font-weight: 600;
    color: #444; /* Cor neutra para o label */
    margin-bottom: 8px;
    font-size: 0.95rem;
}

.form-perfil input {
    width: 100%;
    padding: 12px; /* Aumenta o padding vertical dos inputs */
    border: 1px solid #ccc;
    border-radius: 8px;
    font-size: 1rem;
    transition: border-color 0.2s;
}

.form-perfil input:focus {
    border-color: var(--lilas);
    outline: none;
    box-shadow: 0 0 0 1px var(--lilas-claro);
}

/* Estilo para campos somente leitura (CPF, Data de Nascimento, Nome - como na sua imagem) */
.form-perfil input:disabled, 
.form-perfil input[readonly] {
    background-color: var(--cinza-claro); /* Fundo cinza claro */
    color: #444;
    border: 1px solid #ddd;
    box-shadow: none; /* Remove qualquer sombra desnecessária */
}

/* 3. Botão Salvar (com a cor Roxa Escura do projeto) */
.botoes-form {
    text-align: center;
    margin-top: 35px;
}

.btn-salvar {
    width: 100%;
    background-color: var(--roxo-escuro);
    color: var(--branco);
    padding: 14px 25px; 
    border: none;
    border-radius: 8px;
    font-weight: bold;
    font-size: 1.05rem;
    cursor: pointer;
    transition: 0.3s;
    box-shadow: 0 4px 8px rgba(39, 23, 83, 0.3); /* Sombra roxa leve */
}

.btn-salvar:hover {
    background-color: var(--btn-hover);
    box-shadow: 0 6px 12px rgba(39, 23, 83, 0.4);
}
</style>
