<?php
$conn = new mysqli("localhost", "root", "Lcs14hmhm@", "bdSmartSmile");
//não se esqueça de mudar a senha para a senha do seu banco mySQL

if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}

$email = $_POST['email'];
$senha = $_POST['senha'];

$sql = "SELECT u.id_usuario, u.senha, 
               a.id_administrador, 
               d.id_dentista 
        FROM tbUsuario u
        LEFT JOIN tbAdministrador a ON a.id_administrador = u.id_usuario
        LEFT JOIN tbDentista d ON d.id_dentista = u.id_usuario
        WHERE u.email = '$email'";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();

    //aqui o chat ajudou
    //no caso de acordo com a pesquisa, ele verifica como ja dito pelo if e else
    //validação através da senha e usuário no banco
    //"gambiarra estática"
    if ($senha === $user['senha']) {
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
