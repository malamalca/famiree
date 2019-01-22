<?php
namespace App\Model\Table;

use ArrayObject;
use Cake\Core\Configure;
use Cake\Datasource\EntityInterface;
use Cake\Event\Event;
use Cake\Filesystem\Folder;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Utility\Text;
use Cake\Validation\Validator;
use WideImage\WideImage;

/**
 * Attachments Model
 *
 * @property \App\Model\Table\UsersTable|\Cake\ORM\Association\BelongsTo $Users
 * @property \App\Model\Table\AttachmentsLinksTable|\Cake\ORM\Association\HasMany $AttachmentsLinks
 * @property \App\Model\Table\ImgnotesTable|\Cake\ORM\Association\HasMany $Imgnotes
 *
 * @method \App\Model\Entity\Attachment get($primaryKey, $options = [])
 * @method \App\Model\Entity\Attachment newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Attachment[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Attachment|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Attachment|bool saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Attachment patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Attachment[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Attachment findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class AttachmentsTable extends Table
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

        $this->setTable('attachments');
        $this->setDisplayField('title');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('Loggable', ['excludedProperties' => ['user', 'attachments_links', 'imgnotes']]);

        $this->belongsTo('Creators', [
            'className' => 'Profiles',
            'foreignKey' => 'user_id'
        ]);
        $this->hasMany('AttachmentsLinks', [
            'foreignKey' => 'attachment_id',
            'dependent' => true,
            'cascadeCallbacks' => true,
        ]);
        $this->hasMany('Imgnotes', [
            'foreignKey' => 'attachment_id',
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
            ->uuid('id')
            ->allowEmpty('id', 'create');

        $validator
            ->allowEmptyFile('filename');
        $validator->add('filename', [
            'mime' => [
                'rule' => ['mimeType', ['image/jpeg', 'image/png', 'image/gif']]
            ],
            'size' => [
                'rule' => ['fileSize', '<', '5MB']
            ],
        ]);

        $validator
            ->scalar('original')
            ->maxLength('original', 255)
            ->allowEmpty('original');

        $validator
            ->scalar('ext')
            ->maxLength('ext', 6)
            ->requirePresence('ext', 'create')
            ->notEmpty('ext');

        $validator
            ->scalar('mimetype')
            ->maxLength('mimetype', 30)
            ->allowEmpty('mimetype');

        $validator
            ->integer('filesize')
            ->allowEmpty('filesize');

        $validator
            ->integer('height')
            ->allowEmpty('height');

        $validator
            ->integer('width')
            ->allowEmpty('width');

        $validator
            ->scalar('title')
            ->maxLength('title', 100)
            ->allowEmpty('title');

        $validator
            ->scalar('description')
            ->allowEmpty('description');

        $validator
            ->scalar('checksum')
            ->maxLength('checksum', 32)
            ->allowEmpty('checksum');

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
        //$rules->add($rules->existsIn(['user_id'], 'Users'));

        return $rules;
    }

    /**
     * beforeMarshal event handler
     *
     * @param Event $event Event object
     * @param ArrayObject $data Data object
     * @param ArrayObject $options Options
     * @return void
     */
    public function beforeMarshal(Event $event, ArrayObject $data, ArrayObject $options)
    {
        if (!empty($data['filename']['tmp_name']) && file_exists($data['filename']['tmp_name'])) {
            $data['original'] = $data['filename']['name'];
            $data['mimetype'] = $data['filename']['type'];
            $data['filesize'] = $data['filename']['size'];
            $data['checksum'] = md5_file($data['filename']['tmp_name']);
            $data['ext'] = strtolower(pathinfo($data['filename']['name'], PATHINFO_EXTENSION));

            if (substr($data['mimetype'], 0, 5) == 'image') {
                $imageInfo = getimagesize($data['filename']['tmp_name']);
                $data['width'] = $imageInfo[0];
                $data['height'] = $imageInfo[1];
            }
        }
    }

    /**
     * afterDelete event handler
     *
     * @param Event $event Event object
     * @param EntityInterface $entity Entity object
     * @param ArrayObject $options Options
     * @return void
     */
    public function afterDelete(Event $event, EntityInterface $entity, ArrayObject $options)
    {
        $dir = (new Folder(Configure::read('sourceFolders.attachments') . $entity->id))->delete();
        $thumb = Configure::read('sourceFolders.thumbs') . $entity->id . '.png';
        if (file_exists($thumb)) {
            unlink($thumb);
        }
    }

    /**
     * Fetch for specified profile
     *
     * @param int $id Profile id
     * @return \Cake\ORM\ResultSet
     */
    public function fetchForProfile($id)
    {
        /** @var \Cake\ORM\ResultSet $ret */
        $ret = $this->find()
            ->select()
            ->innerJoinWith('AttachmentsLinks', function ($q) use ($id) {
                return $q->where(['class' => 'Profile', 'foreign_id' => $id]);
            })
            ->all();

        return $ret;
    }

    /**
     * Process uploaded attachment
     *
     * @param \App\Model\Entity\Attachment $entity Attachment entity
     * @param string $tmpFilename Temporary file name
     * @param string $method Move file method - 'uploaded' or 'existing'
     * @return void
     */
    public function processUpload($entity, $tmpFilename, $method = 'uploaded')
    {
        $checkExists = ($method == 'uploaded') ? is_uploaded_file($tmpFilename) : file_exists($tmpFilename);
        if ($checkExists) {
            $image = WideImage::loadFromFile($tmpFilename, $entity->ext);
            if (!empty($image)) {
                $dir = new Folder(Configure::read('sourceFolders.attachments') . $entity->id, true);

                if (file_exists($dir->path . DS . 'original')) {
                    unlink($dir->path . DS . 'original');
                }

                $moveResult = ($method == 'uploaded') ? move_uploaded_file($tmpFilename, $dir->path . DS . 'original') : rename($tmpFilename, $dir->path . DS . 'original');
                if ($moveResult) {
                    $image->resize(640, 480, 'inside')->saveToFile($dir->path . DS . 'large', $entity->ext);
                    $image->resize(200, 200, 'inside')->saveToFile($dir->path . DS . 'medium', $entity->ext);

                    $thumb = $image->resize(75, 75, 'outside');
                    if ($thumb->getWidth() > $thumb->getHeight()) {
                        $thumb = $thumb->crop(floor(($thumb->getWidth() - 75) / 2), 0, 75, 75);
                    } elseif ($thumb->getWidth() < $thumb->getHeight()) {
                        $thumb = $thumb->crop(0, floor(($thumb->getHeight() - 75) / 2), 75, 75);
                    }
                    $thumb->saveToFile(Configure::read('sourceFolders.thumbs') . $entity->id . '.png', null, 9);
                }
                unset($image);
            }
        }
    }

    /**
     * Extract image area from specified attachment and create new image
     *
     * @param \App\Model\Entity\ImgNote $imgnote Imgnote entity
     * @return \App\Model\Entity\Attachment|bool
     */
    public function createFromImgnote($imgnote)
    {
        $filenameOriginal = Configure::read('sourceFolders.attachments') . DS . $imgnote->attachment_id . DS . 'original';
        if (file_exists($filenameOriginal)) {
            $attachment = $this->get($imgnote->attachment_id);

            $largeSize = $this->getImageSize($imgnote->attachment_id, 'large');
            $originalSize = $this->getImageSize($imgnote->attachment_id, 'original');

            $scaleFactor = $originalSize['width'] / $largeSize['width'];
            $x = (int)round($imgnote->x1 * $scaleFactor);
            $y = (int)round($imgnote->y1 * $scaleFactor);
            $width = (int)round($imgnote->width * $scaleFactor);
            $height = (int)round($imgnote->height * $scaleFactor);

            $tmpfname = tempnam(defined('TMP') ? constant('TMP') : null, 'fam');
            $image = WideImage::loadFromFile($filenameOriginal, $attachment->ext);
            if (!empty($image)) {
                $image = $image->crop($x, $y, $width, $height);
                $image->saveToFile($tmpfname, $attachment->ext);
                unset($image);

                $croppedAttachment = $this->newEntity([
                    'filename' => $tmpfname,
                    'original' => Text::slug($imgnote->note) . '.' . $attachment->ext,
                    'ext' => $attachment->ext,
                    'mimetype' => $attachment->mimetype,
                    'filesize' => filesize($tmpfname),
                    'height' => $height,
                    'width' => $width,
                    'title' => $imgnote->note,
                    'description' => '',
                    'checksum' => md5_file($tmpfname),
                    'attachments_links' => [0 => [
                        'attachment_id' => null,
                        'class' => 'Profile',
                        'foreign_id' => $imgnote->profile_id
                    ]]
                ], ['associated' => ['AttachmentsLinks']]);

                if ($this->save($croppedAttachment, ['associated' => ['AttachmentsLinks']])) {
                    $this->processUpload($croppedAttachment, $tmpfname, 'existing');

                    return $croppedAttachment;
                }
                unlink($tmpfname);
            }
        }

        return false;
    }

    /**
     * Returns physical dimension of image file
     *
     * @param string $attachmentId Attachment id
     * @param mixed $size Image size
     * @access public
     * @return bool|array
     */
    public function getImageSize($attachmentId, $size = 'original')
    {
        $ret = false;
        $folder = Configure::read('sourceFolders.attachments');
        if ($size == 'thumbs') {
            $folder = Configure::read('sourceFolders.thumbs');
        }

        $filename = $folder . DS . $attachmentId . DS . $size;
        if (file_exists($filename)) {
            if ($sizes = getimagesize($filename)) {
                $ret = [];
                $ret['width'] = $sizes[0];
                $ret['height'] = $sizes[1];
            }
        }

        return $ret;
    }
}
