<?php
// Incluir o arquivo de conexão
require_once "db_connect.php";

// Definir o cabeçalho para aceitar requisições CORS (essencial para AJAX)
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Garantir que a requisição seja POST e decodificar o JSON enviado
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    http_response_code(405);
    echo json_encode(["success" => false, "message" => "Método não permitido."]);
    exit();
}

$data = json_decode(file_get_contents("php://input"));

if (!isset($data->action)) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Ação não especificada."]);
    exit();
}

$action = $data->action;

// --- FUNÇÃO DE CADASTRO (CREATE) ---
if ($action === 'register') {
    $nome = $link->real_escape_string($data->nome);
    $email = $link->real_escape_string($data->email);
    $senha = $link->real_escape_string($data->senha);
    $cpf = $link->real_escape_string($data->cpf);
    $telefone = $link->real_escape_string($data->telefone);
    $data_nascimento = $link->real_escape_string($data->data_nascimento);
    $status_usuario = 0; // Por padrão, o novo usuário é um paciente (status_usuario = 0/false)

    // 1. Verificar se o e-mail já existe
    $sql_check = "SELECT id_usuario FROM tbUsuario WHERE email = '$email'";
    $result_check = $link->query($sql_check);
    if ($result_check->num_rows > 0) {
        http_response_code(409); // Conflict
        echo json_encode(["success" => false, "message" => "Este e-mail já está cadastrado."]);
        exit();
    }
    
    // 2. Inserir o novo usuário na tbUsuario
    // NOTA: Em um ambiente real, a senha deveria ser HASHED (ex: password_hash())
    $sql_user = "INSERT INTO tbUsuario (nome, email, senha, status_usuario, cpf, telefone, data_nascimento) 
                 VALUES ('$nome', '$email', '$senha', $status_usuario, '$cpf', '$telefone', '$data_nascimento')";

    if ($link->query($sql_user) === TRUE) {
        $user_id = $link->insert_id;
        
        // 3. Inserir na tbPaciente (Assumindo que tbPaciente é o que você chamou de tbUsuario com status_usuario=false)
        // OBS: Como sua tabela tbUsuario já tem todos os campos, não há necessidade de tbPaciente.
        // Vamos apenas confirmar que a inserção foi bem-sucedida.

        http_response_code(201); // Created
        echo json_encode(["success" => true, "message" => "Cadastro realizado com sucesso!"]);
    } else {
        http_response_code(500);
        echo json_encode(["success" => false, "message" => "Erro ao registrar usuário: " . $link->error]);
    }

// --- FUNÇÃO DE LOGIN (READ) ---
} else if ($action === 'login') {
    $email = $link->real_escape_string($data->email);
    $senha = $link->real_escape_string($data->senha);

    // 1. Buscar usuário com e-mail e senha
    $sql = "SELECT id_usuario, status_usuario FROM tbUsuario WHERE email = '$email' AND senha = '$senha'";
    $result = $link->query($sql);

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $user_id = $row['id_usuario'];
        $status_usuario = (int)$row['status_usuario']; // status_usuario: 1=true, 0=false

        $role = 'paciente'; // Padrão
        
        // 2. Verificar o cargo (role) baseado nas tabelas auxiliares
        
        // Se status_usuario for true (1), o usuário pode ser Admin ou Dentista
        if ($status_usuario === 1) {
            
            // Checar se é Administrador
            $sql_admin = "SELECT id_administrador FROM tbAdministrador WHERE id_administrador = $user_id";
            if ($link->query($sql_admin)->num_rows > 0) {
                $role = 'administrador';
            } 
            
            // Checar se é Dentista (pode ser Dentista ou Admin/Dentista se as regras permitirem)
            $sql_dentista = "SELECT id_dentista FROM tbDentista WHERE id_dentista = $user_id";
            if ($link->query($sql_dentista)->num_rows > 0) {
                 // Prioriza Admin, mas se não for Admin (ou se quisermos que Dentista seja a prioridade neste ponto)
                 // Vamos deixar a lógica simples: se for Admin, é Admin. Se for Dentista (e não Admin), é Dentista.
                 // Como status_usuario=true implica em ser Admin OU Dentista na sua estrutura:
                 if ($role !== 'administrador') {
                    $role = 'dentista';
                 }
            }

            // O seu banco sugere que:
            // id_usuario na tbUsuario pode ser admin OU dentista.
            // Para simplificar, vou basear no seu `status_usuario` e checar nas tabelas auxiliares:
            
            if ($role === 'paciente' && $link->query($sql_admin)->num_rows > 0) {
                $role = 'administrador';
            } else if ($link->query($sql_dentista)->num_rows > 0) {
                $role = 'dentista';
            }
            
        } 
        
        // 3. Resposta de sucesso
        http_response_code(200);
        echo json_encode(["success" => true, "message" => "Login realizado.", "role" => $role]);
    } else {
        // 4. Resposta de erro
        http_response_code(401); // Unauthorized
        echo json_encode(["success" => false, "message" => "E-mail ou senha incorretos."]);
    }

} else {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Ação inválida."]);
}

$link->close();
?>