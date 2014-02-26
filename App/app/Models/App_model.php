<?php
class App_model extends Model{
  
private $mapper;
private $f3;
  
 public function __construct(){
   parent::__construct();
   $this->mapper=$this->getMapper('students');
   $this->f3=\Base::instance();
 }
 
 public function getUser($params){
   return $this->mapper->load(array('id=?',$params['id']));
 }
 
 public function signin($params){
   return $this->mapper->load(array('login=? and password=?', $params['login'], $params['password']));
 }
}
?>