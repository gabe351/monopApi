<?php
namespace App\Controller;
use Pearl\CsvJsonConverter\Type\CsvToJson;

class ObrasController extends AppController
{

    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('RequestHandler');
    }

    public function downloadBase()
    {
        $url = "http://repositorio.dados.gov.br/governo-politica/administracao-publica/pac/PAC_2018_12.csv";
        $ch     = @curl_init();
        $timeout= 5;

        curl_setopt ($ch, CURLOPT_URL, $url);
        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt ($ch,  CURLOPT_USERAGENT , "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1)");
        curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);

        $resultado = curl_exec($ch);
        ini_set('memory_limit', '2020M');

        $csvToJson = new CsvToJson($resultado, ['bitmask' => 64|128|256]);
        //$data = $csvToJson->convert();
        $csvToJson->convertAndSave(ROOT . '/webroot/bases/base');
    }

    private function getDados($dado){
        $data = file_get_contents(ROOT.DS.'webroot'.DS.'bases'.DS.$dado.'.json');
        if(empty($data)){
            $this->downloadBase();
        }
        return $data;
    }

    public function index($filter = null)
    {
        $this->response->type('application/json');
        $this->autoRender = false;
        $data = $this->getDados('base');

        if(!empty($filter)){
            $data = $this->filters($data, $filter);
        }
        $data = $this->getDadosExtras($data);
        $this->response->body($data);
    }

    public function getContructionMap($obra){
        $this->response->type('application/json');
        $this->autoRender = false;

        $obra = json_decode($obra, true);
        if(!empty($obra['obra_latitude']) && !empty($obra['obra_longitude'])) {
            $mapa = ['latitude' => $obra['obra_latitude'], 'longitude' => $obra['obra_longitude']];
            $this->response->body(json_encode($mapa));
        }
        else{
            $this->response->statusCode(204);
            $this->response->body(json_encode('Não existe mapa para esta obra'));
        }
    }

    private function filters($data, $filter){
        $filtros = explode(';', $filter);
        $obras = json_decode($data,true);

        $newObras = [];
        foreach($obras as $obra){
            foreach($filtros as $filtro){
                $filtro = explode(':', $filtro);
                if($obra[$filtro[0]] == $filtro[1]){
                    $newObras[] = $obra;
                }

            }
        }
        return json_encode($newObras);
    }

    private function getDadosExtras($obras){
        $obras = json_decode($obras, true);
        $status = $this->getDados('status');
        $status = json_decode($status, true);

        $subeixos = $this->getDados('subeixo');
        $subeixos = json_decode($subeixos, true);

        foreach($obras as $key => $obra){
            foreach($status as $estagio){
                if($obra['idn_estagio'] == $estagio['idn_estagio']){
                    $obras[$key]['idn_estagio'] = $estagio;
                }
            }

            foreach($subeixos as $subeixo){
                if($obra['id_digs'] == $subeixo['id_digs']){
                    $obras[$key]['id_digs'] = $subeixo;
                }
            }
        }

        return json_encode($obras);
    }
}