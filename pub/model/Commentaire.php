<?php

class Commentaire
{
    private $id_commentaire;
    private $note;
    private $contenu;
    private $date_commentaire;
    private $likes_count;
    private $id_pub;

    public function __construct(
        $id_commentaire,
        $note,
        $contenu,
        $date_commentaire,
        $likes_count,
        $id_pub
    ) {
        $this->id_commentaire = $id_commentaire;
        $this->note = $note;
        $this->contenu = $contenu;
        $this->date_commentaire = $date_commentaire;
        $this->likes_count = $likes_count;
        $this->id_pub = $id_pub;
    }

    public function getIdCommentaire() { return $this->id_commentaire; }
    public function getNote() { return $this->note; }
    public function getContenu() { return $this->contenu; }
    public function getDateCommentaire() { return $this->date_commentaire; }
    public function getLikesCount() { return $this->likes_count; }
    public function getIdPub() { return $this->id_pub; }

    public function setIdCommentaire($id_commentaire) { $this->id_commentaire = $id_commentaire; }
    public function setNote($note) { $this->note = $note; }
    public function setContenu($contenu) { $this->contenu = $contenu; }
    public function setDateCommentaire($date_commentaire) { $this->date_commentaire = $date_commentaire; }
    public function setLikesCount($likes_count) { $this->likes_count = $likes_count; }
    public function setIdPub($id_pub) { $this->id_pub = $id_pub; }
}
