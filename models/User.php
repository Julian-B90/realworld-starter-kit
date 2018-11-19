<?php

namespace app\models;

use app\models\query\UserQuery;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Yii;
use yii\base\InvalidArgumentException;
use yii\base\InvalidConfigException;
use yii\db\Expression;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "user".
 *
 * @property integer $id
 * @property string $username
 * @property string $email
 * @property string $password_hash
 * @property string $bio
 * @property string $image
 * @property string $token
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Follow[] $follows
 */
class User extends \yii\db\ActiveRecord implements \yii\web\IdentityInterface
{
    const TOKEN_EXPIRATION_TIMEOUT = 60 * 60 * 24 * 14;

    const SCENARIO_REGISTER = 'register';
    const SCENARIO_UPDATE = 'update';

    public $password;

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_REGISTER] = ['username', 'email', 'password'];
        $scenarios[self::SCENARIO_UPDATE] = ['username', 'email', 'password', 'image', 'bio'];
        return $scenarios;
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username', 'email', 'password'], 'required', 'on' => self::SCENARIO_REGISTER],
            ['email', 'email'],
            ['email', 'unique'],
            [['username', 'email', 'password', 'bio', 'image'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Username',
            'email' => 'Email',
            'password' => 'Password',
            'bio' => 'Bio',
            'image' => 'Image',
            'token' => 'Token',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['token' => $token]);
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username]);
    }
    /**
     * Finds user by email
     *
     * @param string $email
     * @return static|null
     */
    public static function findByEmail($email)
    {
        return static::findOne(['email' => $email]);
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return null;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return false;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    public function login() {
        $this->token = $this->generateToken();
        return $this->save();
    }

    /**
     * @return bool
     * @throws \yii\base\Exception
     */
    public function register() {
        $this->password_hash = Yii::$app->security->generatePasswordHash($this->password);
        $this->token = $this->generateToken();
        return $this->save();
    }

    /**
     * @return string
     * @throws InvalidConfigException
     */
    protected function generateToken() {
        $signer = new Sha256();

        if (!isset(\Yii::$app->jwt)) {
            throw new InvalidConfigException('You must setup JWT component');
        }

        $token = \Yii::$app->jwt->getBuilder()
            ->setExpiration(time() + self::TOKEN_EXPIRATION_TIMEOUT)
            ->set('user_id', $this->id)
            ->sign($signer, \Yii::$app->jwt->key)
            ->getToken();
        return strval($token);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFollows()
    {
        return $this->hasMany(Follow::class, ['follow_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany(Follow::class, ['user_id' => 'id']);
    }

    /**
     * @param User $user
     */
    public function follow($user) {
        $isAlreadyFollowing = Follow::find()
            ->where(['follower_id' => $this->id, 'followed_id' => $user->id])
            ->exists();
        if ($isAlreadyFollowing) {
            return true;
        }

        $follow = new Follow([
            'follower_id' => $this->id,
            'followed_id' => $user->id,
        ]);

        return $follow->save();
    }

    /**
     * @param $user
     *
     * @return false|int
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function unfollow($user) {
        $follow = Follow::findOne(['follower_id' => $this->id, 'followed_id' => $user->id]);
        if (is_null($follow)) {
            return true;
        }
        return $follow->delete();
    }

    public static function find()
    {
        return new UserQuery(get_called_class());
    }
}
