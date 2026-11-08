<?php
declare(strict_types=1);
require_once __DIR__ . '/../atelier6/config/common.php';
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    jsonResponse(['success' => false, 'message' => 'Méthode non autorisée.'], 405);
}
try {
    $rows = getDatabase()->query('SELECT id, nom, prenom, ville, photo, date_inscription FROM stagiaires ORDER BY date_inscription DESC, id DESC')->fetchAll();
    jsonResponse(['success' => true, 'stagiaires' => $rows]);
} catch (Throwable $e) {
    error_log('Atelier 6 list failed: ' . $e->getMessage());
    jsonResponse(['success' => false, 'message' => 'Impossible de charger la liste. Vérifiez la connexion à la base.'], 500);
}
