<?php
namespace App\Model\Table;

use App\Model\Behavior\LoggableBehavior;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Posts Model
 *
 * @property \App\Model\Table\BlogsTable|\Cake\ORM\Association\BelongsTo $Blogs
 * @property \App\Model\Table\CreatorsTable|\Cake\ORM\Association\BelongsTo $Creators
 * @property \App\Model\Table\ModifiersTable|\Cake\ORM\Association\BelongsTo $Modifiers
 * @property \App\Model\Table\CommentsTable|\Cake\ORM\Association\HasMany $Comments
 * @property \App\Model\Table\PostsLinksTable|\Cake\ORM\Association\HasMany $PostsLinks
 *
 * @method \App\Model\Entity\Post get($primaryKey, $options = [])
 * @method \App\Model\Entity\Post newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Post[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Post|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Post|bool saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Post patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Post[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Post findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class PostsTable extends Table
{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('posts');
        $this->setDisplayField('title');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('Loggable', ['excludedProperties' => ['creator', 'modifier', 'posts_links']]);

        $this->belongsTo('Creators', [
            'className' => 'Profiles',
            'foreignKey' => 'creator_id'
        ]);
        $this->hasMany('PostsLinks', [
            'foreignKey' => 'post_id'
        ]);
        $this->belongsToMany('Profiles', [
            'className' => 'Profiles',
            'joinTable' => 'posts_links',
            'foreignKey' => 'post_id',
            'targetForeignKey' => 'foreign_id',
            'conditions' => ['PostsLinks.class' => 'Profile'],
            'dependent' => true
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->integer('id')
            ->allowEmpty('id', 'create');

        $validator
            ->scalar('title')
            ->maxLength('title', 100)
            ->allowEmpty('title');

        $validator
            ->scalar('slug')
            ->maxLength('slug', 100)
            ->allowEmpty('slug');

        $validator
            ->scalar('body')
            ->allowEmpty('body');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules)
    {
        return $rules;
    }

    /**
     * Fetch for specified profile
     *
     * @param int $id Profile id
     * @return Cake\ORM\ResultSet
     */
    public function fetchForProfile($id)
    {
        return $this->find()
            ->select()
            ->contain(['Creators'])
            ->innerJoinWith('PostsLinks', function ($q) use ($id) {
                return $q->where(['class' => 'Profile', 'foreign_id' => $id]);
            })
            ->all();
    }
}
