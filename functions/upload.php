<?php
/**
 * Upload une image avec validation
 * 
 * @param array $file Le fichier $_FILES
 * @param string $uploadDir Dossier de destination
 * @return array ['success' => bool, 'filename' => string|null, 'error' => string|null]
 */
function uploadImage($file, $uploadDir = 'src/uploads/') {
    $result = [
        'success' => false,
        'filename' => null,
        'error' => null
    ];
    
    // Vérifier qu'un fichier a été uploadé
    if (!isset($file) || $file['error'] === UPLOAD_ERR_NO_FILE) {
        $result['error'] = "Aucun fichier sélectionné";
        return $result;
    }
    
    // Vérifier les erreurs d'upload
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $result['error'] = "Erreur lors de l'upload";
        return $result;
    }
    
    // Vérifier la taille (max 5 Mo)
    $maxSize = 5 * 1024 * 1024; // 5 Mo
    if ($file['size'] > $maxSize) {
        $result['error'] = "Le fichier est trop volumineux (max 5 Mo)";
        return $result;
    }
    
    // Vérifier le type MIME
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    if (!in_array($mimeType, $allowedTypes)) {
        $result['error'] = "Format non autorisé (JPG, PNG, GIF uniquement)";
        return $result;
    }
    
    // Générer un nom unique
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid('article_', true) . '.' . $extension;
    
    // Chemin complet
    $uploadPath = $uploadDir . $filename;
    
    // Déplacer le fichier
    if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
        $result['success'] = true;
        $result['filename'] = $filename;
    } else {
        $result['error'] = "Impossible de déplacer le fichier";
    }
    
    return $result;
}

/**
 * Supprime une image du serveur
 * 
 * @param string $filename Nom du fichier
 * @param string $uploadDir Dossier
 * @return bool
 */
function deleteImage($filename, $uploadDir = 'src/uploads/') {
    if (empty($filename)) {
        return false;
    }
    
    $filepath = $uploadDir . $filename;
    
    if (file_exists($filepath)) {
        return unlink($filepath);
    }
    
    return false;
}
?>