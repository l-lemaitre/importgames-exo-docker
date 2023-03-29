<?php
    // Inclut le fichier placé en argument bdd_connection.php à la page categories.php (ligne 23)
    if(empty($bdd)) {
        $bdd = "bdd_connection";

        // On limite l'inclusion aux fichiers .php en ajoutant dynamiquement l'extension. On supprime également d'éventuels espaces
        $bdd = trim($bdd . ".php");
    }

    // On évite les caractères qui permettent de naviguer dans les répertoires
    $bdd = str_replace("../", "protect", $bdd);
    $bdd = str_replace(";", "protect", $bdd);
    $bdd = str_replace("%", "protect", $bdd);

    // On interdit l'inclusion de dossiers protégés par htaccess
    if(preg_match("/backoff/", $bdd)) {
        echo "Vous n'avez pas accès à ce répertoire.";
     }

    else {
        // On vérifie que la page est bien sur le "serveur" en utilisant la constante magique __DIR__ qui représente le dossier du fichier categories.php
        if(file_exists(__DIR__ . "/../../application/" . $bdd)) {
           include __DIR__ . "/../../application/" . $bdd;
        }

        else {
            echo "Page inexistante.";
        }
    }


    // Si aucun administrateur n'est connecté alors on ne va pas sur cette page
    if(!isset($_SESSION["admin_id"])) {
        // L'utilisateur est envoyé à la page index/connexion
        header("location:/importgames/backoff/index");
        exit;
    }


    // Fonction pour afficher le titre de la page dans la balise html "head" (dossier backoff, fichier layout.phtml ligne 10)
    function headTitle() {
        return "Liste des catégories";
    }


    // Si la variable get page n'est pas déclarée ou ne contient pas au moins un chiffre,
    if(!isset($_GET["page"]) OR !preg_match("/^[0-9]+$/", $_GET["page"])) {
        // on retourne à la page 1
        header("location:/importgames/backoff/cats-page-1");
    }

    else {
        // On exécute un count() sur la table "categorie" pour extraire son nombre total de lignes
        $query = "SELECT COUNT(*) FROM `categorie`";
        $resultSet = $bdd->query($query);
        $nbrCats = $resultSet->fetch();


        // Si la variable post lignesCat est déclarée,
        if(isset($_POST["lignesCat"])) {
            // on affecte à l'index 2 du tableau de la variable de session lignes la valeur de la variable post lignesCat qui correspond à la valeur de l'envoi du nombre de lignes à afficher (voir fichiers varback.php ligne 72 et ajax.js ligne 31)
            $_SESSION["lignes"][2] = htmlspecialchars($_POST["lignesCat"]);
        }

        // ou bien si l'index 2 du tableau de la variable de session lignes est déclaré,
        elseif(isset($_SESSION["lignes"][2])) {
            // on lui affecte sa valeur actuelle
            $_SESSION["lignes"][2] = $_SESSION["lignes"][2];
        }

        // sinon l'index 2 du tableau de la variable de session lignes correspond à la valeur 5, 10 ou 25 selon le nombre de lignes à afficher
        else {
            if($nbrCats[0] <= 100) {
                $_SESSION["lignes"][2] = "5";
            }

            elseif($nbrCats[0] > 250) {
                $_SESSION["lignes"][2] = "25";
            }

            else {
                $_SESSION["lignes"][2] = "10";
            }
        }


        // La variable limite définit le nombre de lignes affichées par page
        $limite = $_SESSION["lignes"][2];

        // $_GET["page"] est une variable qui passera par le lien, elle commence à 1 donc pour accéder à la page categories.php il faut ajouter au lien ?page=1
        $page = htmlspecialchars($_GET["page"]);

        // La variable debut définit à partir de quelle ligne commence la sélection de la page courante. Par défaut si $page = 1 alors $debut = 0, si $page = 2 alors $debut = (2-1)*5 = 5
        $debut = ($page - 1) * $limite;


        // Voir fichiers varback.php ligne 73 et ajax.js ligne 35
        $_SESSION["page"] = $page;


        // La variable resultDebut retourne le numéro du résultat d'où commence la sélection de la page. On lui ajoute la valeur 1 pour correspondre avec l'Id du premier résultat affiché par tri ascendant (categories.phtml ligne 41)
        $resultDebut = (($page - 1) * $limite) + 1;

        // Si le numéro de page fois la selection de lignes est supérieur au nombre de categories,
        if(($page * $limite) > $nbrCats[0]) {
            // on affecte à la variable resultFin la valeur du nombre total de categories,
            $resultFin = $nbrCats[0];
        }

        else {
            // sinon on affiche la valeur de la page actuelle multiplié par celle de la limite des lignes à afficher
            $resultFin = $page * $limite;
        }


        // Calcul le nombre de pages
        $nbrPages = ceil($nbrCats[0] / $limite);

        // Si le numéro de la page en cours est différent de 1 et plus grand que le nombre de pages total à afficher ou inférieur à 1 on retourne à la page 1
        if(($page <> 1) && ($page > $nbrPages) OR $page < 1) {
            header("location:/importgames/backoff/cats-page-1");
        }

        // Ou bien si on est à la première page et que le nombre de pages pour afficher les lignes est supérieur à 1 on affiche le lien "Suivant >>" (voir categories.phtml lignes 47 et 116)
        elseif(($page == 1) && ($nbrPages > 1)) {
            $navigation = true;
            $next = $page + 1;
        }

        // Ou bien si la variable page est inférieur au nombre total de pages on affiche "<< Precédent" et "Suivant >>" (voir categories.phtml lignes 44 et 113)
        elseif($page < $nbrPages) {
            $navigation = true;
            $prev = $page - 1;
            $next = $page + 1;
        }

        // Ou bien si on est à la dernière page et que le nombre total de pages est supérieur à 1 on affiche seulement "<< Precédent" (voir categories.phtml lignes 49 et 118)
        elseif(($page == $nbrPages) && ($nbrPages > 1)) {
            $navigation = true;
            $prev = $page - 1;
        }


        // On affecte à l'index 1 du tableau de la variable de session selected la valeur "cat" pour identifier la page actuelle (voir fichiers varback.php ligne 82 et ajax.js ligne 128)
        $_SESSION["selected"][1] = "cat";


        // Si la clé "cat" de la variable de session choixTri n'est pas déclarée ou sa valeur égale à NULL,
        if(!isset($_SESSION["choixTri"]["cat"])) {
            // on affecte à la clé "cat" de la variable de session choixTri un tableau en attribuant à la clé "id" la valeur "triAsc" et à la clé "titre" la valeur spéciale null (voir ligne 207)
            $_SESSION["choixTri"]["cat"] = array("id" => "triAsc", "titre" => null);

            // on affecte à la clé "cat" de la variable de session idTriHidden la valeur "arrowDownId" (voir fichiers varback.php ligne 81 et ajax.js ligne 128)
            $_SESSION["idTriHidden"]["cat"] = "arrowDownId";
        }


        // Si la variable post triCatId est déclarée et sa valeur différente de NULL
        if(isset($_POST["triCatId"])) {
            // On réinitialise l'affichage en attribuant à la clé "id" du tableau affecté à la clé "cat" de la variable de session choixTri sa valeur actuelle et à la clé "titre" la valeur null
            $_SESSION["choixTri"]["cat"] = array("id" => $_SESSION["choixTri"]["cat"]["id"], "titre" => null);

            // Si la clé "id" du tableau affecté à la clé "cat" de la variable de session choixTri correspond à la valeur "triAsc",
            if($_SESSION["choixTri"]["cat"]["id"] == "triAsc") {
                // on lui affecte la valeur "triDesc",
                $_SESSION["choixTri"]["cat"]["id"] = "triDesc";

                // Voir fichiers varback.php ligne 81 et ajax.js ligne 128
                $_SESSION["idTriHidden"]["cat"] = "arrowUpId";
            }

            else {
                // sinon on lui affecte la valeur "triAsc"
                $_SESSION["choixTri"]["cat"]["id"] = "triAsc";

                $_SESSION["idTriHidden"]["cat"] = "arrowDownId";
            }
        }

        elseif(isset($_POST["triCatTitre"])) {
            $_SESSION["choixTri"]["cat"] = array("id" => null, "titre" => $_SESSION["choixTri"]["cat"]["titre"]);

            if($_SESSION["choixTri"]["cat"]["titre"] == "triAsc") {
                $_SESSION["choixTri"]["cat"]["titre"] = "triDesc";

                $_SESSION["idTriHidden"]["cat"] = "arrUpTitre";
            }

            else {
                $_SESSION["choixTri"]["cat"]["titre"] = "triAsc";

                $_SESSION["idTriHidden"]["cat"] = "arrDwnTitre";
            }
        }


        // Requête de base pour afficher et trier les catégories (categories.phtml ligne 86)
        $query = "SELECT * FROM `categorie`";

        // Si la clé "id" du tableau affecté à la clé "cat" de la variable de session choixTri correspond à la valeur "triDesc",
        if($_SESSION["choixTri"]["cat"]["id"] == "triDesc") {
            // on concatène à la variable query la valeur " ORDER BY `id` DESC LIMIT $debut, $limite". On sélectionne toutes les valeurs contenues dans la table "categorie" par tri descendant de la colonne "id" et on les affichent dans la limite définie par les variables debut et limite
            $query .= " ORDER BY `id` DESC LIMIT $debut, $limite";
        }

        // Ou bien si la clé "id" du tableau affecté à la clé "cat" de la variable de session choixTri correspond à la valeur "triAsc",
        elseif($_SESSION["choixTri"]["cat"]["id"] == "triAsc") {
            // on concatène à la variable query la valeur " ORDER BY `id` ASC LIMIT $debut, $limite". On sélectionne toutes les valeurs contenues dans la table "categorie" par tri ascendant de la colonne "id" et on les affichent dans la limite définie par les variables debut et limite
            $query .= " ORDER BY `id` ASC LIMIT $debut, $limite";
        }

        elseif($_SESSION["choixTri"]["cat"]["titre"] == "triDesc") {
            $query .= " ORDER BY `titre` DESC LIMIT $debut, $limite";
        }

        elseif($_SESSION["choixTri"]["cat"]["titre"] == "triAsc") {
            $query .= " ORDER BY `titre` ASC LIMIT $debut, $limite";
        }

        $resultSet = $bdd->query($query);
        $cats = $resultSet->fetchAll();


        if($cats) {
            // On initialise la variable i avec la valeur 0
            $i = 0;

            foreach($cats as $cat) {
                // On stocke dans une variable de session la chaîne de caractères et la variable avec l'index id correspondants à l'attribut id de l'élément html button resetCat pour en récupérer la valeur dans le fichier varback.php (voir fichiers categories.phtml ligne 99, varback.php ligne 76 et ajax.js ligne 76)
                $_SESSION["loopElementId"][1][$i++] = "resetCat" . intval($cat["id"]);
            }

            // On stocke le message de confirmation dans une variable de session pour en récupérer la valeur dans le fichier varback.php (dossier backoff, fichiers varback.php ligne 77 et ajax.js ligne 80)
            $_SESSION["loopMsgConfirm"] = "Voulez-vous vraiment supprimer cette catégorie ? Vous pourrez réutiliser son emplacement avec le bouton REMPLACER.";
        }


        // Si la variable post resetCat est déclarée et différente de NULL
        if(isset($_POST["resetCat"])) {
            $catId = htmlspecialchars($_POST["resetCat"]);
            $formToken = htmlspecialchars(trim($_POST["formToken"])); // On récupère le token de vérification (fichier categories.phtml ligne 101)

            //On vérifie que les 2 jetons sont là
            if(!empty($_SESSION["token"]) AND !empty($formToken)) {
                // On vérifie que les deux correspondent
                if($_SESSION["token"] == $formToken) {
                    // On enlève la vérification du Referer Header pour tester en localhost
                    /* $referer = $_SERVER["HTTP_REFERER"];

                    // On vérifie que la requête vient bien du formulaire
                    if($referer == "https://importgames.llemaitre.com/backoff/pages/categories/categories?page=" . $page OR $referer == "https://importgames.llemaitre.com/backoff/cats-page-" . $page) { */
                        // On remet à zéro les colonnes contenant les informations de la catégorie
                        $query = "UPDATE `categorie` SET `titre` = NULL WHERE `id` = ?";
                        $bdd->insert($query, array($catId));

                        header("location:/importgames/backoff/cats-page-" . $page);
                        exit;
                    /* }

                    else {
                        // La requête vient d'autre part donc on bloque (voir fichier varback.php ligne 79 et ajax.js ligne 118)
                        $_SESSION["refReset"] = "refResetError";
                    } */
                }

                else {
                    // Les tokens ne correspondent pas donc on ne modifie pas
                    $_SESSION["verifReset"] = "verifResetError";
                }
            }

            else {
                // Les tokens sont introuvables donc on ne modifie pas (voir fichier varback.php ligne 78 et ajax.js ligne 113)
                $_SESSION["verifReset"] = "verifResetError";
            }
        }
    }


    // On affecte à la variable de session FILE_PHTML le chemin complet et le nom du fichier courant
    $_SESSION["FILE_PHTML"] = __FILE__;

    // On affecte à la variable de session template le nom et l'extension du fichier courant
    $_SESSION["template"] = substr(strrchr($_SESSION["FILE_PHTML"], "/"), 1);

    // On récupère le nom du fichier courant pour inclure le contenu de la page categories.phtml dans le fichier layout.phtml du dossier "backoff" ligne 188
    $_SESSION["template"] = substr($_SESSION["template"], 0, strrpos($_SESSION["template"], "."));


    // Inclut et exécute le fichier layout.phtml qui hérite de la portée des variables présentes dans categories.php
    if(empty($layout)) {
        $layout = "layout";

        // On limite l'inclusion aux fichiers .phtml en ajoutant dynamiquement l'extension. On supprime également d'éventuels espaces
        $layout = trim($layout . ".phtml");
    }

    // On évite les caractères qui permettent de naviguer dans les répertoires
    $layout = str_replace("../", "protect", $layout);
    $layout = str_replace(";", "protect", $layout);
    $layout = str_replace("%", "protect", $layout);

    // On interdit l'inclusion de dossiers protégés par htaccess
    if(preg_match("/backoff/", $layout)) {
        echo "Vous n'avez pas accès à ce répertoire.";
     }

    else {
        // On vérifie que la page est bien sur le "serveur" en utlisant la constante SITE_DIR du fichier dir.php inclut dans le fichier bdd_connection.php (dossier backoff, fichiers dir.php et bdd_connection.php ligne 43)
        if(file_exists(SITE_DIR . "/" . $layout) && $layout != "index.phtml") {
           include SITE_DIR . "/" . $layout;
        }

        else {
            echo "Page inexistante.";
        }
    }
?>