<?php
declare(strict_types=1);

namespace App\Model\Table;

use App\Model\Entity\Attachment;
use App\Model\Entity\ImgNote;
use ArrayObject;
use Cake\Core\Configure;
use Cake\Datasource\EntityInterface;
use Cake\Datasource\ResultSetInterface;
use Cake\Event\Event;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Utility\Filesystem;
use Cake\Utility\Text;
use Cake\Validation\Validator;
use Laminas\Diactoros\UploadedFile;
use WideImage\WideImage;

/**
 * Attachments Model
 *
 * @property \App\Model\Table\UsersTable|\Cake\ORM\Association\BelongsTo $Users
 * @property \App\Model\Table\AttachmentsLinksTable|\Cake\ORM\Association\HasMany $AttachmentsLinks
 * @property \App\Model\Table\ImgnotesTable|\Cake\ORM\Association\HasMany $Imgnotes
 * @method \App\Model\Entity\Attachment get($primaryKey, $options = [])
 * @method \App\Model\Entity\Attachment newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Attachment[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Attachment|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Attachment|bool saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Attachment patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Attachment[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Attachment findOrCreate($search, callable $callback = null, $options = [])
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
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('attachments');
        $this->setDisplayField('title');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        //$this->addBehavior('Loggable', ['excludedProperties' => ['user', 'attachments_links', 'imgnotes']]);

        $this->belongsTo('Creators', [
            'className' => 'Profiles',
            'foreignKey' => 'user_id',
        ]);
        $this->hasMany('AttachmentsLinks', [
            'foreignKey' => 'attachment_id',
            'dependent' => true,
            'cascadeCallbacks' => true,
        ]);
        $this->hasMany('Imgnotes', [
            'foreignKey' => 'attachment_id',
            'dependent' => true,
        ]);
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
            ->uuid('id')
            ->allowEmptyString('id', 'create');

        $validator
            ->allowEmptyFile('attachment');
        $validator->add('attachment', [
            'mime' => [
                'rule' => ['mimeType', ['image/jpeg', 'image/png', 'image/gif']],
            ],
            'size' => [
                'rule' => ['fileSize', '<', '5MB'],
            ],
        ]);

        $validator
            ->scalar('original')
            ->maxLength('original', 255)
            ->allowEmptyString('original');

        $validator
            ->scalar('ext')
            ->maxLength('ext', 6)
            ->requirePresence('ext', 'create')
            ->notEmptyString('ext');

        //$validator
        //    ->scalar('mimetype')
        //    ->maxLength('mimetype', 30)
        //    ->allowEmptyString('mimetype');

        $validator
            ->integer('filesize')
            ->allowEmptyString('filesize');

        $validator
            ->integer('height')
            ->allowEmptyString('height');

        $validator
            ->integer('width')
            ->allowEmptyString('width');

        $validator
            ->scalar('title')
            ->maxLength('title', 100)
            ->allowEmptyString('title');

        $validator
            ->scalar('description')
            ->allowEmptyString('description');

        $validator
            ->scalar('checksum')
            ->maxLength('checksum', 32)
            ->allowEmptyString('checksum');

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
        //$rules->add($rules->existsIn(['user_id'], 'Users'));

        return $rules;
    }

    /**
     * beforeMarshal event handler
     *
     * @param \Cake\Event\Event $event Event object
     * @param \ArrayObject $data Data object
     * @param \ArrayObject $options Options
     * @return void
     */
    public function beforeMarshal(Event $event, ArrayObject $data, ArrayObject $options): void
    {
        if (!empty($data['attachment']) && $data['attachment'] instanceof UploadedFile) {
            $attachment = $data['attachment'];
            $data['filename'] = $attachment->getClientFilename();
            $data['original'] = $attachment->getClientFilename();
            $data['mimetype'] = $attachment->getClientMediaType();
            $data['filesize'] = $attachment->getSize();
            $data['checksum'] = md5_file($attachment->getStream()->getMetadata('uri'));
            $data['ext'] = strtolower(pathinfo($attachment->getClientFilename(), PATHINFO_EXTENSION));

            if (substr($attachment->getClientMediaType(), 0, 5) == 'image') {
                $imageInfo = @getimagesize($attachment->getStream()->getMetadata('uri'));
                if ($imageInfo !== false) {
                    $data['width'] = $imageInfo[0];
                    $data['height'] = $imageInfo[1];
                }
            }
        }
    }

    /**
     * afterDelete event handler
     *
     * @param \Cake\Event\Event $event Event object
     * @param \Cake\Datasource\EntityInterface $entity Entity object
     * @param \ArrayObject $options Options
     * @return void
     */
    public function afterDelete(Event $event, EntityInterface $entity, ArrayObject $options): void
    {
        (new Filesystem())->deleteDir(Configure::read('sourceFolders.attachments') . $entity->get('id'));
        $thumb = Configure::read('sourceFolders.thumbs') . $entity->get('id') . '.png';
        if (file_exists($thumb)) {
            unlink($thumb);
        }
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
     * @param \Laminas\Diactoros\UploadedFile $uploadedFile Temporary file name
     * @return void
     */
    public function processUpload(Attachment $entity, UploadedFile $uploadedFile): void
    {
        $stream = $uploadedFile->getStream();
        $uploadedPath = $stream->getMetadata('uri');
        $image = WideImage::loadFromFile($uploadedPath, $entity->ext);
        if (!empty($image)) {
            $dir = Configure::read('sourceFolders.attachments') . $entity->id;
            (new Filesystem())->mkdir($dir);

            if (file_exists($dir . DS . 'original')) {
                unlink($dir . DS . 'original');
            }

            //$uploadedFile->moveTo($dir . DS . 'original');
            // release the file handle first, otherwise the file stays locked on Windows
            $stream->close();
            rename($uploadedPath, $dir . DS . 'original');
            if (is_file($dir . DS . 'original')) {
                $image->resize(640, 480, 'inside')->saveToFile($dir . DS . 'large', $entity->ext);
                $image->resize(200, 200, 'inside')->saveToFile($dir . DS . 'medium', $entity->ext);

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

    /**
     * Extract image area from specified attachment and create new image
     *
     * @param \App\Model\Entity\ImgNote $imgnote Imgnote entity
     * @return \App\Model\Entity\Attachment|bool
     */
    public function createFromImgnote(ImgNote $imgnote): Attachment|bool
    {
        $fileOriginal = Configure::read('sourceFolders.attachments') . DS . $imgnote->attachment_id . DS . 'original';
        if (file_exists($fileOriginal)) {
            $attachment = $this->get($imgnote->attachment_id);

            $largeSize = $this->getImageSize($imgnote->attachment_id, 'large');
            $originalSize = $this->getImageSize($imgnote->attachment_id, 'original');

            $scaleFactor = $originalSize['width'] / $largeSize['width'];
            $x = (int)round($imgnote->x1 * $scaleFactor);
            $y = (int)round($imgnote->y1 * $scaleFactor);
            $width = (int)round($imgnote->width * $scaleFactor);
            $height = (int)round($imgnote->height * $scaleFactor);

            $tmpfname = tempnam(defined('TMP') ? constant('TMP') : null, 'fam');
            $image = WideImage::loadFromFile($fileOriginal, $attachment->ext);
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
                        'foreign_id' => $imgnote->profile_id,
                    ]],
                ], ['associated' => ['AttachmentsLinks']]);

                $jpgAttachment = new UploadedFile(
                    $croppedAttachment->filename,
                    $croppedAttachment->filesize,
                    UPLOAD_ERR_OK,
                    $croppedAttachment->original,
                    $croppedAttachment->mimetype,
                );

                if ($this->save($croppedAttachment, ['associated' => ['AttachmentsLinks']])) {
                    $this->processUpload($croppedAttachment, $jpgAttachment);

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
     * @return array|bool
     */
    public function getImageSize(string $attachmentId, mixed $size = 'original'): bool|array
    {
        $ret = false;
        $folder = Configure::read('sourceFolders.attachments');
        if ($size == 'thumbs') {
            $folder = Configure::read('sourceFolders.thumbs');
        }

        $filename = $folder . DS . $attachmentId . DS . $size;
        if (file_exists($filename)) {
            $sizes = @getimagesize($filename);
            if ($sizes) {
                $ret = [];
                $ret['width'] = $sizes[0];
                $ret['height'] = $sizes[1];
            }
        }

        return $ret;
    }
}
