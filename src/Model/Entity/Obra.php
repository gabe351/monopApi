<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Obra Entity
 *
 * @property int $id
 * @property int $idn_empreendimento
 * @property int $id_digs
 * @property string $titulo
 * @property float|null $investimento_total
 * @property string $sig_uf
 * @property string $txt_municipios
 * @property string $txt_executores
 * @property string $dsc_orgao
 * @property int $idn_estagio
 * @property \Cake\I18n\FrozenDate $dat_ciclo
 * @property \Cake\I18n\FrozenDate|null $dat_selecao
 * @property \Cake\I18n\FrozenDate|null $dat_conclusao_revisada
 * @property string|null $obra_latitude
 * @property string|null $obra_longitude
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
