<?php
include('conexao.php');
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_usuario      = $_POST['id_usuario'];
    $nome            = $_POST['nome'];
    $email           = $_POST['email'];
    $telefone        = $_POST['telefone'];
    $data_nascimento = $_POST['data_nascimento'];
    $senha           = $_POST['senha']; // senha simples, máximo 6 caracteres

    // ⚠️ Garante que a senha tenha até 6 caracteres
    if (strlen($senha) > 6) {
        echo "<script>alert('A senha deve ter no máximo 6 caracteres!'); history.back();</script>";
        exit;
    }

    $sql = "UPDATE tbUsuario 
            SET nome=?, email=?, telefone=?, data_nascimento=?, senha=? 
            WHERE id_usuario=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssi", $nome, $email, $telefone, $data_nascimento, $senha, $id_usuario);

    if ($stmt->execute()) {
        echo "<script>alert('Perfil atualizado com sucesso!'); window.location='paciente_perfil.php';</script>";
    } else {
        echo "<script>alert('Erro ao atualizar o perfil.'); history.back();</script>";
    }

    $stmt->close();
    $conn->close();
}



?>
