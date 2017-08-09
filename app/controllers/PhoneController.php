<?php
 
use Phalcon\Mvc\Model\Criteria;
use Phalcon\Paginator\Adapter\Model as Paginator;


class PhoneController extends ControllerBase
{
    /**
     * Index action
     */
    public function indexAction()
    {
        $this->persistent->parameters = null;
    }

    /**
     * Searches for phone
     */
    public function searchAction()
    {
        $numberPage = 1;
        if ($this->request->isPost()) {
            $query = Criteria::fromInput($this->di, 'Phone', $_POST);
            $this->persistent->parameters = $query->getParams();
        } else {
            $numberPage = $this->request->getQuery("page", "int");
        }

        $parameters = $this->persistent->parameters;
        if (!is_array($parameters)) {
            $parameters = [];
        }
        $parameters["order"] = "idphone";

        $Phone = Phone::find($parameters);
        if (count($Phone) == 0) {
            $this->flash->notice("The search did not find any phone");

            $this->dispatcher->forward([
                "controller" => "phone",
                "action" => "index"
            ]);

            return;
        }

        $paginator = new Paginator([
            'data' => $Phone,
            'limit'=> 10,
            'page' => $numberPage
        ]);

        $this->view->page = $paginator->getPaginate();
    }

    /**
     * Displays the creation form
     */
    public function newAction()
    {

    }

    /**
     * Edits a phone
     *
     * @param string $idphone
     */
    public function editAction($idphone)
    {
        if (!$this->request->isPost()) {

            $Phone = Phone::findFirstByidphone($idphone);
            if (!$Phone) {
                $this->flash->error("phone was not found");

                $this->dispatcher->forward([
                    'controller' => "phone",
                    'action' => 'index'
                ]);

                return;
            }

            $this->view->idphone = $Phone->idphone;

            $this->tag->setDefault("idphone", $Phone->idphone);
            $this->tag->setDefault("number", $Phone->number);
            $this->tag->setDefault("name", $Phone->name);
            $this->tag->setDefault("nueva", $Phone->nueva);
            
        }
    }

    /**
     * Creates a new phone
     */
    public function createAction()
    {   
        if (!$this->request->isPost()) {
            $this->dispatcher->forward([
                'controller' => "phone",
                'action' => 'index'
            ]);

            return;
        }

        $Phone = new Phone();
        $Phone->Number = $this->request->getPost("number");
        $Phone->Name = $this->request->getPost("name");
        $Phone->Nueva = $this->request->getPost("nueva");
        

        if (!$Phone->save()) {
            foreach ($Phone->getMessages() as $message) {
                $this->flash->error($message);
            }

            $this->dispatcher->forward([
                'controller' => "phone",
                'action' => 'new'
            ]);

            return;
        }

        $this->flash->success("phone was created successfully");

        $this->dispatcher->forward([
            'controller' => "phone",
            'action' => 'index'
        ]);
    }

    /**
     * Saves a phone edited
     *
     */
    public function saveAction()
    {

        if (!$this->request->isPost()) {
            $this->dispatcher->forward([
                'controller' => "phone",
                'action' => 'index'
            ]);

            return;
        }

        $idphone = $this->request->getPost("idphone");
        $Phone = Phone::findFirstByidphone($idphone);

        if (!$Phone) {
            $this->flash->error("phone does not exist " . $idphone);

            $this->dispatcher->forward([
                'controller' => "phone",
                'action' => 'index'
            ]);

            return;
        }

        $Phone->Number = $this->request->getPost("number");
        $Phone->Name = $this->request->getPost("name");
        $Phone->Nueva = $this->request->getPost("nueva");
        

        if (!$Phone->save()) {

            foreach ($Phone->getMessages() as $message) {
                $this->flash->error($message);
            }

            $this->dispatcher->forward([
                'controller' => "phone",
                'action' => 'edit',
                'params' => [$Phone->idphone]
            ]);

            return;
        }

        $this->flash->success("phone was updated successfully");

        $this->dispatcher->forward([
            'controller' => "phone",
            'action' => 'index'
        ]);
    }

    /**
     * Deletes a phone
     *
     * @param string $idphone
     */
    public function deleteAction($idphone)
    {
        $Phone = Phone::findFirstByidphone($idphone);
        if (!$Phone) {
            $this->flash->error("phone was not found");

            $this->dispatcher->forward([
                'controller' => "phone",
                'action' => 'index'
            ]);

            return;
        }

        if (!$Phone->delete()) {

            foreach ($Phone->getMessages() as $message) {
                $this->flash->error($message);
            }

            $this->dispatcher->forward([
                'controller' => "phone",
                'action' => 'search'
            ]);

            return;
        }

        $this->flash->success("phone was deleted successfully");

        $this->dispatcher->forward([
            'controller' => "phone",
            'action' => "index"
        ]);
    }

}
