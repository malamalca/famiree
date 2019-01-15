<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Imgnotes Model
 *
 * @property \App\Model\Table\UsersTable|\Cake\ORM\Association\BelongsTo $Users
 * @property \App\Model\Table\AttachmentsTable|\Cake\ORM\Association\BelongsTo $Attachments
 * @property \App\Model\Table\ProfilesTable|\Cake\ORM\Association\BelongsTo $Profiles
 *
 * @method \App\Model\Entity\ImgNote get($primaryKey, $options = [])
 * @method \App\Model\Entity\ImgNote newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\ImgNote[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\ImgNote|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\ImgNote|bool saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\ImgNote patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\ImgNote[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\ImgNote findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class ImgnotesTable extends Table
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

        $this->setTable('imgnotes');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('Loggable', ['excludedProperties' => ['user', 'attachment', 'profile']]);

        $this->belongsTo('Creators', [
            'className' => 'Profiles',
            'foreignKey' => 'user_id'
        ]);
        $this->belongsTo('Attachments', [
            'foreignKey' => 'attachment_id'
        ]);
        $this->belongsTo('Profiles', [
            'foreignKey' => 'profile_id'
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
            ->integer('x1')
            ->allowEmpty('x1');

        $validator
            ->integer('y1')
            ->allowEmpty('y1');

        $validator
            ->integer('width')
            ->allowEmpty('width');

        $validator
            ->integer('height')
            ->allowEmpty('height');

        $validator
            ->scalar('note')
            ->maxLength('note', 100)
            ->allowEmpty('note');

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
        $rules->add($rules->existsIn(['user_id'], 'Profiles'));
        $rules->add($rules->existsIn(['attachment_id'], 'Attachments'));
        $rules->add($rules->existsIn(['profile_id'], 'Profiles'));

        return $rules;
    }
}
