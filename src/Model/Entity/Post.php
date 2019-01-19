<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Post Entity
 *
 * @property int $id
 * @property int $blog_id
 * @property int $status
 * @property string|null $title
 * @property string|null $slug
 * @property string|null $body
 * @property \Cake\I18n\FrozenTime|null $created
 * @property int|null $creator_id
 * @property \Cake\I18n\FrozenTime|null $modified
 * @property int|null $modifier_id
 *
 * @property \App\Model\Entity\Creator $creator
 * @property \App\Model\Entity\Modifier $modifier
 * @property \App\Model\Entity\PostsLink[] $posts_links
 */
class Post extends Entity
{

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        'blog_id' => true,
        'status' => true,
        'title' => true,
        'slug' => true,
        'body' => true,
        'created' => true,
        'creator_id' => true,
        'modified' => true,
        'modifier_id' => true,

        'creator' => true,
        'modifier' => true,
        'posts_links' => true
    ];
}
