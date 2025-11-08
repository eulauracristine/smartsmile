<?php
include 'conexao.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // segurança

    $check = "SELECT * FROM tbUsuario WHERE id_usuario = $id AND status_usuario = 0";
    $result = $conn->query($check);

    if ($result->num_rows > 0) {
        // Se for paciente, deleta
        $sql = "DELETE FROM tbUsuario WHERE id_usuario = $id";

        if ($conn->query($sql) === TRUE) {
            header("Location: pacientes.php?msg=deletado");
            exit();
        } else {
            echo "Erro ao deletar paciente: " . $conn->error;
        }
    } else {
        echo "Usuário não encontrado ou não é paciente.";
    }
} else {
    header("Location: pacientes.php");
    exit();
}
?>
