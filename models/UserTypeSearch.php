<?php
/*
 * This file is part of the Dektrium project.
 *
 * (c) Dektrium project <http://github.com/dektrium/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace lnch\users\models;

use Yii;

use yii\base\Model;
use yii\data\ActiveDataProvider;

use lnch\users\Finder;

/**
 * UserTypeSearch represents the model behind the search form about UserTypes.
 */
class UserTypeSearch extends Model
{
    /** @var integer */
    public $type_id;

    /** @var string */
    public $name;

    /** @var string */
    public $alias;

    // /** @var string */
    // public $signup_ip;

    // /** @var string */
    // public $user_type;

    /** @var Finder */
    protected $finder;

    /**
     * @param Finder $finder
     * @param array  $config
     */
    public function __construct(Finder $finder, $config = [])
    {
        $this->finder = $finder;
        parent::__construct($config);
    }

    /** @inheritdoc */
    public function rules()
    {
        return [
            'fieldsSafe' => [['name', 'alias', 'type_id'], 'safe'],
        ];
    }

    /** @inheritdoc */
    public function attributeLabels()
    {
        return [
            'name'      => Yii::t('user', 'Type'),
            'alias'		=> Yii::t('user', 'Alias'),
            'type_id' 	=> Yii::t('user', 'User Level'),
        ];
    }

    /**
     * @param $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = $this->finder->getUserTypeQuery();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        
        if(!($this->load($params) && $this->validate())) 
        {
            return $dataProvider;
        }
        
        // $query->andFilterWhere(['like', 'username', $this->username])
        //     ->andFilterWhere(['like', 'email', $this->email])
        //     ->andFilterWhere(['signup_ip' => $this->signup_ip])
        //     ->andFilterWhere(['user_type' => $this->user_type]);
        
        return $dataProvider;
    }
}