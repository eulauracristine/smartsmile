<?php
// CRÍTICO: db_connect.php deve ser incluído ANTES de qualquer output
require_once "db_connect.php"; 

// CRÍTICO: Inicia a sessão PHP após a conexão com o BD
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Definições de cabeçalho para AJAX
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, PUT, DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    http_response_code(200);
    exit();
}

$method = $_SERVER["REQUEST_METHOD"];
$data = json_decode(file_get_contents("php://input"));
$action = isset($data->action) ? $data->action : null;

if (!$action && $method === 'POST') {
    if (isset($data->id) && isset($data->email) && isset($data->telefone)) {
        $action = 'update_profile';
    } else {
        $action = (isset($data->email) && isset($data->senha)) ? 'login' : 'register';
    }
} elseif (!$action && $method === 'GET') {
    $action = isset($_GET['action']) ? $_GET['action'] : null;
}


// --- FUNÇÕES AUXILIARES ---

function get_user_role($link, $user_id) {
    $role = 'paciente';
    
    $sql_admin = "SELECT id_administrador FROM tbAdministrador WHERE id_administrador = ?";
    $stmt_admin = $link->prepare($sql_admin);
    $stmt_admin->bind_param("i", $user_id);
    $stmt_admin->execute();
    $result_admin = $stmt_admin->get_result();
    if ($result_admin->num_rows > 0) {
        $role = 'administrador';
    }
    $stmt_admin->close();

    if ($role === 'paciente') {
        $sql_dentista = "SELECT id_dentista FROM tbDentista WHERE id_dentista = ?";
        $stmt_dentista = $link->prepare($sql_dentista);
        $stmt_dentista->bind_param("i", $user_id);
        $stmt_dentista->execute();
        $result_dentista = $stmt_dentista->get_result();
        if ($result_dentista->num_rows > 0) {
            $role = 'dentista';
        }
        $stmt_dentista->close();
    }
    
    return $role;
}

// --- CONTROLE DE AÇÕES ---

switch ($action) {
    case 'register':
        // CADASTRO (CREATE)
        if (!isset($data->nome) || !isset($data->email) || !isset($data->senha) || !isset($data->cpf) || !isset($data->telefone) || !isset($data->data_nascimento)) {
            http_response_code(400); echo json_encode(["success" => false, "message" => "Dados incompletos para cadastro."]); break;
        }

        $nome = $link->real_escape_string($data->nome);
        $email = $link->real_escape_string($data->email);
        $senha = $link->real_escape_string($data->senha);
        $cpf = $link->real_escape_string($data->cpf);
        $telefone = $link->real_escape_string($data->telefone);
        $data_nascimento = $link->real_escape_string($data->data_nascimento);
        $status_usuario = 0;

        // 1. Verificar se o e-mail já existe
        $sql_check = "SELECT id_usuario FROM tbUsuario WHERE email = ?"; $stmt_check = $link->prepare($sql_check); $stmt_check->bind_param("s", $email); $stmt_check->execute(); $result_check = $stmt_check->get_result(); $stmt_check->close();

        if ($result_check->num_rows > 0) { http_response_code(409); echo json_encode(["success" => false, "message" => "Este e-mail já está cadastrado."]); break; }
        
        // 2. Inserir o novo usuário
        $sql_user = "INSERT INTO tbUsuario (nome, email, senha, status_usuario, cpf, telefone, data_nascimento) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt_user = $link->prepare($sql_user);
        // Tipos CORRIGIDOS: s s s i s s s (nome, email, senha, status_usuario, cpf, telefone, data_nascimento)
        $stmt_user->bind_param("sssisss", $nome, $email, $senha, $status_usuario, $cpf, $telefone, $data_nascimento);

        if ($stmt_user->execute()) {
            $new_id = $link->insert_id;
            http_response_code(201);
            echo json_encode(["success" => true, "message" => "Cadastro realizado com sucesso! Faça login.", "id" => $new_id, "name" => $nome, "role" => "paciente"]);
        } else {
            http_response_code(500); echo json_encode(["success" => false, "message" => "Erro ao registrar usuário: " . $stmt_user->error]);
        }
        $stmt_user->close();
        break;


    case 'login':
        // Lógica de Login (READ) - Retorna ID e Nome
        if (!isset($data->email) || !isset($data->senha)) { http_response_code(400); echo json_encode(["success" => false, "message" => "E-mail e senha são obrigatórios."]); break; }
        
        $email = $link->real_escape_string($data->email); $senha = $link->real_escape_string($data->senha);

        $sql = "SELECT id_usuario, nome, senha FROM tbUsuario WHERE email = ?";
        $stmt = $link->prepare($sql); $stmt->bind_param("s", $email); $stmt->execute(); $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();
            
            if ($senha === $row['senha']) {
                $user_id = $row['id_usuario']; $user_name = $row['nome'];
                $role = get_user_role($link, $user_id);
                
                // Salvando ID na sessão PHP também (para uso em arquivos PHP tradicionais)
                $_SESSION['id_usuario'] = $user_id; 

                http_response_code(200);
                // Retorna ID e Nome para o JS salvar no localStorage
                echo json_encode(["success" => true, "message" => "Login realizado.", "role" => $role, "id" => $user_id, "name" => $user_name]);
            } else { http_response_code(401); echo json_encode(["success" => false, "message" => "E-mail ou senha incorretos."]); }
        } else { http_response_code(401); echo json_encode(["success" => false, "message" => "E-mail ou senha incorretos."]); }
        $stmt->close();
        break;
        
    case 'read_profile':
        // ... (restante dos casos) ...
        break;
        
    case 'update_profile':
        // ... (restante dos casos) ...
        break;

    default:
        http_response_code(400); echo json_encode(["success" => false, "message" => "Ação não reconhecida."]); break;
}

$link->close();
?>