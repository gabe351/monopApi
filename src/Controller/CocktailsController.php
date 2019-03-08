<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Cocktails Controller
 *
 * @property \App\Model\Table\CocktailsTable $Cocktails
 *
 * @method \App\Model\Entity\Cocktail[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class CocktailsController extends AppController
{

    /**
     * Index method
     *
     * @return \Cake\Http\Response|void
     */
    public function index()
    {
        $cocktails = $this->paginate($this->Cocktails);

        $this->set(compact('cocktails'));
    }

    /**
     * View method
     *
     * @param string|null $id Cocktail id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $cocktail = $this->Cocktails->get($id, [
            'contain' => []
        ]);

        $this->set('cocktail', $cocktail);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $cocktail = $this->Cocktails->newEntity();
        if ($this->request->is('post')) {
            $cocktail = $this->Cocktails->patchEntity($cocktail, $this->request->getData());
            if ($this->Cocktails->save($cocktail)) {
                $this->Flash->success(__('The cocktail has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The cocktail could not be saved. Please, try again.'));
        }
        $this->set(compact('cocktail'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Cocktail id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $cocktail = $this->Cocktails->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $cocktail = $this->Cocktails->patchEntity($cocktail, $this->request->getData());
            if ($this->Cocktails->save($cocktail)) {
                $this->Flash->success(__('The cocktail has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The cocktail could not be saved. Please, try again.'));
        }
        $this->set(compact('cocktail'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Cocktail id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $cocktail = $this->Cocktails->get($id);
        if ($this->Cocktails->delete($cocktail)) {
            $this->Flash->success(__('The cocktail has been deleted.'));
        } else {
            $this->Flash->error(__('The cocktail could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    public $paginate = [
        'page' => 1,
        'limit' => 10,
        'maxLimit' => 100,
        'fields' => [
            'id', 'name', 'description'
        ],
        'sortWhitelist' => [
            'id', 'name', 'description'
        ]
    ];
}
