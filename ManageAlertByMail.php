<?php
session_start();

// Chemin du fichier CSV
$csvFile = 'Alerts-Config.csv';

// Charger le CSV en tableau
function loadCSV($file) {
    $rows = [];
    if (file_exists($file)) {
        $handle = fopen($file, 'r');
        $header = fgetcsv($handle, 1000, ';'); // Lire l'en-tête sans l'ajouter
        while (($data = fgetcsv($handle, 1000, ';')) !== FALSE) {
            $rows[] = $data;
        }
        fclose($handle);
    }
    return $rows;
}

// Sauvegarder le tableau en CSV
function saveCSV($file, $rows) {
    $handle = fopen($file, 'w');
    fputcsv($handle, ['machine_id', 'severity', 'AppName', 'MsgId'], ';'); // Ajouter l'en-tête
    foreach ($rows as $row) {
        fputcsv($handle, $row, ';');
    }
    fclose($handle);
}

$rows = loadCSV($csvFile);

// Gestion des actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add'])) {
        $newRow = [
            $_POST['machine_id'] ?? '*',
            $_POST['severity'] ?? '*',
            $_POST['AppName'] ?? '*',
            $_POST['MsgId'] ?? '*'
        ];
        $rows[] = $newRow;
        saveCSV($csvFile, $rows);
        $_SESSION['message'] = 'Ligne ajoutée avec succès';
    }
    
    if (isset($_POST['delete'])) {
        $index = $_POST['index'];
        unset($rows[$index]);
        $rows = array_values($rows);
        saveCSV($csvFile, $rows);
        $_SESSION['message'] = 'Ligne supprimée avec succès';
    }
    
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit();
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DEV Alerts By Mail Management</title>
    <!-- Bootstrap CSS v5.3 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome Icons v6.4 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <!-- Google Fonts: Roboto -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">

	<link rel="stylesheet" href="styles.css">    
	<link rel="stylesheet" href="stylesManage.css">
	
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark mb-4">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <i class="fas fa-calendar-times me-1"></i>
                Email Alerts Management For Servers
            </a>
            <div class="d-flex">
                <a href="/index.php" target="_blank" class="btn btn-outline-light btn-sm">
                    <i class="fas fa-home me-1"></i>
                    Home
                </a>
            
                <a href="index.php" target="_blank" class="btn btn-outline-light btn-sm ms-2">
                    <i class="fas fa-server me-1"></i>
                    Servers Events
                </a>
            </div>
        </div>
    </nav>
	
    <div class="container-fluid main-container">
        <h1>Gestion du Fichier CSV</h1>
        
        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-success"> <?= $_SESSION['message'] ?> </div>
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>
        
         <div class="table-container">
            <table class="table table-hover">
            <thead>
                <tr>
                    <th style="width: auto;">Machine ID</th>
                    <th style="width: auto;">Severity</th>
                    <th style="width: auto;">App Name</th>
                    <th style="width: auto;">Msg ID</th>
                    <th style="width: auto;">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rows as $index => $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row[0]) ?></td>
                        <td><?= htmlspecialchars($row[1]) ?></td>
                        <td><?= htmlspecialchars($row[2]) ?></td>
                        <td><?= htmlspecialchars($row[3]) ?></td>
                        <td>
                            <form method="post" style="display:inline;">
                                <input type="hidden" name="index" value="<?= $index ?>">
                                <button type="submit" name="delete" class="btn btn-danger">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <h2>Ajouter une Ligne</h2>
        <form method="post" class="form-group">
            <input type="text" name="machine_id" placeholder="Machine ID" class="form-control" required>
            <input type="text" name="severity" placeholder="Severity" class="form-control" required>
            <input type="text" name="AppName" placeholder="App Name" class="form-control" required>
            <input type="text" name="MsgId" placeholder="Msg ID" class="form-control" required>
            <button type="submit" name="add" class="btn btn-primary">Ajouter</button>
        </form>
    </div>
	</div>
</body>
</html>
