<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Obras Model
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
            ->add('id', 'unique', ['rule' => 'validateUnique', 'provider' => 'table']);

        $validator
            ->integer('idn_empreendimento')
            ->requirePresence('idn_empreendimento', 'create')
            ->notEmpty('idn_empreendimento');

        $validator
            ->integer('id_digs')
            ->requirePresence('id_digs', 'create')
            ->notEmpty('id_digs');

        $validator
            ->scalar('titulo')
            ->maxLength('titulo', 200)
            ->requirePresence('titulo', 'create')
            ->notEmpty('titulo');

        $validator
            ->decimal('investimento_total')
            ->allowEmpty('investimento_total');

        $validator
            ->scalar('sig_uf')
            ->maxLength('sig_uf', 10)
            ->requirePresence('sig_uf', 'create')
            ->notEmpty('sig_uf');

        $validator
            ->scalar('txt_municipios')
            ->maxLength('txt_municipios', 100)
            ->requirePresence('txt_municipios', 'create')
            ->notEmpty('txt_municipios');

        $validator
            ->scalar('txt_executores')
            ->maxLength('txt_executores', 100)
            ->requirePresence('txt_executores', 'create')
            ->notEmpty('txt_executores');

        $validator
            ->scalar('dsc_orgao')
            ->maxLength('dsc_orgao', 100)
            ->requirePresence('dsc_orgao', 'create')
            ->notEmpty('dsc_orgao');

        $validator
            ->integer('idn_estagio')
            ->requirePresence('idn_estagio', 'create')
            ->notEmpty('idn_estagio');

        $validator
            ->date('dat_ciclo')
            ->requirePresence('dat_ciclo', 'create')
            ->notEmpty('dat_ciclo');

        $validator
            ->date('dat_selecao')
            ->allowEmpty('dat_selecao');

        $validator
            ->date('dat_conclusao_revisada')
            ->allowEmpty('dat_conclusao_revisada');

        $validator
            ->scalar('obra_latitude')
            ->maxLength('obra_latitude', 20)
            ->allowEmpty('obra_latitude');

        $validator
            ->scalar('obra_longitude')
            ->maxLength('obra_longitude', 20)
            ->allowEmpty('obra_longitude');

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
        $rules->add($rules->isUnique(['id']));

        return $rules;
    }
}
