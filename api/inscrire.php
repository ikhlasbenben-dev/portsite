<?php
declare(strict_types=1);
require_once __DIR__ . '/../atelier6/config/common.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Méthode non autorisée.'], 405);
}
$nom = trim((string) ($_POST['nom'] ?? ''));
$prenom = trim((string) ($_POST['prenom'] ?? ''));
$ville = trim((string) ($_POST['ville'] ?? ''));
$photo = $_FILES['photo'] ?? null;
if ($nom === '' || $prenom === '' || $ville === '' || mb_strlen($nom) > 100 || mb_strlen($prenom) > 100 || mb_strlen($ville) > 100) {
    jsonResponse(['success' => false, 'message' => 'Remplissez les champs correctement (100 caractères maximum).'], 422);
}
if (!$photo || $photo['error'] !== UPLOAD_ERR_OK || $photo['size'] > 5 * 1024 * 1024) {
    jsonResponse(['success' => false, 'message' => 'Choisissez une image de moins de 5 Mo.'], 422);
}
$mime = (new finfo(FILEINFO_MIME_TYPE))->file($photo['tmp_name']);
if (!in_array($mime, ['image/jpeg', 'image/png', 'image/webp'], true)) {
    jsonResponse(['success' => false, 'message' => 'Formats acceptés : JPG, PNG et WEBP.'], 422);
}
try {
    $photoUrl = cloudinaryUpload($photo['tmp_name']);
    $db = getDatabase();
    $stmt = $db->prepare('INSERT INTO stagiaires (nom, prenom, ville, photo) VALUES (:nom, :prenom, :ville, :photo)');
    $stmt->execute(['nom' => $nom, 'prenom' => $prenom, 'ville' => $ville, 'photo' => $photoUrl]);
    jsonResponse(['success' => true, 'message' => 'Inscription enregistrée.']);
} catch (Throwable $e) {
    error_log('Atelier 6 registration failed: ' . $e->getMessage());
    jsonResponse(['success' => false, 'message' => 'Impossible d’enregistrer. Vérifiez les réglages serveur.'], 500);
}
