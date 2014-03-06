<?php
class App_controller extends Controller{

  public function __construct(){
    parent::__construct();
    $this->tpl=array('sync'=>'main.html');
  }
  
  public function home($f3){
    $f3->set('events', $this->model->getEvents(array('promo'=>$f3->get('SESSION.promo'))));
    $f3->set('promos', $this->model->getPromos());
    $program = $this->model->getJson(array('promo'=>$f3->get('SESSION.promo')));
    $f3->set('program', $program['H3']);
  }
  
  public function signin($f3){
    switch($f3->get('VERB')){
      case 'GET': 
        $this->tpl['sync']='signin.html';
      break;
      case 'POST': 
        $auth = $this->model->signin(array('login'=>$f3->get('POST.login'), 'password'=>$f3->get('POST.password')));
        if(!$auth){
          $f3->set('error', 'Oups, vous avez du vous fourvoyer dans vos logins. Ré-essayez, peut être que ça marchera cette fois.');
          $this->tpl['sync']='signin.html';
        }
        else {
          $user = array(
            'id'=>$auth->id,
            'firstname'=>$auth->firstname,
            'lastname'=>$auth->lastname,
            'promo'=>$auth->promo,
            'group'=>$auth->group,
            'subgroup'=>$auth->subgroup
          );
          $f3->set('SESSION', $user);
          $f3->reroute('/');
        }
      break;
    }
  }
  
  public function signout($f3){
    session_destroy();
    $f3->reroute('/signin');
  }  
  
  public function addEvent($f3){
    switch($f3->get('VERB')){
      case 'GET': 
        $this->tpl['sync']='addEvent.html';
      break;
      case 'POST': 
        $this->model->addEvent();
        $f3->reroute('/');
      break;
    }
  }
  
  public function editEvent($f3){
    switch($f3->get('VERB')){
      case 'GET': 
        $f3->set('event', $this->model->getEvent(array('id'=>$f3->get('PARAMS.id'))));
        $f3->set('promos', $this->model->getPromos());
        $this->tpl['async']='partials/editEvent.html';
      break;
      case 'POST': 
        if($f3->get('POST.edit')){
          $this->model->editEvent(array('id'=>$f3->get('PARAMS.id')));
        }
        else if($f3->get('POST.delete')){
          $this->model->deleteEvent(array('id'=>$f3->get('PARAMS.id')));
        }
        $f3->reroute('/');
      break;
    }
  }
}
?>