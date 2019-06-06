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
        $resultado = utf8_encode($resultado);
        $this->saveDataBase($resultado);

        $csvToJson = new CsvToJson($resultado, ['bitmask' => 64|128|256]);
        $csvToJson->convertAndSave(ROOT . '/webroot/bases/base');
    }

    private function cleanStr($value){
        $value = preg_replace('/[^(\x20-\x7F)]*/','', $value);
        return $value;
    }

    private function saveDataBase($csv){
        set_time_limit(0);
        ini_set('memory_limit', '2020M');
        $csv = array_filter(explode(PHP_EOL, $csv));
        unset($csv[0]);
        
        foreach ($csv as $key => $values) {
            $result[] = str_getcsv($values, ',', '"', '\\');
        }

        foreach ($result as  $dado){            
            $obra = [];
            $obra['id'] = $dado[0];
            $obra['tipo_id'] = $dado[1];
            $obra['nome'] = $this->cleanStr($dado[2]);
            $obra['total_investido'] = $dado[3];
            $obra['uf'] = $dado[4];
            $obra['municipios'] = $dado[5];
            $obra['executor'] = $this->cleanStr($dado[6]);
            $obra['monitorador'] = $dado[7];
            $obra['estagio_id'] = $dado[8];
            $obra['data_ciclo'] = date('Y-m-d', strtotime($dado[9]));
            $obra['data_selecao'] = date('Y-m-d', strtotime($dado[10]));
            $obra['data_conclusao_revisada'] = date('Y-m-d', strtotime($dado[11]));
            $obra['latitude'] = $dado[12];
            $obra['longitude'] = $dado[13];
            $obra['emblematica'] = $dado[14];
            $obra['observacao'] = $dado[15];

            $obras = $this->Obras->newEntity(); 
            $obraSave = $this->Obras->patchEntity($obras, $obra);
            $this->Obras->save($obraSave);
        }
    }

    private function getDados($dado){
        $url = ROOT.DS.'webroot'.DS.'bases'.DS.$dado.'.json';
        if(!file_exists($url)){
            $this->downloadBase();
            $this->paginate = [
                'contain' => ['Tipos', 'Estagios']
            ];            
            $data = $this->paginate($this->Obras);
        }
        else{
            $this->paginate = [
                'contain' => ['Tipos', 'Estagios']
            ];            
            $data = $this->paginate($this->Obras);
        }
        return $data->toArray();
    }

    public function index($filter = null)
    {
        $this->response->type('application/json');
        $this->autoRender = false;
        $data = $this->getDados('base');

        if(!empty($filter)){
            $data = $this->filters($data, $filter);
        }
        $obras = json_encode($data,true);
        $this->response->body($obras);
    }

    public function getContructionMap($obra){
        $this->response->type('application/json');
        $this->autoRender = false;

        $obra = json_decode($obra, true);
        if(!empty($obra['latitude']) && !empty($obra['longitude'])) {
            $mapa = ['latitude' => $obra['latitude'], 'longitude' => $obra['longitude']];
            $this->response->body(json_encode($mapa));
        }
        else{
            $this->response->statusCode(204);
            $this->response->body(json_encode('Não existe mapa para esta obra'));
        }
    }

    public function getEstagios(){
        $this->response->type('application/json');
        $this->autoRender = false;

        $this->loadModel('Estagios');
        $data = $this->Estagios->find('all');

        $this->response->body(json_encode($data));
    }

    public function getTipos(){
        $this->response->type('application/json');
        $this->autoRender = false;

        $this->loadModel('Tipos');
        $data = $this->Tipos->find('all');

        $this->response->body(json_encode($data));
    }

    private function filters($data, $filter){
        $filtros = explode(';', $filter); 
        $newObras = [];
        $conditions= [];
        foreach($filtros as $filtro){
            $filtro = explode(':', $filtro);
            if(!empty($filtro[1])){
                if($filtro[0] == 'nome' || $filtro[0] == 'uf'){
                    $conditions = ["Obras." . $filtro[0] . " LIKE '%$filtro[1]%'"];
                }
                elseif($filtro[0] == 'estagio_id' || $filtro[0] == 'tipo_id'){
                    $objeto = $filtro[0] == 'estagio_id' ? 'Estagios' : 'Tipos'; 
                    $this->loadModel($objeto);
                    $objetoFiltrado = $this->$objeto->get($filtro[1]);
                }
                else{
                    $conditions = ['Obras.' .$filtro[0] => $filtro[1]];
                }
            }
        }
        $newObra = $this->Obras->find('all', [
            'conditions' => $conditions,
            'contain' => ['Tipos', 'Estagios']
        ]);
        $newObras[] = $newObra;
        return $newObras;
    }

    public function getInvestment(){
        $this->response->type('application/json');
        $this->autoRender = false;
        $dados = $this->Obras->find('all', [
            'conditions' => [''],
        ])->toArray();

        $menorValor = 0;
        $maiorValor = 0;
        $body = [];
        foreach($dados as $key => $dado){
            $menorValor = $key == 0 ? $dado['total_investido'] : $menorValor ;
            if(!empty($dado['total_investido'])){
                if($dado['total_investido'] < $menorValor){
                    $menorValor = $dado['total_investido'];
                    $body['min'] = $dado;
                }
                else{
                    $menorValor = $menorValor;
                }
                
                if($dado['total_investido'] > $maiorValor){
                    $maiorValor = $dado['total_investido'];
                    $body['max'] = $dado;
                }
                else{
                    $maiorValor = $maiorValor;
                }
            }
        }
        
        $this->response->body(json_encode($body));
    }
}