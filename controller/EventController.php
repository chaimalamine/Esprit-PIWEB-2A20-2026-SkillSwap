<?php
// include_once pour ne charger la classe qu'une seule fois
include_once __DIR__ . '/../model/Event.php';

class EventController {

    public function __construct() {
        if (!isset($_SESSION['events'])) {
            $_SESSION['events'] = array();
        }
    }

    // Ajouter un événement
    public function addEvent() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = $_POST['title'];
            $description = $_POST['description'];
            $location = $_POST['location'];
            $date = $_POST['date'];

            if (!empty($title) && !empty($description)) {
                $newEvent = new Event($title, $description, $location, $date);
                array_push($_SESSION['events'], $newEvent);
            }
        }
    }

    // NOUVEAU : Supprimer un événement
    public function deleteEvent($id) {
        if (isset($_SESSION['events'])) {
            foreach ($_SESSION['events'] as $key => $event) {
                // On vérifie l'ID de l'objet Event
                if ($event->getId() == $id) {
                    unset($_SESSION['events'][$key]); // Supprime l'élément
                    break; // On arrête la boucle une fois trouvé
                }
            }
            // Réindexer le tableau pour éviter les trous (0, 1, 3...)
            $_SESSION['events'] = array_values($_SESSION['events']);
        }
    }

    // Récupérer la liste
    public function listeEvents() {
        return $_SESSION['events'];
    }
}
?>