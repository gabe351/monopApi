<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Estagios Model
 *
 * @property \App\Model\Table\ObrasTable|\Cake\ORM\Association\HasMany $Obras
 *
 * @method \App\Model\Entity\Estagios get($primaryKey, $options = [])
 * @method \App\Model\Entity\Estagios newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Estagios[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Estagios|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Estagios|bool saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Estagios patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Estagios[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Estagios findOrCreate($search, callable $callback = null, $options = [])
 */
class EstagiosTable extends Table
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

        $this->setTable('estagios');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->hasMany('Obras', [
            'foreignKey' => 'estagio_id'
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
            ->scalar('nome')
            ->maxLength('nome', 90)
            ->requirePresence('nome', 'create')
            ->notEmpty('nome');

        $validator
            ->scalar('descricao')
            ->allowEmpty('descricao');

        return $validator;
    }
}
