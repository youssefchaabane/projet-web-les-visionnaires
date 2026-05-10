<?php

class Publication
{
    private $id_pub;
    private $titre;
    private $contenu;
    private $date_publication;
    private $media_url;
    private $id_user;

    public function __construct(
        $id_pub,
        $titre,
        $contenu,
        $date_publication,
        $media_url,
        $id_user
    ) {
        $this->id_pub = $id_pub;
        $this->titre = $titre;
        $this->contenu = $contenu;
        $this->date_publication = $date_publication;
        $this->media_url = $media_url;
        $this->id_user = $id_user;
    }

    public function getIdPub() { return $this->id_pub; }
    public function getTitre() { return $this->titre; }
    public function getContenu() { return $this->contenu; }
    public function getDatePublication() { return $this->date_publication; }
    public function getMediaUrl() { return $this->media_url; }
    public function getIdUser() { return $this->id_user; }

    public function setIdPub($id_pub) { $this->id_pub = $id_pub; }
    public function setTitre($titre) { $this->titre = $titre; }
    public function setContenu($contenu) { $this->contenu = $contenu; }
    public function setDatePublication($date_publication) { $this->date_publication = $date_publication; }
    public function setMediaUrl($media_url) { $this->media_url = $media_url; }
    public function setIdUser($id_user) { $this->id_user = $id_user; }
}
