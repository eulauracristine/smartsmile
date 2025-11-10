<?php
session_start();
include('conexao.php'); // Assumindo $conn é a conexão

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // 1. COLETAR E SANITIZAR DADOS (Agora lendo dos campos HIDDEN e editáveis)
    $id_usuario      = $_POST['id_usuario'];
    
    // CAMPOS RECEBIDOS VIA HIDDEN:
    $nome            = $conn->real_escape_string($_POST['nome']); 
    $data_nascimento = $conn->real_escape_string($_POST['data_nascimento']); 
    
    // CAMPOS EDITÁVEIS:
    $email           = $conn->real_escape_string($_POST['email']);
    $telefone        = $conn->real_escape_string($_POST['telefone']);
    $nova_senha      = isset($_POST['nova_senha']) ? $_POST['nova_senha'] : ''; 

    // 2. CONSTRUIR A QUERY CONDICIONAL
    
    // Campos que sempre serão atualizados (Nome, Email, Telefone, Data Nasc.)
    $update_fields = "nome=?, email=?, telefone=?, data_nascimento=?";
    $bind_types = "ssss"; 
    $bind_params = [$nome, $email, $telefone, $data_nascimento];

    // Se a senha for fornecida e for de um tamanho aceitável
    if (!empty($nova_senha)) {
        if (strlen($nova_senha) > 6) {
             echo "<script>
                alert('Erro: A nova senha deve ter no máximo 6 caracteres.');
                history.back();
              </script>";
             exit();
        }
        
        $update_fields .= ", senha=?";
        $bind_types .= "s";
        $bind_params[] = $nova_senha;
    }
    
    // Adiciona o ID ao final da lista de parâmetros
    $bind_params[] = $id_usuario; 
    $bind_types .= "i"; 

    $sql = "UPDATE tbUsuario SET {$update_fields} WHERE id_usuario=?";
    $stmt = $conn->prepare($sql);

    // Usa call_user_func_array para passar a lista dinâmica de parâmetros
    $stmt->bind_param($bind_types, ...$bind_params);
    
    // 3. EXECUTAR A QUERY
    if ($stmt->execute()) {
        echo "<script>
                alert('Perfil atualizado com sucesso!');
                window.location.href = 'dashboard_paciente.html';
              </script>";
    } else {
        echo "<script>
                alert('Erro ao atualizar o perfil. Detalhes: " . $stmt->error . "');
                history.back();
              </script>";
    }

    $stmt->close();
    $conn->close();
}
?>