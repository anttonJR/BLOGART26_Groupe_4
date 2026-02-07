<?php
/**
 * ============================================================
 * FUNCTIONS/QUERY/DELETE.PHP - Fonction generique de suppression SQL
 * ============================================================
 * 
 * ROLE : Fournit une fonction reutilisable pour supprimer
 *        une ou plusieurs lignes dans n'importe quelle table.
 * 
 * UTILISATION :
 *   sql_delete('ARTICLE', "numArt = 1")
 * 
 * ATTENTION SECURITE :
 * Meme probleme que insert.php et update.php : la clause WHERE
 * est concatenee directement. Risque d'injection SQL.
 * 
 * ATTENTION CIR :
 * Avant de supprimer un article, il faut supprimer les donnees
 * associees (likes, commentaires, mots-cles) sinon MySQL renverra
 * une erreur de contrainte d'integrite referentielle.
 * 
 * MECANISME :
 * - Transaction PDO (atomicite)
 * - Retourne true si la suppression reussit
 * ============================================================
 */

/**
 * Supprime des lignes dans une table
 * 
 * @param string $table  Nom de la table (ex: 'ARTICLE')
 * @param string $where  Condition WHERE (ex: "numArt = 1")
 * @return bool          true si la suppression reussit
 */
function sql_delete($table, $where){
    global $DB;

    /* Connexion si pas encore etablie */
    if(!$DB){
        sql_connect();
    }

    try{
        $DB->beginTransaction();

        /* Construction de la requete DELETE */
        $query = "DELETE FROM $table WHERE $where;";
        $request = $DB->prepare($query);
        $request->execute();
        $DB->commit();
        $request->closeCursor();
    }
    catch(PDOException $e){
        /* Rollback en cas d'erreur (ex: violation de FK) */
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