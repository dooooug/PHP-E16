<?php
class App_model extends Model{
  
private $mapper;
private $f3;
  
 public function __construct(){
   parent::__construct();
   $this->mapper=$this->getMapper('users');
   $this->f3=\Base::instance();
 }
 
 public function getUser($params){
   return $this->mapper->load(array('id=?',$params['id']));
 }
 
 public function signin($params){
   return $this->mapper->load(array('login=? and password=?', $params['login'], $params['password']));
 }
 
 public function getPromos(){
   return $this->getMapper('promos')->find();
 }
 
 public function getEvents($params){
   return $this->getMapper('events')->find(array('promo=?', $params['promo']), array('order'=>'id DESC'));
 }
 
 public function getEvent($params){
   return $this->getMapper('events')->load(array('id=?', $params['id']));
 }
 
 public function addEvent(){
   $mapper = $this->getEventMapper();
   $mapper->copyFrom('POST',function($val) {
       return array_intersect_key($val, array_flip(array('title','description','speaker','creator','room','date','hour','promo','priority')));
   });
   $mapper->save();
 }
 
 public function editEvent($params){
   $mapper = $this->getEventMapper();
   $mapper->load(array('id=?',$params['id']));
   $mapper->copyFrom('POST',function($val) {
       return array_intersect_key($val, array_flip(array('title','description','speaker','creator','room','date','hour','promo','priority')));
   });
   $mapper->update();
 }
 
 public function deleteEvent($params){
   $mapper = $this->getEventMapper();
   $mapper->load(array('id=?',$params['id']));
   $mapper->erase();
 }
 
 public function getEventMapper(){
   $mapper = $this->getMapper('events');
   $mapper->reset();
   return $mapper;
 }
 
 public function getJson($params){
   $string = file_get_contents("public/json/".$params['promo'].".json");
   return json_decode($string,true);
 }
 
 public function getPeople() {
   return $this->getMapper('users')->find();
 }
}
?>