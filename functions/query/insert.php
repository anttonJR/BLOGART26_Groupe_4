<?php
/**
 * ============================================================
 * FUNCTIONS/QUERY/INSERT.PHP - Fonction generique d'insertion SQL
 * ============================================================
 * 
 * ROLE : Fournit une fonction reutilisable pour inserer une ligne
 *        dans n'importe quelle table de la BDD.
 * 
 * UTILISATION :
 *   sql_insert('ARTICLE', 'numArt, libTitrArt', '1, "Mon titre"')
 * 
 * ATTENTION SECURITE :
 * Cette fonction concatene directement les valeurs dans la requete SQL.
 * Les valeurs ne sont PAS echappees via des parametres prepares (?).
 * Risque d'injection SQL si les valeurs viennent de l'utilisateur
 * sans etre nettoyees au prealable.
 * 
 * MECANISME :
 * - Utilise les transactions PDO (beginTransaction/commit/rollBack)
 * - En cas d'erreur, la transaction est annulee (rollBack)
 * - Retourne true si l'insertion reussit
 * ============================================================
 */

/**
 * Insere une ligne dans une table
 * 
 * @param string $table      Nom de la table (ex: 'ARTICLE')
 * @param string $attributs  Colonnes separees par des virgules (ex: 'numArt, libTitrArt')
 * @param string $values     Valeurs separees par des virgules (ex: '1, "Mon titre"')
 * @return bool              true si l'insertion reussit
 */
function sql_insert($table, $attributs, $values){
    /* Acces a la connexion PDO globale */
    global $DB;

    /* Si la connexion n'existe pas encore, on la cree */
    if(!$DB){
        sql_connect();
    }

    try{
        /* 
         * Transaction : groupe d'operations atomiques
         * Soit TOUT reussit (commit), soit RIEN n'est applique (rollBack)
         * Utile quand on fait plusieurs insertions liees
         */
        $DB->beginTransaction();

        /* Construction et execution de la requete INSERT */
        $query = "INSERT INTO $table ($attributs) VALUES ($values);";
        $request = $DB->prepare($query);
        $request->execute();
        
        /* Si pas d'erreur, on valide la transaction */
        $DB->commit();
        $request->closeCursor(); // Libere les ressources du curseur
    }
    catch(PDOException $e){
        /* En cas d'erreur PDO, on annule la transaction */
        $DB->rollBack();
        $request->closeCursor();
        die('Error: ' . $e->getMessage());
    }

    /* Verification supplementaire des erreurs PDO */
    $error = $DB->errorInfo();
    if($error[0] != 0){
        echo "Error: " . $error[2];
    }else{
        return true;
    }
}
?>