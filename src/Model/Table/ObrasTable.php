<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Obras Model
 *
 * @property \App\Model\Table\TiposTable|\Cake\ORM\Association\BelongsTo $Tipos
 * @property \App\Model\Table\EstagiosTable|\Cake\ORM\Association\BelongsTo $Estagios
 *
 * @method \App\Model\Entity\Obra get($primaryKey, $options = [])
 * @method \App\Model\Entity\Obra newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Obra[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Obra|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Obra|bool saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Obra patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Obra[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Obra findOrCreate($search, callable $callback = null, $options = [])
 */
class ObrasTable extends Table
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

        $this->setTable('obras');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('Tipos', [
            'foreignKey' => 'tipo_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('Estagios', [
            'foreignKey' => 'estagio_id',
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
            ->scalar('nome')
            ->maxLength('nome', 255)
            ->requirePresence('nome', 'create')
            ->notEmpty('nome');

        $validator
            ->decimal('total_investido', 2)
            ->allowEmpty('total_investido');

        $validator
            ->scalar('uf')
            ->maxLength('uf', 10)
            ->requirePresence('uf', 'create')
            ->notEmpty('uf');

        $validator
            ->scalar('municipios')
            ->maxLength('municipios', 200)
            ->requirePresence('municipios', 'create')
            ->notEmpty('municipios');

        $validator
            ->scalar('executor')
            ->maxLength('executor', 100)
            ->requirePresence('executor', 'create')
            ->notEmpty('executor');

        $validator
            ->scalar('monitorador')
            ->maxLength('monitorador', 100)
            ->requirePresence('monitorador', 'create')
            ->notEmpty('monitorador');

        $validator
            ->date('data_ciclo')
            ->requirePresence('data_ciclo', 'create')
            ->notEmpty('data_ciclo');

        $validator
            ->date('data_selecao')
            ->allowEmpty('data_selecao');

        $validator
            ->date('data_conclusao_revisada')
            ->allowEmpty('data_conclusao_revisada');

        $validator
            ->scalar('longitude')
            ->maxLength('longitude', 20)
            ->allowEmpty('longitude');

        $validator
            ->scalar('latitude')
            ->maxLength('latitude', 20)
            ->allowEmpty('latitude');

        $validator
            ->scalar('emblematica')
            ->maxLength('emblematica', 100)
            ->allowEmpty('emblematica');

        $validator
            ->scalar('observacao')
            ->maxLength('observacao', 100)
            ->allowEmpty('observacao');

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
        $rules->add($rules->existsIn(['tipo_id'], 'Tipos'));
        $rules->add($rules->existsIn(['estagio_id'], 'Estagios'));

        return $rules;
    }
}
