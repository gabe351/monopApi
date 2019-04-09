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

    private function cleanStr($value){
        $value = preg_replace('/[^(\x20-\x7F)]*/','', $value);
        return $value;
    }

    private function saveDataBase($data){
        $data = json_decode($data,true);
        ini_set('memory_limit', '2020M');
        foreach ($data as $dado){
            $dado['dat_ciclo'] = date('Y-m-d', strtotime($dado['dat_ciclo']));
            $dado['dat_selecao'] = date('Y-m-d', strtotime($dado['dat_selecao']));
            $dado['dat_conclusao_revisada'] = date('Y-m-d', strtotime($dado['dat_conclusao_revisada']));

            $dado['titulo'] = $this->cleanStr($dado['titulo']);
            $dado['txt_executores'] = $this->cleanStr($dado['txt_executores']);

            $obras = $this->Obras->newEntity();
            $obra = $this->Obras->patchEntity($obras, $dado);
            $this->Obras->save($obra);
        }
    }

    private function getDados($dado){
        $url = ROOT.DS.'webroot'.DS.'bases'.DS.$dado.'.json';
        if(!file_exists($url)){
            $this->downloadBase();
            $data = file_get_contents($url);
            $this->saveDataBase($data);
        }
        elseif(in_array($dado, ['status', 'subeixo'])){
            $data = file_get_contents($url);
        }
        else{
            $data = $this->paginate($this->Obras);
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
        $obras = json_encode($data,true);
        $obras = json_decode($obras,true);

        $newObras = [];
        foreach($obras as $obra){ 
            foreach($filtros as $filtro){
                $filtro = explode(':', $filtro);
                if($obra[$filtro[0]] == $filtro[1]){
                    $newObras[] = $obra;
                }

            }
        }
        return $newObras;
    }

    private function getDadosExtras($obras){
        $obras = json_encode($obras, true);
        $obras = json_decode($obras, true);
        
        $status = $this->getDados('status');
        $status = json_decode($status,true);
        
        $subeixos = $this->getDados('subeixo');
        $subeixos = json_decode($subeixos,true);
        
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
            $menorValor = $key == 0 ? $dado['investimento_total'] : $menorValor ;
            if(!empty($dado['investimento_total'])){

                //$menorValor = $dado['investimento_total'] < $menorValor ? $dado['investimento_total'] : $menorValor ;
                //$maiorValor = $dado['investimento_total'] > $maiorValor ? $dado['investimento_total'] : $maiorValor ;            
                if($dado['investimento_total'] < $menorValor){
                    $menorValor = $dado['investimento_total'];
                    $body['min'] = $dado;
                }
                else{
                    $menorValor = $menorValor;
                }

                if($dado['investimento_total'] > $maiorValor){
                    $maiorValor = $dado['investimento_total'];
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