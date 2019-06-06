<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Obra Entity
 *
 * @property int $id
 * @property int $tipo_id
 * @property string $nome
 * @property float|null $total_investido
 * @property string $uf
 * @property string $municipios
 * @property string $executor
 * @property int $estagio_id
 * @property string $monitorador
 * @property \Cake\I18n\FrozenDate $data_ciclo
 * @property \Cake\I18n\FrozenDate|null $data_selecao
 * @property \Cake\I18n\FrozenDate|null $data_conclusao_revisada
 * @property string|null $longitude
 * @property string|null $latitude
 * @property string|null $emblematica
 * @property string|null $observacao
 */
class Obra extends Entity
{

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        '*' => true
    ];
}
