<?php
/**
 * ============================================================
 * FUNCTIONS/QUERY/UPDATE.PHP - Fonction generique de mise a jour SQL
 * ============================================================
 * 
 * ROLE : Fournit une fonction reutilisable pour mettre a jour
 *        une ou plusieurs lignes dans n'importe quelle table.
 * 
 * UTILISATION :
 *   sql_update('ARTICLE', "libTitrArt = 'Nouveau titre'", "numArt = 1")
 * 
 * ATTENTION SECURITE :
 * Meme probleme que insert.php : les valeurs sont concatenees
 * directement dans la requete. Risque d'injection SQL.
 * 
 * MECANISME :
 * - Transaction PDO (atomicite)
 * - Retourne true si la mise a jour reussit
 * ============================================================
 */

/**
 * Met a jour des lignes dans une table
 * 
 * @param string $table      Nom de la table (ex: 'ARTICLE')
 * @param string $attributs  Colonnes et valeurs (ex: "libTitrArt = 'Titre'")
 * @param string $where      Condition WHERE (ex: "numArt = 1")
 * @return bool              true si la mise a jour reussit
 */
function sql_update($table, $attributs, $where) {
    global $DB;

    /* Connexion si pas encore etablie */
    if(!$DB){
        sql_connect();
    }

    try{
        $DB->beginTransaction();

        /* Construction de la requete UPDATE */
        $query = "UPDATE $table SET $attributs WHERE $where;";
        $request = $DB->prepare($query);
        $request->execute();
        $DB->commit();
        $request->closeCursor();
    }
    catch(PDOException $e){
        /* Rollback en cas d'erreur */
        $DB->rollBack();
        $request->closeCursor();
        die('Error: ' . $e->getMessage());
    }

    $error = $DB->errorInfo();
    if($error[0] != 0){
        echo "Error: " . $error[2];
    }else{
        return true;
    }
}
?>