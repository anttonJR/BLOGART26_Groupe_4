# 📝 CRUD Article - Documentation Complète

## 📋 Table des matières

1. [Vue d'ensemble](#vue-densemble)
2. [Structure de la BDD](#structure-de-la-bdd)
3. [Architecture des fichiers](#architecture-des-fichiers)
4. [Flux de navigation](#flux-de-navigation)
5. [CREATE - Création d'article](#create---création-darticle)
6. [READ - Lecture / Affichage](#read---lecture--affichage)
7. [UPDATE - Modification d'article](#update---modification-darticle)
8. [DELETE - Suppression d'article](#delete---suppression-darticle)
9. [Fonctions utilitaires](#fonctions-utilitaires)
10. [Sécurité](#sécurité)
11. [Notes techniques](#notes-techniques)

---

## 🔍 Vue d'ensemble

Le CRUD Article de BlogArt26 permet de gérer les articles du blog (Créer, Lire, Modifier, Supprimer). Il existe **deux implémentations** :

| Implémentation | Dossier | Statut | Description |
|---|---|---|---|
| **Backend Views** | `views/backend/articles/` | ✅ **Actif** | Interface admin avec PDO direct |
| **API** | `api/articles/` | ⚠️ Partiel | Endpoints avec fonctions génériques (contient du code mort) |

> **Le système actif est `views/backend/articles/`**. Les fichiers `api/articles/` contiennent du code mort (unreachable code) après des instructions `exit;`.

---

## 🗄️ Structure de la BDD

### Table ARTICLE
```sql
CREATE TABLE ARTICLE (
   numArt int(8) NOT NULL AUTO_INCREMENT,   -- PK : identifiant unique
   dtCreaArt datetime DEFAULT CURRENT_TIMESTAMP,  -- Date de création
   dtMajArt datetime DEFAULT NULL,          -- Date de dernière modification
   libTitrArt varchar(100),                 -- Titre de l'article
   libChapoArt text(500),                   -- Chapô (résumé court)
   libAccrochArt varchar(100),              -- Phrase d'accroche
   parag1Art text(1200),                    -- Paragraphe 1 (contenu principal)
   libSsTitr1Art varchar(100),              -- Sous-titre 1
   parag2Art text(1200),                    -- Paragraphe 2
   libSsTitr2Art varchar(100),              -- Sous-titre 2
   parag3Art text(1200),                    -- Paragraphe 3
   libConclArt text(800),                   -- Conclusion
   urlPhotArt varchar(70),                  -- Nom du fichier image
   numThem int(10) NOT NULL,                -- FK → THEMATIQUE
   PRIMARY KEY (numArt)
);
```

### Tables associées (relations)

```
ARTICLE (1) ←→ (N) MOTCLEARTICLE (N) ←→ (1) MOTCLE
   ↑                                              
   |── (1) ←→ (N) COMMENT                        
   |── (1) ←→ (N) LIKEART                        
   |── (N) ←→ (1) THEMATIQUE                     
```

| Table | Relation | Rôle |
|---|---|---|
| `MOTCLEARTICLE` | N:N (table de jointure) | Associe mots-clés à articles |
| `COMMENT` | 1:N | Commentaires sur l'article |
| `LIKEART` | 1:N | Likes des membres sur l'article |
| `THEMATIQUE` | N:1 | Catégorie/thème de l'article |

### Contraintes d'Intégrité Référentielle (CIR)

Toutes les FK utilisent `ON DELETE RESTRICT` → **impossible de supprimer un article tant que des données associées existent**. C'est pourquoi on doit supprimer dans l'ordre :
1. `LIKEART` (likes)
2. `COMMENT` (commentaires)
3. `MOTCLEARTICLE` (associations mots-clés)
4. `ARTICLE` (l'article lui-même)

---

## 📁 Architecture des fichiers

```
BLOGART26/
├── views/backend/articles/          ← INTERFACE ADMIN (le système actif)
│   ├── list.php                     ← Liste des articles actifs
│   ├── create.php                   ← Formulaire de création + traitement POST
│   ├── edit.php                     ← Formulaire de modification + traitement POST
│   ├── delete.php                   ← Suppression logique (soft delete)
│   ├── trash.php                    ← Vue de la corbeille
│   ├── restore.php                  ← Restauration depuis la corbeille
│   ├── permanent-delete.php         ← Suppression définitive (hard delete)
│   └── empty-trash.php              ← Vider toute la corbeille
│
├── views/frontend/articles/         ← AFFICHAGE PUBLIC
│   ├── article1.php                 ← Page de détail d'un article
│   └── recherche.php                ← Recherche/filtrage d'articles
│
├── api/articles/                    ← ENDPOINTS API (ancienne version, code mort)
│   ├── create.php                   ← ⚠️ Contient du code mort après exit
│   ├── update.php                   ← ⚠️ Contient du code mort après exit
│   └── delete.php                   ← ⚠️ Contient 3 try/catch dont 2 morts
│
├── functions/                       ← FONCTIONS UTILITAIRES
│   ├── upload.php                   ← Upload/suppression/compression d'images
│   ├── motcle.php                   ← Gestion mots-clés (get/disponibles)
│   ├── auth.php                     ← Authentification (requireAdmin)
│   ├── csrf.php                     ← Protection CSRF
│   ├── bbcode.php                   ← Conversion BBCode → HTML
│   └── query/                       ← Fonctions SQL génériques
│       ├── connect.php              ← Connexion PDO
│       ├── insert.php               ← INSERT générique
│       ├── update.php               ← UPDATE générique
│       ├── delete.php               ← DELETE générique
│       └── select.php               ← SELECT générique
│
└── BDD/
    └── CreateDbBlogArt26.sql        ← Script de création de la BDD
```

---

## 🔄 Flux de navigation

### Backend (Admin)

```
┌──────────┐     ┌───────────┐     ┌──────────┐
│ list.php │────→│ create.php│────→│ list.php  │
│ (liste)  │     │ (formulaire)    │ (succès)  │
└──────────┘     └───────────┘     └──────────┘
     │                                    ↑
     │           ┌───────────┐            │
     ├──────────→│  edit.php │────────────┘
     │           │ (modifier)│
     │           └───────────┘
     │
     │           ┌───────────┐     ┌──────────┐
     ├──────────→│ delete.php│────→│ list.php  │
     │           │(corbeille)│     │ (succès)  │
     │           └───────────┘     └──────────┘
     │
     │           ┌───────────┐     ┌──────────────────┐
     └──────────→│ trash.php │────→│ restore.php      │ → trash.php
                 │(corbeille)│     │ permanent-delete  │ → trash.php
                 │           │────→│ empty-trash.php   │ → list.php
                 └───────────┘     └──────────────────┘
```

### Frontend (Public)

```
┌────────────┐     ┌──────────────┐
│ index.php  │────→│recherche.php │ (liste d'articles filtrée)
│ (accueil)  │     └──────────────┘
└────────────┘            │
                          │
                    ┌─────▼──────┐
                    │article1.php│ (détail article + commentaires + likes)
                    └────────────┘
```

---

## ✏️ CREATE - Création d'article

### Fichier principal : `views/backend/articles/create.php`

**Ce fichier gère à la fois le GET (affichage du formulaire) et le POST (traitement).**

#### Logique étape par étape :

```
1. CHARGEMENT INITIAL (GET)
   ├── Inclusion header backend (session, config, navbar admin)
   ├── Vérification admin (requireAdmin)
   ├── Chargement thématiques (SELECT * FROM THEMATIQUE)
   └── Chargement mots-clés (SELECT * FROM MOTCLE)

2. TRAITEMENT FORMULAIRE (POST)
   ├── Récupération données POST avec trim()
   ├── Validation (titre obligatoire)
   ├── INSERT INTO ARTICLE (...) VALUES (...)
   ├── Récupération lastInsertId() pour l'association mots-clés
   ├── INSERT INTO MOTCLEARTICLE pour chaque mot-clé coché
   ├── Flash message en session
   └── Redirection vers list.php

3. AFFICHAGE FORMULAIRE (HTML)
   ├── Champs : titre*, chapô, accroche, contenu (textarea)
   ├── Select : thématique (boucle sur $thematiques)
   └── Checkboxes : mots-clés (name="motcles[]" = tableau PHP)
```

#### Points importants :
- **`lastInsertId()`** : récupère l'ID auto-incrémenté de l'article qu'on vient de créer. Indispensable pour insérer dans `MOTCLEARTICLE`.
- **`name="motcles[]"`** : le `[]` dans le name HTML crée un tableau PHP dans `$_POST['motcles']`. Chaque checkbox cochée ajoute son `value` au tableau.
- **Conservation des données** : si le formulaire échoue (erreur de validation), les champs sont ré-remplis avec `$_POST['libTitrArt'] ?? ''` pour ne pas perdre la saisie.

---

## 📖 READ - Lecture / Affichage

### Liste admin : `views/backend/articles/list.php`

```sql
-- Requête : articles actifs avec nom de thématique
SELECT a.*, t.libThem 
FROM ARTICLE a 
LEFT JOIN THEMATIQUE t ON a.numThem = t.numThem 
WHERE a.delLogiq = 0 OR a.delLogiq IS NULL
ORDER BY a.dtCreaArt DESC
```

- **`LEFT JOIN`** : on veut aussi les articles sans thématique (NULL)
- **`WHERE delLogiq = 0 OR IS NULL`** : exclut les articles dans la corbeille
- **Flash messages** : affiche `$_SESSION['success']` puis le supprime (pattern Flash Message)

### Détail article : `views/frontend/articles/article1.php`

```
1. Récupère l'article par ID (GET ?id=X)
2. Récupère les mots-clés (getMotsClesArticle)
3. Récupère le nombre de likes (COUNT dans LIKEART)
4. Vérifie si l'utilisateur connecté a liké (isLoggedIn + SELECT)
5. Récupère les commentaires validés (attModOK = 1 AND dtDelLogCom IS NULL)
6. Affiche : titre, image, chapô, accroche, paragraphes, conclusion
7. Affiche le formulaire de commentaire (si connecté)
8. Affiche la liste des commentaires avec BBCode
```

### Recherche : `views/frontend/articles/recherche.php`

- Filtre par **texte** (LIKE sur titre et chapô)
- Filtre par **thématique** (numThem)
- Filtre par **mot-clé** (INNER JOIN MOTCLEARTICLE)
- Tri par **récent**, **ancien**, **populaire** (nombre de likes)

---

## ✏️ UPDATE - Modification d'article

### Fichier principal : `views/backend/articles/edit.php`

**Différence avec create.php : le traitement POST est fait AVANT l'inclusion du header.**

#### Pourquoi ?
```php
// ❌ PROBLÈME : header() après du HTML
require_once 'header.php';  // ← envoie du HTML
// ... traitement ...
header('Location: list.php');  // ← ERREUR : "headers already sent"

// ✅ SOLUTION : traitement AVANT le header
session_start();
require_once 'config.php';
// ... traitement (peut faire header('Location: ...')) ...
require_once 'header.php';  // ← HTML envoyé APRÈS le traitement
```

`header()` en PHP ne peut fonctionner que si **aucun HTML** n'a encore été envoyé au navigateur. Si le header backend est inclus avant le traitement POST, les redirections échoueraient.

#### Logique étape par étape :

```
1. PRÉ-TRAITEMENT (avant le HTML)
   ├── session_start() + config.php (manuellement, pas via header)
   ├── Vérification admin
   ├── Récupération article par ID (GET ?id=X)
   ├── Chargement thématiques + mots-clés + mots-clés actuels
   └── Si POST : UPDATE ARTICLE + gestion mots-clés → redirect

2. GESTION DES MOTS-CLÉS (stratégie Delete & Re-insert)
   ├── DELETE FROM MOTCLEARTICLE WHERE numArt = ?  (tout supprimer)
   └── INSERT INTO MOTCLEARTICLE (numArt, numMotCle) pour chaque coché

3. AFFICHAGE FORMULAIRE (HTML)
   ├── Champs pré-remplis : $_POST['champ'] ?? $article['champ']
   │   → Si POST existe (erreur) : valeur saisie
   │   → Sinon : valeur en BDD
   └── Checkboxes pré-cochées : $_POST['motcles'] ?? $currentMotcles
```

#### Stratégie Delete & Re-insert pour les mots-clés :
Au lieu de comparer les anciens et nouveaux mots-clés pour savoir lesquels ajouter/supprimer, on :
1. **Supprime TOUTES** les anciennes associations
2. **Insère** les nouvelles

C'est plus simple et le résultat est identique.

---

## 🗑️ DELETE - Suppression d'article

### Le système utilise une **suppression en 2 étapes** :

| Étape | Fichier | Type | Réversible ? |
|---|---|---|---|
| 1 | `delete.php` | Soft Delete | ✅ Oui |
| 2 | `permanent-delete.php` | Hard Delete | ❌ Non |

### Étape 1 : Soft Delete (`delete.php`)

```php
// Marque l'article comme "supprimé" sans le supprimer réellement
UPDATE ARTICLE SET delLogiq = 1, dtDelLogArt = NOW() WHERE numArt = ?
```

- L'article **reste en BDD** mais n'apparaît plus dans `list.php`
- Il est visible dans `trash.php` (corbeille)
- Il peut être **restauré** via `restore.php`
- La date de suppression est enregistrée pour un compte à rebours (30 jours)

### Corbeille (`trash.php`)

- Affiche les articles avec `delLogiq = 1`
- Calcule le temps restant avant expiration :
  ```php
  $daysLeft = 30 - floor((time() - strtotime($art['dtDelLogArt'])) / 86400);
  // 86400 = 60 * 60 * 24 = nombre de secondes dans un jour
  ```
- Actions : Restaurer ou Supprimer définitivement

### Restauration (`restore.php`)

```php
// Inverse du soft delete : remet l'article dans la liste active
UPDATE ARTICLE SET delLogiq = 0, dtDelLogArt = NULL WHERE numArt = ?
```

### Étape 2 : Hard Delete (`permanent-delete.php`)

**Supprime définitivement l'article ET toutes ses données associées.**

L'ordre de suppression est **critique** à cause des CIR (ON DELETE RESTRICT) :

```php
// 1. Supprimer les likes (FK → ARTICLE)
DELETE FROM LIKEART WHERE numArt = ?

// 2. Supprimer les commentaires (FK → ARTICLE)
DELETE FROM COMMENT WHERE numArt = ?

// 3. Supprimer les mots-clés associés (FK → ARTICLE)
DELETE FROM MOTCLEARTICLE WHERE numArt = ?

// 4. Maintenant on peut supprimer l'article
DELETE FROM ARTICLE WHERE numArt = ?
```

> ⚠️ **Si on essaie de supprimer l'article en premier**, MySQL renvoie :
> `Cannot delete or update a parent row: a foreign key constraint fails`

### Vider la corbeille (`empty-trash.php`)

Même logique que `permanent-delete.php` mais en boucle sur tous les articles de la corbeille.

---

## 🔧 Fonctions utilitaires

### `functions/upload.php`

| Fonction | Rôle | Utilisée dans |
|---|---|---|
| `uploadImage($file)` | Upload une image avec validation (taille, MIME, nom unique) | Création/modification article |
| `deleteImage($filename)` | Supprime une image du serveur | Suppression article, remplacement image |
| `compressImage($source, $dest)` | Compresse une image en JPEG | Optimisation (non utilisée actuellement) |

**Validations d'upload :**
1. Fichier présent et sans erreur PHP
2. Taille ≤ 5 Mo
3. Type MIME réel (finfo_file, pas juste l'extension)
4. Formats : JPEG, PNG, GIF uniquement
5. Nom unique : `uniqid('article_', true)` + extension

### `functions/motcle.php`

| Fonction | Rôle | Requête SQL |
|---|---|---|
| `getMotsClesArticle($numArt)` | Mots-clés associés à l'article | `INNER JOIN MOTCLEARTICLE` |
| `getMotsClesDisponibles($numArt)` | Mots-clés NON associés | `NOT IN (sous-requête)` |

### `functions/query/*.php`

Fonctions SQL génériques utilisées par les fichiers `api/articles/` :

| Fichier | Fonction | SQL |
|---|---|---|
| `connect.php` | `sql_connect()` | Connexion PDO MySQL |
| `insert.php` | `sql_insert($table, $cols, $vals)` | `INSERT INTO` |
| `update.php` | `sql_update($table, $set, $where)` | `UPDATE SET WHERE` |
| `delete.php` | `sql_delete($table, $where)` | `DELETE FROM WHERE` |
| `select.php` | `sql_select($table, $cols, $where, ...)` | `SELECT FROM WHERE` |

> ⚠️ Ces fonctions **concatènent** les valeurs directement dans les requêtes SQL (pas de paramètres préparés). Risque d'injection SQL si les données ne sont pas nettoyées en amont.

---

## 🔒 Sécurité

| Protection | Mécanisme | Fichiers |
|---|---|---|
| **Accès admin** | `requireAdmin()` vérifie la session | Tous les fichiers backend |
| **CSRF** | Token caché dans le formulaire, vérifié côté serveur | `api/articles/*.php` |
| **XSS** | `htmlspecialchars()` sur toutes les sorties HTML | Views (frontend + backend) |
| **Injection SQL** | Requêtes préparées (PDO `prepare/execute`) | Views backend |
| **Upload** | Vérification MIME réel + taille + extension | `functions/upload.php` |
| **Sessions** | Messages flash (succès/erreur) stockés en `$_SESSION` | Tous |

### Pattern Flash Message
```php
// Écriture (après une action)
$_SESSION['success'] = "Article créé avec succès";
header('Location: list.php');

// Lecture (dans la page suivante)
if (isset($_SESSION['success'])):
    echo $_SESSION['success'];
    unset($_SESSION['success']);  // Supprimé après affichage
endif;
```

---

## 📝 Notes techniques

### Double implémentation (views/ vs api/)

Le projet contient **deux systèmes** de CRUD article :

1. **`views/backend/articles/`** → Utilise **PDO directement** (`$DB->prepare()`)
   - ✅ Fonctionne correctement
   - ✅ Gère les mots-clés
   - ✅ Gère le soft delete / corbeille

2. **`api/articles/`** → Utilise les **fonctions génériques** (`sql_insert()`, etc.)
   - ⚠️ Contient du code mort (unreachable après `exit`)
   - ⚠️ Ne gère pas correctement les CIR dans delete.php
   - ⚠️ L'upload d'image et les mots-clés sont dans le code mort

### Pourquoi du code mort dans `api/articles/` ?

Les fonctionnalités ont été ajoutées **après** les instructions `exit;` au lieu d'être intégrées **avant**. Par exemple dans `api/articles/create.php` :

```php
// Ce code s'exécute
try {
    $result = insert('ARTICLE', $data);
} catch (Exception $e) { ... }
header('Location: ...');
exit;  // ← TOUT S'ARRÊTE ICI

// Ce code ne s'exécute JAMAIS (dead code)
require_once '../../functions/upload.php';
// ... gestion upload ...
// ... gestion mots-clés ...
```

### Soft Delete vs Hard Delete

| Aspect | Soft Delete | Hard Delete |
|---|---|---|
| SQL | `UPDATE SET delLogiq = 1` | `DELETE FROM` |
| Données | Conservées en BDD | Supprimées définitivement |
| Réversible | ✅ Oui (restore) | ❌ Non |
| CIR | Pas de problème | Doit supprimer les FK d'abord |
| Fichier | `delete.php` | `permanent-delete.php` |

### BBCode dans les articles

Les articles supportent le BBCode grâce à `functions/bbcode.php` :
- `[b]gras[/b]` → **gras**
- `[i]italique[/i]` → *italique*
- Utilisé dans `article1.php` via `bbcode_to_html()`

---

## 🗂️ Résumé des fichiers modifiés/commentés

| Fichier | Commentaires ajoutés |
|---|---|
| `views/backend/articles/list.php` | ✅ Logique complète documentée |
| `views/backend/articles/create.php` | ✅ Formulaire + traitement POST |
| `views/backend/articles/edit.php` | ✅ Header avant/après expliqué |
| `views/backend/articles/delete.php` | ✅ Soft delete expliqué |
| `views/backend/articles/trash.php` | ✅ Corbeille + expiration |
| `views/backend/articles/restore.php` | ✅ Restauration documentée |
| `views/backend/articles/permanent-delete.php` | ✅ CIR + ordre de suppression |
| `views/backend/articles/empty-trash.php` | ✅ Suppression massive |
| `api/articles/create.php` | ✅ Code mort identifié |
| `api/articles/update.php` | ✅ Code mort identifié |
| `api/articles/delete.php` | ✅ 3 blocs try/catch documentés |
| `functions/query/insert.php` | ✅ Transaction PDO |
| `functions/query/update.php` | ✅ Transaction PDO |
| `functions/query/delete.php` | ✅ CIR mentionnées |
| `functions/query/select.php` | ✅ Construction dynamique |
| `functions/upload.php` | ✅ 6 étapes de validation |
| `functions/motcle.php` | ✅ JOIN + NOT IN expliqués |
