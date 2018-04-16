<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <link rel="stylesheet" href="CSS/photos.css">
    <?php include 'script/scriptBootStrapHead.php' ?>
    <title>Album photo</title>

</head>
    <header><?php include 'navbar.php';?></header>

<body>
    <?php include('script/connexionBDD.php');

        if(isset($_SESSION['id'])) {

            // Affiche si l'utilisateur a participé à au moins un événement un bouton pour ajouter des photos
            $reponse=$bdd->query('SELECT (ID_evenement)FROM evenements WHERE valide=\'1\' AND ID_utilisateur='.$_SESSION['id'].' LIMIT 0,1');
            $data=$reponse->fetch();
                if(!$data==NULL){ ?>
                   <a href="addPhoto.php" class="btn btn-primary btn-lg" role="button" aria-disabled="true" id="buttonAjouter">Ajouter une photo</a>
                 <?php }


                // Stockage du like lié à une photo dans la BDD
                if(isset($_POST['likeButton']) and isset($_POST['idPhoto']) )
                {
                    $reponse=$bdd->query('SELECT (ID_photo) FROM AIMER WHERE ID_utilisateur='.$_SESSION['id'].' and ID_photo='.$_POST['idPhoto'].' LIMIT 0,1');
                    $data=$reponse->fetch();
                    if($data==NULL){
                        $sql = 'INSERT INTO AIMER (ID_utilisateur, ID_photo) VALUES ('.$_SESSION['id'].','.$_POST['idPhoto'].')';
                        $bdd->exec($sql);
                    }else{
                        $sql='DELETE FROM AIMER WHERE ID_utilisateur='.$_SESSION['id'].' AND ID_photo='.$_POST['idPhoto'];
                        $bdd->exec($sql);
                    }


                }
            // Fermeture pour permettre d'être de nouveau exécutée
            $reponse->closeCursor();


                // Stockage du commentaire lié à une photo dans la BDD
                if(isset($_POST['sendButton']) and isset($_POST['idPhotoComment']) and isset($_POST['comment']) )
                {
                        $sql = 'INSERT INTO COMMENTER (commentaires, ID_utilisateur, ID_evenement, ID_photo) VALUES ("'.$_POST['comment'].'",'.$_SESSION['id'].','.$_POST['idPhotoComment'].')';
                        //$bdd->exec($sql);
                }
        }
    ?>

    <?php
    // Récupère les événements validées et passées
    $reponse=$bdd->query('SELECT (ID_evenement) FROM evenements WHERE valide =  \'1\' AND ((date_evenement) >=  NOW()) ORDER BY date_evenement DESC ');
    $data=$reponse->fetch();

        if($data==NULL){
            echo "<h1 class='my-4 text-center text-lg-left'>Il n'y a aucun événement</h1>";
        }else{

            // Déclation d'un tableau qui contiendra l'ID des événements
            $nom_evenements=array();

                // Ajout des événements dans le tableau $nom_evenements
                do{
                    array_push($nom_evenements,$data['ID_evenement']);
                } while($data=$reponse->fetch());

            $reponse->closeCursor();
            ?>

            <div class="container-fluid" style="float:left;">
            <?php

                for($index=0;$index<sizeof($nom_evenements);$index++){
                    $reponse=$bdd->query('SELECT photos.ID_photo, photos.ID_evenement, evenements.nom_evenement, photos.url_image, titre_photo FROM photos INNER JOIN evenements ON (photos.ID_evenement =
                    evenements.ID_evenement) WHERE photos.ID_evenement ='.$nom_evenements[$index]);
                    $data=$reponse->fetch();
                    ?>
                    <h2><?php echo $data['nom_evenement']; ?></h2>

                    <?php
                    echo "<div class=\"row\">";
                        if($data == NULL){
                            echo "Il y a aucune photo sur l'événénement.";
                        }else{
                            do{
                                ?>
                                <div class="col-s-5">
                                    <div class="thumbnail">
                                  <a href="<?php echo $data['url_image']; ?>">
                                    <img src="<?php echo $data['url_image']; ?>" alt="<?php echo $data['titre_photo']; ?>" style="width:393px;height:263px;">
                                   </a>
                                    <div class="caption" style="display:flex;justify-content:flex-end;">

                                        <?php
                                                if(isset($_SESSION['id'])) {?>
                                                    <form method="post" action="">
                                                        <?php
                                                        $reponseLike=$bdd3->query('SELECT COUNT( ID_photo ) AS nbLike FROM AIMER WHERE ID_photo='.$data['ID_photo']);
                                                        $dataLike=$reponseLike->fetch();
                                                        ?>

                                                     <button type="submit" class="btn btn-link" name="likeButton"><img src="images/like_logo.png" alt="like_logo" /><span class="badge badge-light"><?php echo $dataLike['nbLike'] ?></span></button>
                                                    <?php echo '<input type=hidden name="idPhoto" value='.$data['ID_photo'].' />'; ?>
                                                    </form> <?php

                                                }
                                            ?>


                                            <!-- Trigger the modal with a button -->
                                            <button class="btn btn-link" type="button" data-toggle="modal" data-target="#<?php echo $data['ID_photo']; ?>"><img src="images/comment_logo.png" alt="comment_logo"/></button>

                                            <button class="btn btn-link"><a href="<?php echo $data['url_image'] ?>" download title="Téléchargement de l'image"><img src="images/download_logo.png" alt="download_logo"  />
                                            </a></button>

                                            <!-- Modal -->
                                            <div id="<?php echo $data['ID_photo']; ?>" class="modal fade" role="dialog">
                                              <div class="modal-dialog modal-lg">

                                                <!-- Modal content-->
                                                <div class="modal-content">
                                                  <div class="modal-header">
                                                      <h4 class="modal-title">Commentaires</h4>
                                                      <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
                                                  </div>
                                                  <div class="modal-body">

                                                      <?php

                                                // TASK : Remplacer commentaires par commentaire
                                                $reponseComment=$bdd2->query('SELECT utilisateurs.ID_utilisateur, utilisateurs.nom,utilisateurs.prenom, commentaires FROM COMMENTER INNER JOIN utilisateurs ON COMMENTER.ID_utilisateur = utilisateurs.ID_utilisateur WHERE ID_photo='.$data['ID_photo']);
                                                $dataComment=$reponseComment->fetch();
                                                if(!$dataComment==NULL){
                                                    do{?>
                                                       <h4><?php echo $dataComment['prenom'].' '.$dataComment['nom']; ?></h4>
                                                      <!-- TASK : Remplacer commentaires par commentaire -->
                                                    <p><?php echo $dataComment['commentaires']; ?></p>
                                                    <?php } while($dataComment=$reponseComment->fetch());
                                                    $reponseComment->closeCursor();
                                                    echo "<hr>";
                                                }
?>
                                                        <form method="post" action="">
                                                            <div class="form-group">
                                                                <label for="message-text" class="col-form-label">Message :</label>
                                                                <textarea class="form-control" id="message-text" name="comment"></textarea>
                                                            </div>


                                                            <input type="hidden" name="idPhotoComment" value="<?php echo $data['ID_photo']; ?>" />
                                                            <button type="submit" class="btn btn-primary" name="sendButton" style="float:right;">Envoyer un message</button>
                                                        </form>
                                                  </div>
                                                </div>

                                              </div>
                                            </div>


                                    </div>
                                </div>
                              </div>


                             <?php
                            } while ($data=$reponse->fetch());
                            echo "</div>";
                        }


                    $reponse->closeCursor();

                }
            }
    ?>
    </div>

    <?php include 'script/scriptBootStrapBody.php' ?>
</body>

</html>
