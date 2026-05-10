<?php
session_start();
include_once "../../controller/commentaireC.php";
include_once "../../controller/postC.php";

function validateCommentaire($data) {
    $errors = [];
    
    if (empty(trim($data['contenu']))) {
        $errors['contenu'] = "Le commentaire est requis";
    } elseif (strlen(trim($data['contenu'])) < 2) {
        $errors['contenu'] = "Le commentaire doit contenir au moins 2 caractères";
    } elseif (strlen(trim($data['contenu'])) > 500) {
        $errors['contenu'] = "Le commentaire ne peut pas dépasser 500 caractères";
    }
    
    return $errors;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sauvegarder l'ancien commentaire
    $_SESSION['old_comment'] = $_POST['contenu'];
    
    // Valider
    $errors = validateCommentaire($_POST);
    
    if (empty($errors)) {
        // Pas d'erreur → on enregistre
        $cc = new commentaireC();
        $commentaire = new commentaire($_POST['contenu'], date('Y-m-d'), $_POST['idpost']);
        $cc->createCommentaire($commentaire);
        $_SESSION['success'] = "Commentaire ajouté avec succès";
        unset($_SESSION['old_comment']);
        unset($_SESSION['comment_errors']);
    } else {
        // Erreur → on n'enregistre pas
        $_SESSION['comment_errors'] = $errors;
    }
    
    // Redirection (ACTIVÉE)
    header('Location: groupedetail.php?id=' . $_POST['idgroupe']);
    exit();
}
?>