<?php
class App_model extends Model{
  
private $mapper;
private $f3;
  
 public function __construct(){
   parent::__construct();
   //Setting mapper & $f3
   $this->mapper=$this->getMapper('users');
   $this->f3=\Base::instance();
 }
 
 public function getUser($params){
   //Returns a user by ID
   return $this->mapper->load(array('id=?',$params['id']));
 }
 
 public function signin($params){
   //Returns a user by login & password
   return $this->mapper->load(array('login=? and password=?', $params['login'], $params['password']));
 }
 
 public function getPromos(){
   //Returns all the promos
   return $this->getMapper('promos')->find();
 }
 
 public function getEvents($params){
   //Returns events by promo in desc order
   return $this->getMapper('events')->find(array('promo=?', $params['promo']), array('order'=>'id DESC'));
 }
 
 public function getEvent($params){
   //Returns an event by ID
   return $this->getMapper('events')->load(array('id=?', $params['id']));
 }
 
 public function addEvent(){
   //Add an event on post's datas
   $mapper = $this->getEventMapper();
   $mapper->copyFrom('POST',function($val) {
       return array_intersect_key($val, array_flip(array('title','creator','room','date','hour','promo','priority')));
   });
   $mapper->save();
 }
 
 public function editEvent($params){
   //Edit an event on post's datas & by event ID
   $mapper = $this->getEventMapper();
   $mapper->load(array('id=?',$params['id']));
   $mapper->copyFrom('POST',function($val) {
       return array_intersect_key($val, array_flip(array('title','creator','room','date','hour','promo','priority')));
   });
   $mapper->update();
 }
 
 public function deleteEvent($params){
   //Delete an event on it's ID
   $mapper = $this->getEventMapper();
   $mapper->load(array('id=?',$params['id']));
   $mapper->erase();
 }
 
 public function getEventMapper(){
   //returns the event mapper after reseting it
   $mapper = $this->getMapper('events');
   $mapper->reset();
   return $mapper;
 }
 
 public function getJson($params){
   //returns a promo's JSON
   $string = file_get_contents("public/json/".strtolower($params['promo']).".json");
   return json_decode($string,true);
 }
 
 public function getPeople() {
   //return all users
   return $this->getMapper('users')->find();
 }
}
?>