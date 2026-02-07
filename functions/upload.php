<?php
/**
 * ============================================================
 * FUNCTIONS/UPLOAD.PHP - Gestion des images (upload, suppression, compression)
 * ============================================================
 * 
 * ROLE : Fournit les fonctions d'upload, suppression et compression
 *        d'images pour les articles du blog.
 * 
 * UTILISATION DANS LE CRUD ARTICLE :
 * - uploadImage() : appelee lors de la creation/modification d'un article
 *   pour sauvegarder l'image sur le serveur
 * - deleteImage() : appelee lors de la suppression d'un article ou du
 *   remplacement d'une image existante
 * - compressImage() : fonction utilitaire pour reduire la taille des images
 * 
 * DOSSIER D'UPLOAD : src/uploads/
 * FORMATS ACCEPTES : JPEG, PNG, GIF
 * TAILLE MAX : 5 Mo
 * ============================================================
 */

/**
 * Upload une image avec validation complete
 * 
 * Etapes de validation :
 * 1. Verifie qu'un fichier a ete envoye
 * 2. Verifie qu'il n'y a pas d'erreur d'upload PHP
 * 3. Verifie la taille (max 5 Mo)
 * 4. Verifie le type MIME reel (pas juste l'extension)
 * 5. Genere un nom unique pour eviter les conflits
 * 6. Deplace le fichier du dossier temporaire vers src/uploads/
 * 
 * @param array  $file      Le fichier depuis $_FILES['imageArt']
 * @param string $uploadDir Dossier de destination (defaut: 'src/uploads/')
 * @return array ['success' => bool, 'filename' => string|null, 'error' => string|null]
 */
function uploadImage($file, $uploadDir = 'src/uploads/') {
    /* Structure de retour standardisee */
    $result = [
        'success' => false,
        'filename' => null,
        'error' => null
    ];
    
    /* --- Etape 1 : Verification de la presence du fichier --- */
    if (!isset($file) || $file['error'] === UPLOAD_ERR_NO_FILE) {
        $result['error'] = "Aucun fichier sélectionné";
        return $result;
    }
    
    /* --- Etape 2 : Verification des erreurs d'upload PHP --- */
    /* UPLOAD_ERR_OK (0) = pas d'erreur */
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $result['error'] = "Erreur lors de l'upload";
        return $result;
    }
    
    /* --- Etape 3 : Verification de la taille --- */
    /* 5 * 1024 * 1024 = 5 242 880 octets = 5 Mo */
    $maxSize = 5 * 1024 * 1024;
    if ($file['size'] > $maxSize) {
        $result['error'] = "Le fichier est trop volumineux (max 5 Mo)";
        return $result;
    }
    
    /* 
     * --- Etape 4 : Verification du type MIME reel ---
     * finfo_file() analyse le CONTENU du fichier (pas juste l'extension)
     * Securite : empeche d'uploader un .php renomme en .jpg
     */
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    if (!in_array($mimeType, $allowedTypes)) {
        $result['error'] = "Format non autorisé (JPG, PNG, GIF uniquement)";
        return $result;
    }
    
    /* 
     * --- Etape 5 : Generation d'un nom unique ---
     * uniqid('article_', true) genere un identifiant unique base sur le temps
     * Prefixe 'article_' pour identifier facilement les images d'articles
     * Evite les conflits si 2 utilisateurs uploadent en meme temps
     */
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid('article_', true) . '.' . $extension;
    
    /* Chemin complet sur le serveur */
    $uploadPath = $uploadDir . $filename;
    
    /* 
     * --- Etape 6 : Deplacement du fichier ---
     * move_uploaded_file() deplace le fichier du dossier temporaire PHP
     * vers le dossier de destination. Cette fonction verifie aussi que
     * le fichier a bien ete uploade via HTTP POST (securite)
     */
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
 * Utilisee lors de :
 * - Suppression d'un article (hard delete)
 * - Remplacement de l'image d'un article (update)
 * 
 * @param string $filename  Nom du fichier (ex: 'article_65f1a2b3c4d5e.jpg')
 * @param string $uploadDir Dossier (defaut: 'src/uploads/')
 * @return bool             true si supprime, false sinon
 */
function deleteImage($filename, $uploadDir = 'src/uploads/') {
    if (empty($filename)) {
        return false;
    }
    
    $filepath = $uploadDir . $filename;
    
    /* 
     * file_exists() verifie que le fichier existe avant de tenter la suppression
     * unlink() supprime le fichier du systeme de fichiers
     */
    if (file_exists($filepath)) {
        return unlink($filepath);
    }
    
    return false;
}

/**
 * Compresse une image pour reduire sa taille
 * 
 * Convertit l'image en JPEG avec un niveau de qualite configurable.
 * Utile pour optimiser les performances de chargement des pages.
 * 
 * @param string $source      Chemin du fichier source
 * @param string $destination Chemin du fichier de destination
 * @param int    $quality     Qualite JPEG (0-100, defaut: 75)
 * @return bool               true si compresse, false si format non supporte
 */
function compressImage($source, $destination, $quality = 75) {
    /* Detecte le type MIME de l'image source */
    $info = getimagesize($source);
    
    /* Cree une ressource image selon le format */
    if ($info['mime'] == 'image/jpeg') {
        $image = imagecreatefromjpeg($source);
    } elseif ($info['mime'] == 'image/png') {
        $image = imagecreatefrompng($source);
    } else {
        return false; // Format non supporte (ex: GIF)
    }
    
    /* Sauvegarde en JPEG avec le niveau de qualite specifie */
    imagejpeg($image, $destination, $quality);
    /* Libere la memoire utilisee par la ressource image */
    imagedestroy($image);
    
    return true;
}
?>