<?php
class App_controller extends Controller{

  public function __construct(){
    parent::__construct();
    $this->tpl=array('sync'=>'main.html');
  }
  
  public function home($f3){
    //Get & Set the events array to be desplayed
    $f3->set('events', $this->model->getEvents(array('promo'=>$f3->get('SESSION.promo'))));
    //Get & Set all promos to create events
    $f3->set('promos', $this->model->getPromos());
    //Get & Set all users to be desplayed in events feed & events cards
    $f3->set('users', $this->model->getPeople());
    //Get the program of the week, in JSON, for the current Promotion
    $program = $this->model->getJson(array('promo'=>$f3->get('SESSION.promo')));
    $f3->set('program', $program['H3']);
  }
  
  public function signin($f3){
    switch($f3->get('VERB')){
      case 'GET': 
        //Render signin template for get requests
        $this->tpl['sync']='signin.html';
      break;
      case 'POST': 
        //Check if the user is found & correct when form's submitted
        $auth = $this->model->signin(array('login'=>$f3->get('POST.login'), 'password'=>$f3->get('POST.password')));
        if(!$auth){
          //If the user isn't found, or isn't correct, render signin template & display error message
          $f3->set('error', 'Oups, vous avez du vous fourvoyer dans vos logins. Ré-essayez, peut être que ça marchera cette fois.');
          $this->tpl['sync']='signin.html';
        }
        else {
          //If the user is found, setting session variables & rerouting to app root
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
    //Destroying session for signing out
    session_destroy();
    $f3->reroute('/signin');
  }  
  
  public function addEvent($f3){
    switch($f3->get('VERB')){
      case 'GET': 
        //Render addevent template fo get requests
        $this->tpl['sync']='addEvent.html';
      break;
      case 'POST': 
        //When form's submitted, add the event to the database & rerouting to app root
        $this->model->addEvent();
        $f3->reroute('/');
      break;
    }
  }
  
  public function editEvent($f3){
    switch($f3->get('VERB')){
      case 'GET': 
        //Getting event datas to be displayed in event edit form & render editEvent partial
        $f3->set('event', $this->model->getEvent(array('id'=>$f3->get('PARAMS.id'))));
        $f3->set('promos', $this->model->getPromos());
        $this->tpl['async']='partials/editEvent.html';
      break;
      case 'POST': 
        //If the event is to be updated, update it
        if($f3->get('POST.edit')){
          $this->model->editEvent(array('id'=>$f3->get('PARAMS.id')));
        }
        //If the event is to be deleted, delete it
        else if($f3->get('POST.delete')){
          $this->model->deleteEvent(array('id'=>$f3->get('PARAMS.id')));
        }
        $f3->reroute('/');
      break;
    }
  }
  
  public function getFullSchedule($f3) {
    //Getting the full promo program, setting it & rendering it in renderPromo template
    $program = $this->getSchedule($f3->get('PARAMS.promo'));
    $f3->set('program', $program['H3']);
    $this->tpl['sync']='renderPromo.html';
  }
  
  public function getGroupSchedule($f3) {
    //Getting the full promo program, setting it & rendering it to be parsed for just a group program in renderGroup template
    $program = $this->getSchedule($f3->get('PARAMS.promo'));
    $f3->set('program', $program['H3']);
    //Get & set the usefull variables
    $f3->set('group', $f3->get('PARAMS.group'));
    $this->tpl['sync']='renderGroup.html';
  }
  
  public function getSupgroupSchedule($f3) {
    //Getting the full promo program, setting it & rendering it to be parsed for just a subgroup program in renderGroup template
    $program = $this->getSchedule($f3->get('PARAMS.promo'));
    $f3->set('program', $program['H3']);
    //Get & set the usefull variables
    $f3->set('group', $f3->get('PARAMS.group'));
    $f3->set('subgroup', $f3->get('PARAMS.subgroup'));
    $this->tpl['sync']='renderSubgroup.html';
  }
  
  public function getSchedule($promo){
    //returns the asked promo's program
    $program = $this->model->getJson(array('promo'=>$promo));
    return $program;
  }
}
?>