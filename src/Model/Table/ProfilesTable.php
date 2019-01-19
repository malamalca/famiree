<?php
namespace App\Model\Table;

use App\Model\Table\Traits\FamilyTreeTrait;
use ArrayObject;
use Cake\Cache\Cache;
use Cake\Core\Configure;
use Cake\Database\Expression\QueryExpression;
use Cake\Datasource\EntityInterface;
use Cake\Event\Event;
use Cake\Mailer\Email;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;
use Cake\Validation\Validator;

/**
 * Profiles Model
 *
 * @property \App\Model\Table\CreatorsTable|\Cake\ORM\Association\BelongsTo $Creators
 * @property \App\Model\Table\ModifiersTable|\Cake\ORM\Association\BelongsTo $Modifiers
 * @property \App\Model\Table\ImgnotesTable|\Cake\ORM\Association\HasMany $Imgnotes
 * @property \App\Model\Table\SettingsTable|\Cake\ORM\Association\HasMany $Settings
 * @property \App\Model\Table\UnitsTable|\Cake\ORM\Association\HasMany $Units
 *
 * @method \App\Model\Entity\Profile get($primaryKey, $options = [])
 * @method \App\Model\Entity\Profile newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Profile[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Profile|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Profile|bool saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Profile patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Profile[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Profile findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class ProfilesTable extends Table
{
    use FamilyTreeTrait;
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('profiles');
        $this->setDisplayField('d_n');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('Loggable', ['excludedProperties' => ['created', 'modified', 'units', 'marriages']]);

        $this->belongsTo('Creators', [
            'className' => 'Profiles',
            'foreignKey' => 'creator_id'
        ]);
        $this->belongsToMany('Attachments', [
            'joinTable' => 'attachments_links',
            'foreignKey' => 'foreign_id',
            'dependent' => true
        ]);

        $this->belongsToMany('Posts', [
            'joinTable' => 'posts_links',
            'foreignKey' => 'foreign_id',
            'conditions' => ['PostsLinks.class' => 'Profile'],
            'dependent' => true
        ]);

        $this->belongsToMany('Marriages', [
            'className' => 'Unions',
            'joinTable' => 'units',
            'foreignKey' => 'profile_id',
            'targetForeignKey' => 'union_id',
            'conditions' => ['Units.kind' => 'p'],
            'dependent' => true
        ]);
        $this->hasMany('Units', [
            'foreignKey' => 'profile_id',
            'cascadeCallbacks' => true,
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
            ->allowEmpty('id', 'create')
            ->notEmpty('fn');

        return $validator;
    }

    /**
     * validationResetPassword validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     *
     * @return \Cake\Validation\Validator
     */
    public function validationResetPassword($validator)
    {
        $validator = new Validator();
        $validator
            ->add('p', 'minLength', ['rule' => ['minLength', 4]])
            ->requirePresence(
                'repeat_pass',
                function ($context) {
                    return !empty($context['data']['p']);
                }
            )
            ->notEmpty('repeat_pass')
            ->add('repeat_pass', 'match', [
                    'rule' => function ($value, $context) {
                        return $value == $context['data']['p'];
                    }
                ]);

        return $validator;
    }

    /**
     * Install validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     *
     * @return \Cake\Validation\Validator
     */
    public function validationInstall($validator)
    {
        $validator = new Validator();
        $validator
            ->notEmpty('fn')
            ->notEmpty('ln')
            ->notEmpty('u')
            ->notEmpty('e')
            ->email('e')
            ->add('p', 'minLength', ['rule' => ['minLength', 4]])
            ->requirePresence(
                'repeat_pass',
                function ($context) {
                    return !empty($context['data']['p']);
                }
            )
            ->notEmpty('repeat_pass')
            ->add('repeat_pass', 'match', [
                    'rule' => function ($value, $context) {
                        return $value == $context['data']['p'];
                    }
                ]);

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
        //$rules->add($rules->existsIn(['creator_id'], 'Creators'));
        //$rules->add($rules->existsIn(['modifier_id'], 'Modifiers'));

        return $rules;
    }

    /**
     * beforeSave event handler
     *
     * @param Event $event Event object
     * @param \App\Model\Entity\Profile $entity Entity object
     * @param ArrayObject $options Options
     * @return bool
     */
    public function beforeSave(Event $event, \App\Model\Entity\Profile $entity, ArrayObject $options)
    {
        if ($entity->isDirty('fn') || $entity->isDirty('mn') || $entity->isDirty('ln')) {
            $entity->d_n = trim(trim($entity->fn . ' ' . $entity->mn) . ' ' . $entity->ln);
        }
        Cache::delete('Profiles');

        return true;
    }

    /**
     * Build family tree for specified profile
     *
     * @param int $id Profile id
     * @param string|array $type Which part of family to fetch: parents, children, siblings, marriages, spouses
     * @param bool $assoc Fetch associativly (array keys equal profile id)
     * @return array
     */
    public function family($id, $type = null, $assoc = false)
    {
        $ret = [];

        $data = TableRegistry::get('Profiles')->get($id);

        /** @var \App\Model\Entity\Unit $family */
        $family = TableRegistry::get('Units')->find()
            ->select(['union_id'])
            ->where(['profile_id' => $id, 'kind' => 'c'])
            ->first();

        $parents = [];
        $siblings = [];
        if (!empty($family)) {
            if (empty($type) || $type == 'parents' || (is_array($type) && in_array('parents', $type))) {
                $parents = TableRegistry::get('Profiles')->find()
                    ->select()
                    ->matching('Units', function ($q) use ($family) {
                        return $q->where(['Units.union_id' => $family->union_id, 'Units.kind' => 'p']);
                    })
                    ->order(['Profiles.g DESC'])
                    ->toArray();

                if ($assoc) {
                    $parents2 = [];
                    foreach ($parents as $parent) {
                        $parents2[$parent->id] = $parent;
                    }
                    $parents = $parents2;
                }

                if ($type == 'parents') {
                    return $parents;
                }
                $ret['parents'] = $parents;
            }

            if (empty($type) || $type == 'siblings' || (is_array($type) && in_array('siblings', $type))) {
                $siblings = TableRegistry::get('Profiles')->find()
                    ->select()
                    ->matching('Units', function ($q) use ($id, $family) {
                        return $q->where(['Units.union_id' => $family->union_id, 'Units.kind' => 'c', 'Units.profile_id !=' => $id]);
                    })
                    ->order(['Units.sort_order', 'Profiles.dob_y'])
                    ->toArray();

                if ($assoc) {
                    $siblings2 = [];
                    foreach ($siblings as $sibling) {
                        $siblings2[$sibling->id] = $sibling;
                    }
                    $siblings = $siblings2;
                }
                if ($type == 'siblings') {
                    return $siblings;
                }
                $ret['siblings'] = $siblings;
            }
        }

        if (empty($type) || $type == 'marriages' || $type == 'spouses' || $type == 'children' ||
        (is_array($type) && (in_array('marriages', $type) || in_array('spouses', $type) || in_array('children', $type)))) {
            $marr = TableRegistry::get('Units')->find()
                ->select(['union_id'])
                ->where(['profile_id' => $id, 'kind' => 'p'])
                ->all();

            $marriages = [];
            $spouses = [];
            $children = [];
            foreach ($marr as $m_k => $marriage) {
                if ($assoc) {
                    $marriage_key = $marriage->union_id;
                } else {
                    $marriage_key = $m_k;
                }

                $marriages[$marriage_key]['spouse'] = TableRegistry::get('Profiles')->find()
                    ->matching('Units', function ($q) use ($id, $marriage) {
                        return $q->where(['Units.union_id' => $marriage->union_id, 'Units.kind' => 'p', 'Units.profile_id !=' => $id]);
                    })
                    ->first();
                if ($assoc) {
                    $spouses[$marriages[$marriage_key]['spouse']->id] = $marriages[$marriage_key]['spouse'];
                } else {
                    $spouses[] = $marriages[$marriage_key]['spouse'];
                }

                if (empty($type) || $type == 'marriages' || $type == 'children' ||
                (is_array($type) && (in_array('marriages', $type) || (in_array('marriages', $type) || in_array('children', $type))))) {
                    $marriages[$marriage_key]['children'] = TableRegistry::get('Profiles')->find()
                        ->matching('Units', function ($q) use ($marriage) {
                            return $q->where(['Units.union_id' => $marriage->union_id, 'Units.kind' => 'c']);
                        })
                        ->order(['Units.sort_order', 'Profiles.dob_y'])
                        ->toArray();

                    if ($assoc) {
                        foreach ($marriages[$marriage_key]['children'] as $child) {
                            $children[$child->id] = $child;
                        }
                    } else {
                            $children = array_merge($children, $marriages[$marriage_key]['children']);
                    }
                }
            }

            if (empty($type) || is_array($type)) {
                if (empty($type) || in_array('marriages', $type)) {
                    $ret['marriages'] = $marriages;
                }
                if (empty($type) || in_array('spouses', $type)) {
                    $ret['spouses'] = $marriages;
                }
                if (empty($type) || in_array('children', $type)) {
                    $ret['children'] = $children;
                }
            } else {
                if ($type == 'marriages') {
                    return $marriages;
                } elseif ($type == 'spouses') {
                    return $spouses;
                } else {
                    return $children;
                }
            }
        }

        return $ret;
    }

    /**
     * Costum finder method "search"
     *
     * @param Query $query Query object.
     * @param array $options Options.
     * @return Query
     */
    public function findSearch(Query $query, array $options)
    {
        if (!empty($options['criterio']) && strlen($options['criterio']) > 1) {
            $query->where([
                'OR' => [
                    'd_n LIKE' => '%' . $options['criterio'] . '%',
                    'mdn LIKE' => '%' . $options['criterio'] . '%',
                    'loc LIKE' => '%' . $options['criterio'] . '%',
                ]
            ]);
        }

        $query->order(['ln', 'fn']);

        return $query;
    }

    /**
     * Find gender counts
     *
     * @return array Counts
     */
    public function countGenders()
    {
        $query = $this->find();
        $counts = $query
            ->select([
                'g',
                'count' => $query->func()->count('*'),
            ])
            ->hydrate(false)
            ->group(['g'])
            ->toArray();

        $counts = Hash::combine($counts, '{n}.g', '{n}.count');
        if (!isset($counts['f'])) {
            $counts['f'] = 0;
        }
        if (!isset($counts['m'])) {
            $counts['m'] = 0;
        }

        return $counts;
    }

    /**
     * Sends reset email
     *
     * @param \App\Model\Entity\Profile $user User entity.
     *
     * @return bool
     */
    public function sendResetEmail($user)
    {
        $reset_key = uniqid();
        $user->rst = $reset_key;
        if ($this->save($user)) {
            $email = new Email('default');
            $email->from([Configure::read('from.email') => Configure::read('from.name')]);
            $email->to($user->e);
            $email->subject(__('Password Reset'));

            $email->template('reset');
            $email->emailFormat('text');
            $email->viewVars(['reset_key' => $reset_key]);
            $email->helpers(['Html']);

            $ret = $email->send();

            return (bool)$ret;
        }

        return false;
    }

    /**
     * Fetch profiles with concatenated date of birth, ordered by dob
     *
     * @return \Cake\ORM\ResultSet
     */
    public function withBirthdays()
    {
        //Cache::delete('Profiles.birthdays');
        $ret = Cache::remember('Profiles.birthdays', function () {
            $q = $this->find();
            $fieldExpr = $q->newExpr()->add('DATE_ADD(DATE_ADD(MAKEDATE(dob_y, 1), INTERVAL (dob_m)-1 MONTH), INTERVAL (dob_d)-1 DAY)');
            $diffExpr = $q->newExpr()->add('DATEDIFF(DATE_ADD( DATE_ADD( MAKEDATE(YEAR(CURDATE()), 1), INTERVAL (dob_m)-1 MONTH ), INTERVAL (dob_d)-1 DAY ), CURDATE() )');
            $dates = $q
                ->select($this)
                ->select(['dob' => $fieldExpr])
                ->select(['diff' => $diffExpr])
                ->where(['l' => true])
                ->andWhere(function (QueryExpression $whereExpr) use ($fieldExpr) {
                    return $whereExpr->isNotNull($fieldExpr);
                })
                ->andWhere(function (QueryExpression $andWhereExpr) use ($diffExpr) {
                    return $andWhereExpr->gt($diffExpr, 0, 'integer');
                })
                ->order(['diff'])
                ->limit(20)
                ->all();

            return $dates;
        });

        return $ret;
    }
}
