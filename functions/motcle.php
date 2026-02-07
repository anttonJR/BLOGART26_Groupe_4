<?php
/**
 * ============================================================
 * FUNCTIONS/MOTCLE.PHP - Fonctions de gestion des mots-cles
 * ============================================================
 * 
 * ROLE : Fournit des fonctions pour recuperer les mots-cles
 *        associes ou disponibles pour un article donne.
 * 
 * CONTEXTE BDD :
 * - Table MOTCLE : contient tous les mots-cles (numMotCle, libMotCle)
 * - Table MOTCLEARTICLE : table de jointure N:N entre ARTICLE et MOTCLE
 *   → Un article peut avoir plusieurs mots-cles
 *   → Un mot-cle peut etre associe a plusieurs articles
 * 
 * UTILISATION :
 * - getMotsClesArticle(5)     → retourne les mots-cles de l'article 5
 * - getMotsClesDisponibles(5) → retourne les mots-cles NON associes a l'article 5
 *   (utile dans un formulaire pour proposer les mots-cles non encore selectionnes)
 * ============================================================
 */

/**
 * Recupere tous les mots-cles associes a un article
 * 
 * Requete : INNER JOIN entre MOTCLE et MOTCLEARTICLE
 * → Ne retourne que les mots-cles qui ont une association dans MOTCLEARTICLE
 * 
 * @param int $numArt Numero de l'article (cle primaire)
 * @return array Liste des mots-cles [['numMotCle' => 1, 'libMotCle' => 'Vin'], ...]
 */
function getMotsClesArticle($numArt) {
    global $DB;
    
    /* 
     * INNER JOIN : retourne uniquement les lignes qui ont une correspondance
     * dans les DEUX tables (MOTCLE et MOTCLEARTICLE)
     * WHERE mca.numArt = ? : filtre sur l'article demande
     * ORDER BY mc.libMotCle : tri alphabetique
     */
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
 * Recupere les mots-cles NON associes a un article
 * 
 * Utile dans le formulaire d'edition pour proposer les mots-cles
 * que l'on peut encore ajouter a l'article.
 * 
 * Requete : NOT IN (sous-requete) → exclut les mots-cles deja associes
 * 
 * @param int $numArt Numero de l'article
 * @return array Liste des mots-cles disponibles (non encore associes)
 */
function getMotsClesDisponibles($numArt) {
    global $DB;
    
    /* 
     * Sous-requete NOT IN :
     * 1. La sous-requete recupere tous les numMotCle associes a l'article
     * 2. La requete principale exclut ces numMotCle du resultat
     * → On obtient les mots-cles qui ne sont PAS associes a cet article
     */
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