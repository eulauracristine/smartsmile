<?php
include('conexao.php');
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_usuario      = $_POST['id_usuario'];
    $nome            = $_POST['nome'];
    $email           = $_POST['email'];
    $telefone        = $_POST['telefone'];
    $data_nascimento = $_POST['data_nascimento'];
    $senha           = $_POST['senha'];

    // Atualiza a senha apenas se o campo não estiver vazio
    if (!empty($senha)) {
        $sql = "UPDATE tbUsuario 
                SET nome=?, email=?, telefone=?, data_nascimento=?, senha=? 
                WHERE id_usuario=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssi", $nome, $email, $telefone, $data_nascimento, $senha, $id_usuario);
    } else {
        $sql = "UPDATE tbUsuario 
                SET nome=?, email=?, telefone=?, data_nascimento=? 
                WHERE id_usuario=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssi", $nome, $email, $telefone, $data_nascimento, $id_usuario);
    }

    if ($stmt->execute()) {
        echo "<script>
                alert('Perfil atualizado com sucesso!');
                window.location.href = 'dashboard_paciente.html';
              </script>";
    } else {
        echo "<script>
                alert('Erro ao atualizar o perfil. Tente novamente.');
                history.back();
              </script>";
    }

    $stmt->close();
    $conn->close();
}
?>
