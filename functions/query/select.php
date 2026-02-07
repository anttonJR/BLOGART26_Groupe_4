<?php
/**
 * ============================================================
 * FUNCTIONS/QUERY/SELECT.PHP - Fonction generique de selection SQL
 * ============================================================
 * 
 * ROLE : Fournit une fonction reutilisable pour lire des donnees
 *        depuis n'importe quelle table avec filtres optionnels.
 * 
 * UTILISATION :
 *   sql_select('ARTICLE')                                     // SELECT * FROM ARTICLE
 *   sql_select('ARTICLE', 'libTitrArt, dtCreaArt')           // colonnes specifiques
 *   sql_select('ARTICLE', '*', 'numArt = 1')                 // avec WHERE
 *   sql_select('ARTICLE', '*', null, null, 'dtCreaArt DESC') // avec ORDER BY
 *   sql_select('ARTICLE', '*', null, null, null, '10')       // avec LIMIT
 * 
 * NOTE : Cette fonction ne retourne PAS une seule ligne.
 * Pour recuperer un seul article, utilisez selectOne() 
 * (definie probablement ailleurs ou via sql_select + [0]).
 * 
 * ATTENTION SECURITE :
 * Les parametres sont concatenes directement dans la requete SQL.
 * Risque d'injection SQL si les valeurs viennent de l'utilisateur.
 * Utilisez les requetes preparees PDO pour les donnees utilisateur.
 * ============================================================
 */

/**
 * Selectionne des lignes dans une table
 * 
 * @param string      $table     Nom de la table
 * @param string      $attributs Colonnes (defaut: '*' = toutes)
 * @param string|null $where     Condition WHERE (optionnel)
 * @param string|null $group     Clause GROUP BY (optionnel)
 * @param string|null $order     Clause ORDER BY (optionnel)
 * @param string|null $limit     Clause LIMIT (optionnel)
 * @return array                 Tableau de resultats (vide si aucun)
 */
function sql_select($table, $attributs = '*', $where = null, $group = null, $order = null, $limit = null){
    global $DB;

    /* Connexion si pas encore etablie */
    if(!$DB){
        sql_connect();
    }

    /* 
     * Construction dynamique de la requete SQL
     * On ajoute les clauses uniquement si elles sont fournies
     */
    $query = "SELECT " . $attributs . " FROM $table";
    if($where){
        $query .= " WHERE $where";
    }
    if($group){
        $query .= " GROUP BY $group";
    }
    if($order){
        $query .= " ORDER BY $order";
    }
    if($limit){
        $query .= " LIMIT $limit";
    }

    /* Execution de la requete (pas de prepare car pas de parametres) */
    $result = $DB->query($query);
    
    /* Verification des erreurs */
    $error = $DB->errorInfo();
    if($error[0] != 0){
        echo("Error: " . $error[2]);
    }else{
        /* fetchAll() retourne TOUTES les lignes dans un tableau */
        $result = $result->fetchAll();
    }

    /* Si pas de resultat, retourne un tableau vide (pas null/false) */
    if(!$result){
        $result = array();
    }

    return $result;
}
?>