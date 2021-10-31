<?php
declare(strict_types=1);

namespace App\Controller;

use App\Core\App;
use App\Core\Configure;
use App\Model\Table\PostsTable;
use App\Model\Table\ProfilesTable;

class PostsController
{
    /**
     * index action
     *
     * @return void
     */
    public function index()
    {
        $ProfilesTable = new ProfilesTable();
        $PostsTable = new PostsTable();

        $posts = $PostsTable->query("SELECT * FROM posts ORDER BY posts.created DESC LIMIT 5");

        App::set(compact('posts'));
    }
}
