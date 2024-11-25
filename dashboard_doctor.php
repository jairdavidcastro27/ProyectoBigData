<?php
session_start();

// Verificar si el usuario está autenticado y tiene el rol de doctor
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'doctor') {
    header('Location: index.php');
    exit();
}

require 'db_connection.php';

$action = isset($_GET['action']) ? $_GET['action'] : null;
$editingPaciente = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($action === 'add') {
        // Agregar un paciente
        $nombre = $_POST['nombre']; 
        $edad = $_POST['edad'];
        $genero = $_POST['genero'];
        $diagnostico = $_POST['diagnostico'];
        $fecha = $_POST['fecha'];
        $tratamiento = $_POST['tratamiento'];
        $medicamento = $_POST['medicamento'];
        $frecuenciaConsulta = $_POST['frecuenciaConsulta'];
        $observaciones = $_POST['observaciones'];
        $estado = $_POST['estado'];

        $stmt = $conn->prepare("INSERT INTO pacientes (nombre, edad, genero, diagnostico, fecha, tratamiento, medicamento, frecuenciaConsulta, observaciones, estado)
                                VALUES (:nombre, :edad, :genero, :diagnostico, :fecha, :tratamiento, :medicamento, :frecuenciaConsulta, :observaciones, :estado)");
        $stmt->execute([
            ':nombre' => $nombre,
            ':edad' => $edad,
            ':genero' => $genero,
            ':diagnostico' => $diagnostico,
            ':fecha' => $fecha,
            ':tratamiento' => $tratamiento,
            ':medicamento' => $medicamento,
            ':frecuenciaConsulta' => $frecuenciaConsulta,
            ':observaciones' => $observaciones,
            ':estado' => $estado
        ]);
    } elseif ($action === 'edit') {
        // Editar un paciente
        $idPaciente = $_POST['idPaciente'];
        $nombre = $_POST['nombre'];
        $edad = $_POST['edad'];
        $genero = $_POST['genero'];
        $diagnostico = $_POST['diagnostico'];
        $fecha = $_POST['fecha'];
        $tratamiento = $_POST['tratamiento'];
        $medicamento = $_POST['medicamento'];
        $frecuenciaConsulta = $_POST['frecuenciaConsulta'];
        $observaciones = $_POST['observaciones'];
        $estado = $_POST['estado'];

        $stmt = $conn->prepare("UPDATE pacientes SET nombre = :nombre, edad = :edad, genero = :genero, diagnostico = :diagnostico, 
                                fecha = :fecha, tratamiento = :tratamiento, medicamento = :medicamento, 
                                frecuenciaConsulta = :frecuenciaConsulta, observaciones = :observaciones, estado = :estado 
                                WHERE idPaciente = :idPaciente");
        $stmt->execute([
            ':nombre' => $nombre,
            ':edad' => $edad,
            ':genero' => $genero,
            ':diagnostico' => $diagnostico,
            ':fecha' => $fecha,
            ':tratamiento' => $tratamiento,
            ':medicamento' => $medicamento,
            ':frecuenciaConsulta' => $frecuenciaConsulta,
            ':observaciones' => $observaciones,
            ':estado' => $estado,
            ':idPaciente' => $idPaciente
        ]);
        header('Location: dashboard_doctor.php');
        exit();
    } elseif ($action === 'delete') {
        // Eliminar un paciente
        $idPaciente = $_POST['idPaciente'];
        $stmt = $conn->prepare("DELETE FROM pacientes WHERE idPaciente = :idPaciente");
        $stmt->execute([':idPaciente' => $idPaciente]);
    }
}

// Cargar datos para editar
if ($action === 'edit' && isset($_GET['id'])) {
    $idPaciente = $_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM pacientes WHERE idPaciente = :idPaciente");
    $stmt->execute([':idPaciente' => $idPaciente]);
    $editingPaciente = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Exportar datos a Excel
if ($action === 'export') {
    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=pacientes.xls");

    echo "ID\tNombre\tEdad\tGenero\tDiagnostico\tFecha\tTratamiento\tMedicamento\tFrecuencia Consulta\tObservaciones\tEstado\n";

    $stmt = $conn->query("SELECT * FROM pacientes");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo implode("\t", $row) . "\n";
    }
    exit();
}

// Obtener todos los pacientes para mostrar en el dashboard
$stmt = $conn->query("SELECT * FROM pacientes");
$pacientes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Cerrar sesión
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
    <title>Dashboard Doctor - Pacientes con TDAH</title>
    <link rel="stylesheet" href="style.css">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Estilos adicionales */
        .container {
            max-width: 1200px;
            background-color: rgb(184, 216, 241);
            padding: 20px;
            border-radius: 10px;
        }
        h1, h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        .form-group input, .form-group select, .form-group textarea {
            max-width: 500px;
            margin: 10px auto;
        }
        .table th, .table td {
            text-align: center;
        }
        .btn-sm {
            padding: 5px 10px;
        }
        .btn-danger, .btn-primary, .btn-warning {
            width: 100%;
        }
        /* Mejorar las tablas responsivas */
        @media (max-width: 768px) {
            .table-responsive {
                overflow-x: scroll;
            }
        }
        /* Estilo para los formularios dentro de cards */
        .card {
            margin-bottom: 20px;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .card-header {
            font-weight: bold;
        }
    </style>
</head>
<body class="container mt-4">
    <h1>DASHBOARD PACIENTES CON TDAH</h1>

    <!-- Cerrar sesión -->
    <form method="POST" action="dashboard_doctor.php" class="d-inline">
        <button class="btn btn-danger" type="submit" name="logout">Cerrar Sesión</button>
    </form>
    <a href="dashboard_doctor.php?action=export" class="btn btn-primary mb-3">Exportar a Excel</a>

    <!-- Formulario para agregar o editar paciente dentro de una card -->
    <div class="">
        <div class="card-header">
            <h2><?php echo $editingPaciente ? 'Editar Paciente' : 'Agregar Paciente'; ?></h2>
        </div>
        <div class="">
            <form method="POST" action="dashboard_doctor.php?action=<?php echo $editingPaciente ? 'edit' : 'add'; ?>" class="form-group">
                <?php if ($editingPaciente): ?>
                    <input type="hidden" name="idPaciente" value="<?php echo $editingPaciente['idPaciente']; ?>">
                <?php endif; ?>
                <div class="mb-3">
                    <input type="text" name="nombre" placeholder="Nombre" value="<?php echo $editingPaciente['nombre'] ?? ''; ?>" class="form-control" required>
                </div>
                <div class="mb-3">
                    <input type="number" name="edad" placeholder="Edad" value="<?php echo $editingPaciente['edad'] ?? ''; ?>" class="form-control" required>
                </div>
                <div class="mb-3">
                    <select name="genero" class="form-select" required>
                        <option value="Masculino" <?php echo isset($editingPaciente['genero']) && $editingPaciente['genero'] === 'Masculino' ? 'selected' : ''; ?>>Masculino</option>
                        <option value="Femenino" <?php echo isset($editingPaciente['genero']) && $editingPaciente['genero'] === 'Femenino' ? 'selected' : ''; ?>>Femenino</option>
                    </select>
                </div>
                <div class="mb-3">
                    <textarea name="diagnostico" placeholder="Diagnóstico" class="form-control"><?php echo $editingPaciente['diagnostico'] ?? ''; ?></textarea>
                </div>
                <div class="mb-3">
                    <input type="date" name="fecha" value="<?php echo $editingPaciente['fecha'] ?? ''; ?>" class="form-control" required>
                </div>
                <div class="mb-3">
                    <textarea name="tratamiento" placeholder="Tratamiento" class="form-control"><?php echo $editingPaciente['tratamiento'] ?? ''; ?></textarea>
                </div>
                <div class="mb-3">
                    <textarea name="medicamento" placeholder="Medicamento" class="form-control"><?php echo $editingPaciente['medicamento'] ?? ''; ?></textarea>
                </div>
                <div class="mb-3">
                    <input type="text" name="frecuenciaConsulta" placeholder="Frecuencia Consulta" value="<?php echo $editingPaciente['frecuenciaConsulta'] ?? ''; ?>" class="form-control" required>
                </div>
                <div class="mb-3">
                    <textarea name="observaciones" placeholder="Observaciones" class="form-control"><?php echo $editingPaciente['observaciones'] ?? ''; ?></textarea>
                </div>
                <div class="mb-3">
                    <select name="estado" class="form-select" required>
                        <option value="Activo" <?php echo isset($editingPaciente['estado']) && $editingPaciente['estado'] === 'Activo' ? 'selected' : ''; ?>>Activo</option>
                        <option value="Inactivo" <?php echo isset($editingPaciente['estado']) && $editingPaciente['estado'] === 'Inactivo' ? 'selected' : ''; ?>>Inactivo</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-success"><?php echo $editingPaciente ? 'Actualizar' : 'Agregar'; ?></button>
            </form>
        </div>
    </div>

    <hr>
    <div style="display: flex; justify-content: center; align-items: center; text-align: center;">
    <iframe title="TDAH_BIGDATA" width="1024" height="612" src="https://app.powerbi.com/view?r=eyJrIjoiMjRjMWE2NWYtMDM3Yi00MTIzLTk0OTctMGEwZGZiZTZiMDkxIiwidCI6IjEzODQxZDVmLTk2OGQtNDYyNC1hN2RhLWQ2OGE2MDA2YTg0YSIsImMiOjR9" frameborder="0" allowFullScreen="true"></iframe>
    </div>
    <hr>


    <hr>
    <!-- Tabla de pacientes dentro de una card -->
    <div class="card mt-4">
        <div class="card-header">
            <h2>Lista de Pacientes</h2>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
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
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pacientes as $paciente): ?>
                            <tr>
                                <td><?php echo $paciente['idPaciente']; ?></td>
                                <td><?php echo $paciente['nombre']; ?></td>
                                <td><?php echo $paciente['edad']; ?></td>
                                <td><?php echo $paciente['genero']; ?></td>
                                <td><?php echo $paciente['diagnostico']; ?></td>
                                <td><?php echo $paciente['fecha']; ?></td>
                                <td><?php echo $paciente['tratamiento']; ?></td>
                                <td><?php echo $paciente['medicamento']; ?></td>
                                <td><?php echo $paciente['frecuenciaConsulta']; ?></td>
                                <td><?php echo $paciente['observaciones']; ?></td>
                                <td><?php echo $paciente['estado']; ?></td>
                                <td>
                                    <a href="dashboard_doctor.php?action=edit&id=<?php echo $paciente['idPaciente']; ?>" class="btn btn-warning btn-sm mb-2">Editar</a>
                                    <form method="POST" action="dashboard_doctor.php?action=delete" style="display:inline;">
                                        <input type="hidden" name="idPaciente" value="<?php echo $paciente['idPaciente']; ?>">
                                        <button type="submit" class="btn btn-danger btn-sm">Eliminar</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
