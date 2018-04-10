<!DOCTYPE html>
<html>
    <head>

        <meta charset="utf-8" />
        <link rel="stylesheet" href="CSS/photos.css">
        <?php include 'script/scriptBootStrapHead.php' ?>
        <title>Publication d'une photo</title>

    </head>
    <body>
        <?php include 'navbar.php';?>

        <?php
        try
        {
            // On se connecte à MySQL
            $bdd = new PDO('mysql:host=localhost;dbname=bddphoto','root','');
        }
        catch(Exception $e)
        {
            // En cas d'erreur, on affiche un message et on arrête tout
            die('Erreur : '.$e->getMessage());
        }

            // On récupère le contenu du champ nom_evenement
            $reponse = $bdd->query('SELECT * FROM evenements');

        ?>

        <?php if($donnees = $reponse->fetch()){ ?>

        <div class="container" id="placement">
        <h1>Formulaire publication photo</h1>
        <form method="post" action="resultPhoto.php" enctype="multipart/form-data">

        <div class="form-group">
            <label for="titre_photo">Titre de l'image</label>
            <input class="form-control" type="text" name="titre_photo" id="titre_photo"/>
        </div>

        <div class="form-group">
            <label for="evenement">Veuillez choisir l'événement :</label>
            <select class="form-control" id="evenement" name='choix'>
                <?php
                do {
                ?>
                    <option value="<?php echo $donnees['ID_evenement']; ?>"><?php echo $donnees['nom_evenement']; ?></option>
                <?php
                } while ($donnees = $reponse->fetch());

            // Termine le traitement de la requête
            $reponse->closeCursor();
            ?>
            </select>
        </div>

        <div class="form-group">
            <label for="piecejointe">Pièce Jointe :</label>
            <input class="form-control" type="file" name="monfichier" id="piecejointe" />
            <label for="piecejointe">Format accepté : png, jpeg et jpg <br />
            Limite du fichier : 15 Mo</label>
        </div>

        <input type="hidden" name="username" value=1 />
        <button type="submit" class="btn btn-default">Publier</button>

        </form>
        </div>

        <?php
        }else{
            echo "<br/><br /><br /><h1>Vous ne pouvez pas publier une photo car il n'y a pas d'événement.</h1>";
        }
        ?>

        <?php include 'script/scriptBootStrapBody.php' ?>
    </body>
</html>