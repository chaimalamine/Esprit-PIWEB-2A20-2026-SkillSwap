<?php
include_once "../../controller/commentaireC.php";
include_once "../../controller/postC.php";

// ========== VALIDATION DANS LA VUE ==========
function validateCommentaire($data) {
    $errors = [];
    
    if (empty(trim($data['contenu']))) {
        $errors['contenu'] = "Le commentaire est requis";
    } elseif (strlen(trim($data['contenu'])) > 500) {
        $errors['contenu'] = "Le commentaire ne peut pas dépasser 500 caractères";
    } elseif (strlen(trim($data['contenu'])) < 2) {
        $errors['contenu'] = "Le commentaire doit contenir au moins 2 caractères";
    }
    
    return $errors;
}
// =========================================

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    session_start();
    $cc = new commentaireC();
    $pc = new postC();
    
    // Sauvegarder l'ancien commentaire pour l'afficher en cas d'erreur
    $_SESSION['old_comment'] = $_POST['contenu'];
    
    // Validation (appel à la fonction dans la vue)
    $errors = validateCommentaire($_POST);
    
    if (empty($errors)) {
        $commentaire = new commentaire($_POST['contenu'], date('Y-m-d'), $_POST['idpost']);
        $cc->createCommentaire($commentaire);
        $_SESSION['success'] = "Commentaire ajouté avec succès";
        unset($_SESSION['old_comment']);
        unset($_SESSION['comment_errors']);
    } else {
        $_SESSION['comment_errors'] = $errors;
    }
    
    header('Location: groupedetail.php?id=' . $_POST['idgroupe']);
    exit();
}
?>