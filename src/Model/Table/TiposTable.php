<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Tipos Model
 *
 * @property \App\Model\Table\ObrasTable|\Cake\ORM\Association\HasMany $Obras
 *
 * @method \App\Model\Entity\Tipos get($primaryKey, $options = [])
 * @method \App\Model\Entity\Tipos newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Tipos[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Tipos|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Tipos|bool saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Tipos patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Tipos[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Tipos findOrCreate($search, callable $callback = null, $options = [])
 */
class TiposTable extends Table
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

        $this->setTable('tipos');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->hasMany('Obras', [
            'foreignKey' => 'tipo_id'
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
            ->maxLength('nome', 255)
            ->requirePresence('nome', 'create')
            ->notEmpty('nome');

        $validator
            ->scalar('subeixo')
            ->maxLength('subeixo', 90)
            ->allowEmpty('subeixo');

        return $validator;
    }
}
