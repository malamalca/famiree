<?php
declare(strict_types=1);

namespace App\Model\Table;

use App\Core\DB;
use App\Core\Table;
use App\Model\Entity\Device;

class PostsTable extends Table
{
    public $entityName = '\App\Model\Entity\Post';
    public $tableName = 'posts';

    public function fetchPosts($limit, $offset = null)
    {
        $posts = $this->query("SELECT * FROM posts ORDER BY posts.created DESC LIMIT $limit");

        $postsIds = [];
        foreach ($posts as $post) {
            $postsIds[] = $post->id;
        }
        $postsLinks = $ProfilesTable->query('SELECT posts_links.post_id, profiles.* FROM posts_links ' .
            'LEFT JOIN profiles ON posts_links.foreign_id = profiles.id ' .
            'WHERE posts_links.class="Profile" AND posts_links.post_id IN (:posts)',
            ['posts' => implode(',', $postsIds)],
            ['group' => 'post_id']
        );

        foreach ($posts as $i => $post) {
            if (isset($postsLinks[$post->id])) {
                $posts[$i]->posts_links = $postsLinks[$post->id];
            } else {
                $posts[$i]->posts_links = [];
            }
        }
    }

}
