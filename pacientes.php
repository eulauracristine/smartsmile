<?php
include 'conexao.php';

$sql = "SELECT id_usuario, nome, email, cpf, telefone, data_nascimento 
        FROM tbUsuario 
        WHERE status_usuario = 0";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Dashboard - adm | Smart Smile</title>
    <!-- Favicons -->
    <link href="assets/img/logo.jpg" rel="icon">
    <link href="assets/css/app.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/controlepacientes.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">



    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
</head>

<body>
    <div class="wrapper">
        <nav id="sidebar" class="sidebar js-sidebar">
            <div class="sidebar-content js-simplebar">
                <a class="sidebar-brand" href="dashboard_adm.html">
                    <img src="assets/img/logo.jpg" alt="Smart Smile" class="logo-sidebar">
                </a>


                <ul class="sidebar-nav">
                    <li class="sidebar-header"></li>

                    <li class="sidebar-item">
                        <a class="sidebar-link" href="dashboard_adm.html">
                            <i class="align-middle" data-feather="home"></i>
                            <span class="align-middle">Início</span>
                        </a>
                    </li>

                    <li class="sidebar-item">
                        <a class="sidebar-link" href="">
                            <i class="bi bi-folder"></i>
                            <span class="align-middle">Históricos</span>
                        </a>
                    </li>

                    <li class="sidebar-item">
                        <a class="sidebar-link" href="">
                            <i class="align-middle" data-feather="calendar"></i>
                            <span class="align-middle">Relatórios</span>
                        </a>
                    </li>

                    <li class="sidebar-item active">
                        <a class="sidebar-link" href="">
                            <i class="align-middle" data-feather="user"></i>
                            <span class="align-middle">Pacientes</span>
                        </a>
                    </li>

                    <li class="sidebar-item">
                        <a class="sidebar-link" href="">
                            <i class="bi bi-credit-card"></i>
                            <span class="align-middle">Pendências</span>
                        </a>
                    </li>
                </ul>

                <div class="sidebar-cta">
                    <div class="d-grid">
                        <a href="index.html" class="btn btn-danger">Sair</a>
                    </div>
                </div>
            </div>
        </nav>

        <div class="main">
            <nav class="navbar navbar-expand navbar-light navbar-bg">
                <a class="sidebar-toggle js-sidebar-toggle">
                    <i class="hamburger align-self-center"></i>
                </a>

                <div class="navbar-collapse collapse">
                    <ul class="navbar-nav navbar-align">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle d-none d-sm-inline-block" href="#"
                                data-bs-toggle="dropdown">
                                <img src="assets/img/avatars/avatar-3.jpg" class="avatar img-fluid rounded me-1"
                                    alt="Paciente" />
                                <span class="text-dark">Olá, Marcela 👋</span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end">

                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="index.html">Sair</a>
                            </div>
                        </li>
                    </ul>
                </div>
            </nav>

            <!--hero-->
            <section id="hero" class="hero section">
                <div class="container" data-aos="fade-up" data-aos-delay="100">
                    <main class="content">
                        <h1>LISTA DE PACIENTES</h1>
                        <p>Gerencie e acompanhe as informações dos pacientes com facilidade.</p>
                    </main>
                </div>
            </section>
<!--tabela que puxa os usuários comuns, sem adm-->
<div class="container mt-5">
    <?php if (isset($_GET['msg']) && $_GET['msg'] == 'deletado'): ?>
        <div class="alert alert-success">Paciente deletado com sucesso!</div>
    <?php endif; ?>

    <?php if ($result->num_rows > 0): ?>
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Email</th>
                    <th>CPF</th>
                    <th>Telefone</th>
                    <th>Data de Nascimento</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['id_usuario'] ?></td>
                        <td><?= htmlspecialchars($row['nome']) ?></td>
                        <td><?= htmlspecialchars($row['email']) ?></td>
                        <td><?= htmlspecialchars($row['cpf']) ?></td>
                        <td><?= htmlspecialchars($row['telefone']) ?></td>
                        <td><?= date('d/m/Y', strtotime($row['data_nascimento'])) ?></td>
                        <td>
                            <a href="delete_paciente.php?id=<?= $row['id_usuario'] ?>" 
                               class="btn btn-danger btn-sm"
                               onclick="return confirm('Tem certeza que deseja deletar este paciente?')">
                               Deletar
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="alert alert-warning">Nenhum paciente encontrado.</p>
    <?php endif; ?>
</div>
        </div>
    </div>



    <script src="assets/js/app.js"></script>


</body>

</html>