<?php
// CRÍTICO: Inicia a sessão PHP para armazenar o ID do usuário
session_start();

// Suas credenciais de conexão
$conn = new mysqli("localhost", "root", "Js.27112022", "bdSmartSmile"); 

if ($conn->connect_error) {
    // É recomendado um tratamento de erro mais amigável em produção
    die("Erro na conexão: " . $conn->connect_error);
}

$email = $_POST['email'];
$senha = $_POST['senha'];

// A query busca o ID, nome e senha para autenticação e dados de sessão
$sql = "SELECT u.id_usuario, u.nome, u.senha, 
                a.id_administrador, 
                d.id_dentista 
        FROM tbUsuario u
        LEFT JOIN tbAdministrador a ON a.id_administrador = u.id_usuario
        LEFT JOIN tbDentista d ON d.id_dentista = u.id_usuario
        WHERE u.email = '$email'";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();

    if ($senha === $user['senha']) {
        
        
        $_SESSION['id_usuario'] = $user['id_usuario'];
        $_SESSION['nome_usuario'] = $user['nome']; // Salva o nome para exibição (Opcional)

        if (!is_null($user['id_administrador'])) { 
            header("Location: dashboard_adm.html");
        } elseif (!is_null($user['id_dentista'])) {
            header("Location: dashboard_dentista.html");
        } else {
            header("Location: dashboard_paciente.html");
        }
        exit();
    } else {
        echo "<script>alert('Senha incorreta!'); window.history.back();</script>";
    }
} else {
    echo "<script>alert('Usuário não encontrado!'); window.history.back();</script>";
}

$conn->close();
?>