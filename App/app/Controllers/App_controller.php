<?php
class App_controller {

  public function __construct(){
    $modelName=substr(get_class($this),0,strpos(get_class($this),'_')+1).'model';
    if(class_exists($modelName)){
      $this->model=new $modelName();
    } 
  }
  
  public function home($f3){
    if($f3->get('SESSION.heticalUserId') && $f3->get('SESSION.heticalUserId')!= 0) {
      $f3->set('user', $this->model->getUserById(array('id'=>$f3->get('SESSION.heticalUserId'))));
      echo View::instance()->render('homepage.html');
    }
    else if($f3->get('POST.login') && $f3->get('POST.password')) {
      $user = $this->model->getUser(array('login'=>$f3->get('POST.login'), 'password'=>$f3->get('POST.password')));
      if(!empty($user)) {
        new Session();
        $f3->set('SESSION.heticalUserId',$user->id);
        $f3->reroute('/');
      }
      else {
        $f3->reroute('/');
      }
    }
    else {
      echo View::instance()->render('login.html');
    }
  }
}
?>