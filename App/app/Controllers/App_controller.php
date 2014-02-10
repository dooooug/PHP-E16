<?php
class App_controller {

  public function __construct(){
    $modelName=substr(get_class($this),0,strpos(get_class($this),'_')+1).'model';
    if(class_exists($modelName)){
      $this->model=new $modelName();
    } 
  }
  
  public function login($f3){
    echo View::instance()->render('login.html');
  }
  
  public function home($f3){
    $user = $this->model->getUser(array('login'=>$f3->get('POST.login'), 'password'=>$f3->get('POST.password')));
    if(!empty($user)) {
      echo View::instance()->render('homepage.html');
    }
    else {
      $f3->reroute('/');
    }
  }
}
?>