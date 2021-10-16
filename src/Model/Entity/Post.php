<?php
declare(strict_types=1);

namespace App\Model\Entity;

class Post
{
    public $id;
    public $blog_id;
    public $status;
    public $title;
    public $slug;
    public $body;
    public $created;
    public $creator_id;
    public $modified;
    public $modifier_id;
}
