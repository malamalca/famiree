<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * AttachmentsLinks Model
 *
 * @property \App\Model\Table\AttachmentsTable|\Cake\ORM\Association\BelongsTo $Attachments
 * @property \App\Model\Table\ForeignsTable|\Cake\ORM\Association\BelongsTo $Foreigns
 *
 * @method \App\Model\Entity\AttachmentsLink get($primaryKey, $options = [])
 * @method \App\Model\Entity\AttachmentsLink newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\AttachmentsLink[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\AttachmentsLink|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\AttachmentsLink|bool saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\AttachmentsLink patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\AttachmentsLink[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\AttachmentsLink findOrCreate($search, callable $callback = null, $options = [])
 */
class AttachmentsLinksTable extends Table
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

        $this->setTable('attachments_links');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('Attachments', [
            'foreignKey' => 'attachment_id'
        ]);
        $this->belongsTo('Profiles', [
            'className' => 'Profiles',
            'conditions' => ['class' => 'Profile'],
            'foreignKey' => 'foreign_id',
            'joinType' => 'INNER'
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
            ->scalar('class')
            ->maxLength('class', 7)
            ->requirePresence('class', 'create')
            ->notEmpty('class');

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
        $rules->add($rules->existsIn(['attachment_id'], 'Attachments'));

        return $rules;
    }

    /**
     * Link profile and attachment
     *
     * @param int $profileId Profile id
     * @param string $attachmentId Attachment id
     * @return bool
     */
    public function linkProfile($profileId, $attachmentId)
    {
        if (!$this->exists(['attachment_id' => $attachmentId, 'class' => 'Profile', 'foreign_id' => $profileId])) {
            $attachemntsLink = $this->newEntity(['attachment_id' => $attachmentId, 'class' => 'Profile', 'foreign_id' => $profileId]);

            return (bool)$this->save($attachemntsLink);
        } else {
            return false;
        }
    }
}
