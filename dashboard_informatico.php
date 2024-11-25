<?php
session_start();

// Verificar si el usuario está autenticado y tiene el rol de informático
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'informatico') {
    header('Location: index.php');
    exit();
}

require 'db_connection.php';

// Verificar si el informático ha enviado un enlace de Power BI
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['powerbi_link'])) {
    $powerbiLink = $_POST['powerbi_link'];

    // Aquí puedes guardar el enlace en la base de datos si es necesario
    // Por ahora, solo lo mostramos en el panel
    // Ejemplo: guardarlo en una tabla (si lo necesitas)
    // $stmt = $conn->prepare("UPDATE configuracion SET powerbi_link = :link WHERE id = 1");
    // $stmt->execute([':link' => $powerbiLink]);
}

// Exportar datos a Excel
if (isset($_GET['action']) && $_GET['action'] === 'export') {
    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=pacientes.xls");

    echo "ID\tNombre\tEdad\tGenero\tDiagnostico\tFecha\tTratamiento\tMedicamento\tFrecuencia Consulta\tObservaciones\tEstado\n";

    $stmt = $conn->query("SELECT * FROM pacientes");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo implode("\t", $row) . "\n";
    }
    exit();
}

// Obtener los datos de los pacientes
$stmt = $conn->query("SELECT * FROM pacientes");
$pacientes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Función para cerrar sesión
if (isset($_POST['logout'])) {
    session_unset();
    session_destroy();
    header('Location: index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Informático</title>

    <!-- Incluir Bootstrap desde su CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        /* Colores de Essalud */
        .navbar, .btn-primary {
            background-color: #006B8E; /* Azul Essalud */
        }
        .btn-primary:hover {
            background-color: #004F6A; /* Azul Essalud más oscuro */
        }
        .table th, .table td {
            text-align: center;
        }
        .btn-logout {
            background-color: #45A050; /* Verde Essalud */
            color: white;
        }
        .btn-logout:hover {
            background-color: #388741; /* Verde Essalud más oscuro */
        }
        h1, h2, h3 {
            color: #006B8E; /* Azul Essalud */
        }
    </style>
</head>
<body>

    <!-- Barra de navegación -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Panel Informático</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <!-- Aquí pueden ir más enlaces si es necesario -->
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h1>Panel Informático</h1>

        <!-- Botón de Cerrar Sesión -->
        <form method="POST" action="dashboard_informatico.php">
            <button type="submit" name="logout" class="btn btn-logout mb-4">Cerrar Sesión</button>
        </form>

        <!-- Mostrar los datos de pacientes ingresados por el doctor -->
        <h2>Datos de Pacientes</h2>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID Paciente</th>
                        <th>Nombre</th>
                        <th>Edad</th>
                        <th>Género</th>
                        <th>Diagnóstico</th>
                        <th>Fecha</th>
                        <th>Tratamiento</th>
                        <th>Medicamento</th>
                        <th>Frecuencia Consulta</th>
                        <th>Observaciones</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pacientes as $paciente): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($paciente['idPaciente']); ?></td>
                            <td><?php echo htmlspecialchars($paciente['nombre']); ?></td>
                            <td><?php echo htmlspecialchars($paciente['edad']); ?></td>
                            <td><?php echo htmlspecialchars($paciente['genero']); ?></td>
                            <td><?php echo htmlspecialchars($paciente['diagnostico']); ?></td>
                            <td><?php echo htmlspecialchars($paciente['fecha']); ?></td>
                            <td><?php echo htmlspecialchars($paciente['tratamiento']); ?></td>
                            <td><?php echo htmlspecialchars($paciente['medicamento']); ?></td>
                            <td><?php echo htmlspecialchars($paciente['frecuenciaConsulta']); ?></td>
                            <td><?php echo htmlspecialchars($paciente['observaciones']); ?></td>
                            <td><?php echo htmlspecialchars($paciente['estado']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Enlace para exportar los datos de pacientes a Excel -->
        <h3>Acciones</h3>
        <a href="dashboard_informatico.php?action=export" class="btn btn-primary mb-4">Exportar Datos a Excel</a>

        <hr>

        <!-- Espacio para ingresar el enlace de Power BI -->
        <h3>Enlace de Power BI</h3>
        <form method="POST" action="dashboard_informatico.php">
            <div class="mb-3">
                <label for="powerbi_link" class="form-label">Enlace de Power BI:</label>
                <input type="url" id="powerbi_link" name="powerbi_link" class="form-control" placeholder="Ingresa el enlace de Power BI" required>
            </div>
            <button type="submit" class="btn btn-primary">Guardar Enlace</button>
        </form>

        <!-- Mostrar el enlace guardado (si es necesario) -->
        <?php if (isset($powerbiLink)): ?>
            <p class="mt-4">Enlace de Power BI guardado: <a href="<?php echo htmlspecialchars($powerbiLink); ?>" target="_blank">Ver Power BI</a></p>
        <?php endif; ?>
    </div>

    <!-- Incluir JS de Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
