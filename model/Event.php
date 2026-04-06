<?php
class Event {
    
    private $id;
    private $title;
    private $description;
    private $location;
    private $date;

    
    public function __construct($title, $description, $location, $date, $id = null) {
        $this->id = $id;
        $this->title = $title;
        $this->description = $description;
        $this->location = $location;
        $this->date = $date;
    }

    public function getId() { return $this->id; }
    public function getTitle() { return $this->title; }
    public function getDescription() { return $this->description; }
    public function getLocation() { return $this->location; }
    public function getDate() { return $this->date; }

    
    public function setTitle($t) { $this->title = $t; }
    public function setDescription($d) { $this->description = $d; }
    public function setLocation($l) { $this->location = $l; }
    public function setDate($d) { $this->date = $d; }
}
?>