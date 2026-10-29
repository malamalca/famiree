<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\Datasource\ResultSetInterface;
use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Posts Model
 *
 * @property \App\Model\Table\CreatorsTable|\Cake\ORM\Association\BelongsTo $Creators
 * @property \App\Model\Table\PostsLinksTable|\Cake\ORM\Association\HasMany $PostsLinks
 * @property \App\Model\Table\ProfilesTable|\Cake\ORM\Association\belongsToMany $Profiles
 * @method \App\Model\Entity\Post get($primaryKey, $options = [])
 * @method \App\Model\Entity\Post newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Post[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Post|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Post|bool saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Post patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Post[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Post findOrCreate($search, callable $callback = null, $options = [])
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
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('posts');
        $this->setDisplayField('title');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        //$this->addBehavior('Loggable', ['excludedProperties' => ['creator', 'modifier', 'posts_links']]);

        $this->belongsTo('Creators', [
            'className' => 'Profiles',
            'foreignKey' => 'creator_id',
        ]);
        $this->hasMany('PostsLinks', [
            'foreignKey' => 'post_id',
            'dependent' => true,
            'cascadeCallbacks' => true,
        ]);
        $this->belongsToMany('Profiles', [
            'className' => 'Profiles',
            'joinTable' => 'posts_links',
            'foreignKey' => 'post_id',
            'targetForeignKey' => 'foreign_id',
            'conditions' => ['PostsLinks.class' => 'Profile'],
            'dependent' => true,
        ]);
    }

    /**
     * Finder function for posts pagination
     *
     * @param \Cake\ORM\Query\SelectQuery $q Query instance.
     * @return \Cake\ORM\Query\SelectQuery
     */
    public function findPaginated(SelectQuery $q): SelectQuery
    {
        $q
            ->contain(['Creators', 'Profiles'])
            ->orderBy(['Posts.created DESC'])
            ->limit(10);

        return $q;
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('id')
            ->allowEmptyString('id', 'create');

        $validator
            ->scalar('title')
            ->maxLength('title', 100)
            ->allowEmptyString('title');

        $validator
            ->scalar('body')
            ->allowEmptyString('body');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules): RulesChecker
    {
        return $rules;
    }

    /**
     * Fetch for specified profile
     *
     * @param string|int $id Profile id
     * @return \Cake\Datasource\ResultSetInterface
     */
    public function fetchForProfile(string|int $id): ResultSetInterface
    {
        /** @var \Cake\ORM\ResultSet $ret */
        $ret = $this->find()
            ->select()
            ->contain(['Creators'])
            ->innerJoinWith('PostsLinks', function ($q) use ($id) {
                return $q->where(['class' => 'Profile', 'foreign_id' => $id]);
            })
            ->all();

        return $ret;
    }
}
