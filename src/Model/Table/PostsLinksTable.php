<?php
namespace App\Model\Table;

use ArrayObject;
use Cake\Event\Event;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;

/**
 * PostsLinks Model
 *
 * @method \App\Model\Entity\PostsLink get($primaryKey, $options = [])
 * @method \App\Model\Entity\PostsLink newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\PostsLink[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\PostsLink|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\PostsLink|bool saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\PostsLink patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\PostsLink[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\PostsLink findOrCreate($search, callable $callback = null, $options = [])
 */
class PostsLinksTable extends Table
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

        $this->setTable('posts_links');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');
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
            ->allowEmptyString('id', 'create');

        $validator
            ->scalar('class')
            ->maxLength('class', 20)
            ->requirePresence('class', 'create')
            ->allowEmptyString('class', false);

        return $validator;
    }

    /**
     * Aftersave event handler
     *
     * @param Event $event Event object
     * @param \App\Model\Entity\PostsLink $entity Entity object
     * @param ArrayObject $options Options
     * @return void
     */
    public function afterSave(Event $event, \App\Model\Entity\PostsLink $entity, ArrayObject $options)
    {
        if ($entity->class == 'Profile') {
            $profilesToUpdate = [$entity->foreign_id];
            $previousProfileId = $entity->getOriginal('foreign_id');
            if ($entity->foreign_id != $previousProfileId) {
                $profilesToUpdate[] = $previousProfileId;
            }
            /** @var \App\Model\Table\ProfilesTable $ProfilesTable */
            $ProfilesTable = TableRegistry::get('Profiles');
            $ProfilesTable->updateMemoryCount($profilesToUpdate);
        }
    }

    /**
     * Afterdelete event handler
     *
     * @param Event $event Event object
     * @param \App\Model\Entity\PostsLink $entity Entity object
     * @param ArrayObject $options Options
     * @return void
     */
    public function afterDelete(Event $event, \App\Model\Entity\PostsLink $entity, ArrayObject $options)
    {
        if ($entity->class == 'Profile') {
            /** @var \App\Model\Table\ProfilesTable $ProfilesTable */
            $ProfilesTable = TableRegistry::get('Profiles');
            $ProfilesTable->updateMemoryCount([$entity->foreign_id]);
        }
    }
}
