<?php

/**
 * Récupère tous les mots-clés d'un article
 * 
 * @param int $numArt Numéro de l'article
 * @return array Liste des mots-clés
 */
function getMotsClesArticle($numArt) {
    global $DB;
    
    $sql = "SELECT mc.* 
            FROM MOTCLE mc
            INNER JOIN MOTCLEARTICLE mca ON mc.numMotCle = mca.numMotCle
            WHERE mca.numArt = ?
            ORDER BY mc.libMotCle";
    
    $stmt = $DB->prepare($sql);
    $stmt->execute([$numArt]);
    
    return $stmt->fetchAll();
}

/**
 * Récupère les mots-clés NON associés à un article
 * 
 * @param int $numArt Numéro de l'article
 * @return array Liste des mots-clés disponibles
 */
function getMotsClesDisponibles($numArt) {
    global $DB;
    
    $sql = "SELECT mc.* 
            FROM MOTCLE mc
            WHERE mc.numMotCle NOT IN (
                SELECT mca.numMotCle 
                FROM MOTCLEARTICLE mca 
                WHERE mca.numArt = ?
            )
            ORDER BY mc.libMotCle";
    
    $stmt = $DB->prepare($sql);
    $stmt->execute([$numArt]);
    
    return $stmt->fetchAll();
}
?>