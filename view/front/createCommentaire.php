<?php
include_once "../../controller/commentaireC.php";
include_once "../../controller/postC.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    session_start();
    $cc = new commentaireC();
    $pc = new postC();
    
    // Validation
    $errors = $cc->validate($_POST);
    
    if (empty($errors)) {
        // Créer l'objet commentaire
        $commentaire = new commentaire(
            $_POST['contenu'],
            date('Y-m-d'),
            $_POST['idpost']
        );
        
        // Enregistrer
        $cc->createCommentaire($commentaire);
        $_SESSION['success'] = "Commentaire ajouté avec succès";
    } else {
        $_SESSION['errors'] = $errors;
    }
    
    // Rediriger vers la page du groupe
    header('Location: groupedetail.php?id=' . $_POST['idgroupe']);
    exit();
}
?>