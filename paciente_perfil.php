<?php
session_start();
include('conexao.php');

// Verifica se o usuário está logado
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

$id_usuario = $_SESSION['id_usuario'];

// Busca os dados do usuário
$sql = "SELECT nome, email, telefone, data_nascimento, cpf FROM tbUsuario WHERE id_usuario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$result = $stmt->get_result();
$usuario = $result->fetch_assoc();

$stmt->close();
$conn->close();
?>

<!-- CONTEÚDO DA PÁGINA -->
<div class="container-perfil">
    <h2>Meu Perfil</h2>
    <p>Atualize suas informações pessoais abaixo.</p>

    <form action="atualizar_perfil.php" method="POST" class="form-perfil">
        <input type="hidden" name="id_usuario" value="<?php echo $id_usuario; ?>">

        <div class="campo-form">
            <label for="nome">Nome completo</label>
            <input type="text" id="nome" name="nome" value="<?php echo htmlspecialchars($usuario['nome']); ?>" required>
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
            <label for="cpf">CPF</label>
            <input type="text" id="cpf" name="cpf" value="<?php echo htmlspecialchars($usuario['cpf']); ?>" disabled>
        </div>

        <div class="campo-form">
            <label for="data_nascimento">Data de Nascimento</label>
            <input type="date" id="data_nascimento" name="data_nascimento" value="<?php echo htmlspecialchars($usuario['data_nascimento']); ?>" required>
        </div>

        <div class="campo-form">
            <label for="senha">Nova Senha (opcional)</label>
            <input type="password" id="senha" name="senha" placeholder="••••••">
        </div>

        <div class="botoes-form">
            <button type="submit" class="btn-salvar">Salvar Alterações</button>
        </div>
    </form>
</div>

<style>
/* ---------- ESTILO UNIFICADO PARA DENTRO DA DASH ---------- */
.container-perfil {
    background-color: #fff;
    padding: 30px;
    border-radius: 15px;
    box-shadow: 0 0 10px rgba(0,0,0,0.08);
    max-width: 600px;
    margin: 40px auto;
}

.container-perfil h2 {
    font-size: 1.8rem;
    color: #271753;
    margin-bottom: 10px;
}

.container-perfil p {
    color: #666;
    margin-bottom: 25px;
}

.form-perfil .campo-form {
    margin-bottom: 15px;
}

.form-perfil label {
    display: block;
    font-weight: 600;
    color: #271753;
    margin-bottom: 6px;
}

.form-perfil input {
    width: 100%;
    padding: 10px 12px;
    border: 1px solid #ccc;
    border-radius: 8px;
    font-size: 15px;
}

.form-perfil input:disabled {
    background-color: #f2f2f2;
}

.botoes-form {
    text-align: center;
    margin-top: 25px;
}

.btn-salvar {
    background-color: #b084cc;
    color: #fff;
    padding: 10px 25px;
    border: none;
    border-radius: 8px;
    font-weight: bold;
    cursor: pointer;
    transition: 0.2s;
}

.btn-salvar:hover {
    background-color: #271753;
}
</style>
