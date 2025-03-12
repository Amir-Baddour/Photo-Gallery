<?php
class PhotoSkeleton {
    private $id;
    private $user_id;
    private $title;
    private $description;
    private $tags;
    private $image_path;
    private $created_at;

    public function __construct($id = null, $user_id = null, $title = null, $description = null, $tags = null, $image_path = null, $created_at = null) {
        $this->id         = $id;
        $this->user_id    = $user_id;
        $this->title      = $title;
        $this->description = $description;
        $this->tags       = $tags;
        $this->image_path = $image_path;
        $this->created_at = $created_at;
    }

    public function getId() {
        return $this->id;
    }
    public function setId($id) {
        $this->id = $id;
    }

    public function getUserId() {
        return $this->user_id;
    }
    public function setUserId($user_id) {
        $this->user_id = $user_id;
    }

    public function getTitle() {
        return $this->title;
    }
    public function setTitle($title) {
        $this->title = $title;
    }

    public function getDescription() {
        return $this->description;
    }
    public function setDescription($description) {
        $this->description = $description;
    }

    public function getTags() {
        return $this->tags;
    }
    public function setTags($tags) {
        $this->tags = $tags;
    }

    public function getImagePath() {
        return $this->image_path;
    }
    public function setImagePath($image_path) {
        $this->image_path = $image_path;
    }

    public function getCreatedAt() {
        return $this->created_at;
    }
    public function setCreatedAt($created_at) {
        $this->created_at = $created_at;
    }
}
?>
