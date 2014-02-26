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
 
 public function getEvents($params){
   return $this->getMapper('events')->find(array('promo=?', $params['promo']));
 }
 
 public function getEvent($params){
   return $this->getMapper('events')->load(array('id=?', $params['id']));
 }
 
 public function addEvent(){
   $mapper = $this->getMapper('events');
   $mapper->reset();
   $mapper->copyFrom('POST',function($val) {
       return array_intersect_key($val, array_flip(array('title','description','speaker','creator','room','date','hour','promo','priority')));
   });
   $mapper->save();
 }
 
 public function editEvent($params){
   $mapper = $this->getMapper('events');
   $mapper->reset();
   $mapper->load(array('id=?',$params['id']));
   $mapper->copyFrom('POST',function($val) {
       return array_intersect_key($val, array_flip(array('title','description','speaker','creator','room','date','hour','promo','priority')));
   });
   $mapper->update();
 }
 
 public function deleteEvent($params){
   $mapper = $this->getMapper('events');
   $mapper->reset();
   $mapper->load(array('id=?',$params['id']));
   $mapper->erase();
 }
}
?>