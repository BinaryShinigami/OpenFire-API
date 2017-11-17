<?php
/**
 * This file contains the implementation of the open fire administration REST API plugin
 * This implementation relies on cURL for PHP to function, must have PHP cURL installed to operate
 * @author Shane McIntosh
 * @version 0.0.1
 */
 
 namespace ArpStormTechnology\OpenFireAdmin;
 
 class OpenFireApi {
     
     private $serverConnection = null;
     private $serverHost = null;
     private $returnJSON = true;
     private $headers = null;
     public $ret = null;
     
     public function __construct() {
         
         if (! function_exists('curl_init')) {
            throw new Exception();   
         }
         
     }
     
     /** Server Functions */
     public function connect($serverHost, $serverUsername, $serverPassword) {
         
         $this->headers = array('Authorization: Basic ' . base64_encode($serverUsername . ':' . $serverPassword));
         
         if ($this->returnJSON) {
             array_push($this->headers, 'Accept: application/json');
         }
         
         $this->serverHost = $serverHost . '/plugins/restapi/v1/';
         
         $this->serverConnection = curl_init();
         curl_setopt($this->serverConnection, CURLOPT_HTTPHEADER, $this->headers);
         curl_setopt($this->serverConnection, CURLOPT_RETURNTRANSFER, true);
         
     }
     
     /**
      * Returns the HTTP Response Code of the last request
      */
     public function getHttpResponse() {
         $status = curl_getinfo($this->serverConnection, CURLINFO_HTTP_CODE);
         return $status;
     }
     
     /** cURL Functions Private */
     
     /** User Manipulation */
     
     /**
      * Gets a list of all users on the server
      */
     public function getUserList() {
         
         $this->serverConnection = curl_init();
         curl_setopt($this->serverConnection, CURLOPT_HTTPHEADER, $this->headers);
         curl_setopt($this->serverConnection, CURLOPT_RETURNTRANSFER, true);
         curl_setopt($this->serverConnection, CURLOPT_URL, $this->serverHost . 'users');
         curl_setopt($this->serverConnection, CURLOPT_HTTPGET, true);
         
         $data = curl_exec($this->serverConnection);
         
         $this->ret = $data;
         
         return $data;
         
     }

    /**
     * Gets information about a specific user
     */
     public function getUser($username) {
         
         $this->serverConnection = curl_init();
         curl_setopt($this->serverConnection, CURLOPT_HTTPHEADER, $this->headers);
         curl_setopt($this->serverConnection, CURLOPT_RETURNTRANSFER, true);
         curl_setopt($this->serverConnection, CURLOPT_URL, $this->serverHost . 'users/' . htmlspecialchars($username));
         curl_setopt($this->serverConnection, CURLOPT_HTTPGET, true);
         
         $data = curl_exec($this->serverConnection);
         
         $this->ret = $data;
         
         return $data;
         
     }
     
     /**
      * Creates a new user in the chat server with the information provided.
      * $username and $password are required, other fields are optional.
      * Partial implementation currently, doesn't support properties
      */
     public function createUser($username, $password, $name = null, $email = null) {
      
         array_push($this->headers, 'Content-Type: application/json');
         
         $this->serverConnection = curl_init();
         curl_setopt($this->serverConnection, CURLOPT_RETURNTRANSFER, true);
         curl_setopt($this->serverConnection, CURLOPT_URL, $this->serverHost . 'users');
         curl_setopt($this->serverConnection, CURLOPT_POST, true);
         curl_setopt($this->serverConnection, CURLOPT_HTTPHEADER, $this->headers);
         
         $postdata = array(
            'username' => htmlspecialchars($username),
            'password' => htmlspecialchars($password)
          );
          
          if ($name) {
           $postdata['name'] = htmlspecialchars($name);
          }
          
          if ($email) {
           $postdata['email'] = htmlspecialchars($email);
          }
          
          $json_data = json_encode($postdata);
          curl_setopt($this->serverConnection, CURLOPT_POSTFIELDS, $json_data);
          
          $this->ret = curl_exec($this->serverConnection);
          
          if ($this->getHttpResponse() == '201') {
           return true;
          }
          else {
           return false;
          }
         
     }
     
     
     /**
      * Updates a user in the chat server
      * NOTE ---- DUE TO THE WAY THE SERVER WAS DEVELOPED YOU CAN NOT UPDATE A USERNAME 
      * AND A PASSWORD AT THE SAME TIME, THEY MUST BE DONE SEPARATELY!
      */
     public function updateUser($username, $new_username, $new_password, $name = null, $email = null) {
      
         array_push($this->headers, 'Content-Type: application/json');
        
         $this->serverConnection = curl_init();
         curl_setopt($this->serverConnection, CURLOPT_HTTPHEADER, $this->headers);
         curl_setopt($this->serverConnection, CURLOPT_RETURNTRANSFER, true);
         curl_setopt($this->serverConnection, CURLOPT_URL, $this->serverHost . 'users/' . htmlspecialchars($username));
         curl_setopt($this->serverConnection, CURLOPT_CUSTOMREQUEST, 'PUT');
         
         $postdata = array(
            'username' => htmlspecialchars($new_username),
            'password' => htmlspecialchars($new_password)
          );
          
          if ($name) {
           $postdata['name'] = htmlspecialchars($name);
          }
          
          if ($email) {
           $postdata['email'] = htmlspecialchars($email);
          }
          
          $json_data = json_encode($postdata);
          //print_r(htmlspecialchars($json_data));
          curl_setopt($this->serverConnection, CURLOPT_POSTFIELDS, $json_data);
          
          $this->ret = curl_exec($this->serverConnection);
          
          if ($this->getHttpResponse() == '200') {
           return true;
          }
          else {
           return false;
          }
         
     }
     
     /**
      * Deletes a user from the server
      */
     public function deleteUser($username) {
         
         $this->serverConnection = curl_init();
         curl_setopt($this->serverConnection, CURLOPT_HTTPHEADER, $this->headers);
         curl_setopt($this->serverConnection, CURLOPT_RETURNTRANSFER, true);
         curl_setopt($this->serverConnection, CURLOPT_URL, $this->serverHost . 'users/' . htmlspecialchars($username));
         curl_setopt($this->serverConnection, CURLOPT_CUSTOMREQUEST, 'DELETE');
         
         $this->ret = curl_exec($this->serverConnection);
         
         if ($this->getHttpResponse() == '200') {
          return true;
         }
         else {
          return false;
         }
         
     }
     
     /** Group Manipulation */
     /**
      * Create A new Group
      */
     public function createGroup($name, $description) {
      
         array_push($this->headers, 'Content-Type: application/json');
         
         $this->serverConnection = curl_init();
         curl_setopt($this->serverConnection, CURLOPT_RETURNTRANSFER, true);
         curl_setopt($this->serverConnection, CURLOPT_URL, $this->serverHost . 'groups');
         curl_setopt($this->serverConnection, CURLOPT_POST, true);
         curl_setopt($this->serverConnection, CURLOPT_HTTPHEADER, $this->headers);
         
         $postdata = array(
            'name' => htmlspecialchars($name),
            'description' => htmlspecialchars($description)
          );
          
          $json_data = json_encode($postdata);
          curl_setopt($this->serverConnection, CURLOPT_POSTFIELDS, $json_data);
          
          $this->ret = curl_exec($this->serverConnection);
          
          if ($this->getHttpResponse() == '201') {
           return true;
          }
          else {
           return false;
          }
         
     }
     
     public function updateGroup($name, $description) {
      
      array_push($this->headers, 'Content-Type: application/json');
        
         $this->serverConnection = curl_init();
         curl_setopt($this->serverConnection, CURLOPT_HTTPHEADER, $this->headers);
         curl_setopt($this->serverConnection, CURLOPT_RETURNTRANSFER, true);
         curl_setopt($this->serverConnection, CURLOPT_URL, $this->serverHost . 'groups/' . rawurlencode($name));
         curl_setopt($this->serverConnection, CURLOPT_CUSTOMREQUEST, 'PUT');
         
         $postdata = array(
            'name' => htmlspecialchars($name),
            'description' => htmlspecialchars($description)
          );
          
          $json_data = json_encode($postdata);
          //print_r(htmlspecialchars($json_data));
          curl_setopt($this->serverConnection, CURLOPT_POSTFIELDS, $json_data);
          
          $this->ret = curl_exec($this->serverConnection);
          
          if ($this->getHttpResponse() == '200') {
           return true;
          }
          else {
           return false;
          }
         
     }
     
     public function deleteGroup($name) {
      
         $this->serverConnection = curl_init();
         curl_setopt($this->serverConnection, CURLOPT_HTTPHEADER, $this->headers);
         curl_setopt($this->serverConnection, CURLOPT_RETURNTRANSFER, true);
         curl_setopt($this->serverConnection, CURLOPT_URL, $this->serverHost . 'groups/' . rawurlencode($name));
         curl_setopt($this->serverConnection, CURLOPT_CUSTOMREQUEST, 'DELETE');
         
         $ret = curl_exec($this->serverConnection);
         $this->ret = $ret;
         
         if ($this->getHttpResponse() == '200') {
          return true;
         }
         else {
          return false;
         }
         
     }
     
     /**
      * User Group Manipulation Functions
      */
      public function addUserToGroup($user, $group) {
       
         $this->serverConnection = curl_init();
         curl_setopt($this->serverConnection, CURLOPT_HTTPHEADER, $this->headers);
         curl_setopt($this->serverConnection, CURLOPT_RETURNTRANSFER, true);
         curl_setopt($this->serverConnection, CURLOPT_URL, $this->serverHost . 'users/' . htmlspecialchars($user) .'/groups/'.rawurlencode($group));
         curl_setopt($this->serverConnection, CURLOPT_POST, true);

         $this->ret = curl_exec($this->serverConnection);
         
         if ($this->getHttpResponse() == '201') {
          return true;
         }
         else {
          return false;
         }
       
      }
      
      public function removeUserFromGroup($username, $group) {
       
         $this->serverConnection = curl_init();
         curl_setopt($this->serverConnection, CURLOPT_HTTPHEADER, $this->headers);
         curl_setopt($this->serverConnection, CURLOPT_RETURNTRANSFER, true);
         curl_setopt($this->serverConnection, CURLOPT_URL, $this->serverHost . 'users/' . htmlspecialchars($username) . '/groups/' . rawurlencode($group));
         curl_setopt($this->serverConnection, CURLOPT_CUSTOMREQUEST, 'DELETE');
         
         //echo $this->serverHost . 'users/' . htmlspecialchars($username) . 'groups/' . rawurlencode($group);
         
         $ret = curl_exec($this->serverConnection);
         $this->ret = $ret;
         
         if ($this->getHttpResponse() == '200') {
          return true;
         }
         else {
          return false;
         }
       
      }
      
      public function getUserGroups($user) {
       
         $this->serverConnection = curl_init();
         curl_setopt($this->serverConnection, CURLOPT_HTTPHEADER, $this->headers);
         curl_setopt($this->serverConnection, CURLOPT_RETURNTRANSFER, true);
         curl_setopt($this->serverConnection, CURLOPT_URL, $this->serverHost . 'users/' . htmlspecialchars($user) .'/groups');
         curl_setopt($this->serverConnection, CURLOPT_HTTPGET, true);

         $data = curl_exec($this->serverConnection);
         
         $this->ret = $data;
         
         return $data;
       
      }
     
      public function getAllGroups() {
       
         $this->serverConnection = curl_init();
         curl_setopt($this->serverConnection, CURLOPT_HTTPHEADER, $this->headers);
         curl_setopt($this->serverConnection, CURLOPT_RETURNTRANSFER, true);
         curl_setopt($this->serverConnection, CURLOPT_URL, $this->serverHost . 'groups/');
         curl_setopt($this->serverConnection, CURLOPT_HTTPGET, true);
         
         $data = curl_exec($this->serverConnection);
         
         $this->ret = $data;
         
         return $data;
       
      }
      
     /** Misc Functions */
     /**
      * Sends a Broadcast message to all online users
      */
     public function broadcastMessage($message) {
      
         array_push($this->headers, 'Content-Type: application/json');
         
         $this->serverConnection = curl_init();
         curl_setopt($this->serverConnection, CURLOPT_RETURNTRANSFER, true);
         curl_setopt($this->serverConnection, CURLOPT_URL, $this->serverHost . 'messages/users');
         curl_setopt($this->serverConnection, CURLOPT_POST, true);
         curl_setopt($this->serverConnection, CURLOPT_HTTPHEADER, $this->headers);
         
         $postdata = array(
            'body' => htmlspecialchars($message)
          );
          
          $json_data = json_encode($postdata);
          curl_setopt($this->serverConnection, CURLOPT_POSTFIELDS, $json_data);
          
          $this->ret = curl_exec($this->serverConnection);
          
          if ($this->getHttpResponse() == '200') {
           return true;
          }
          else {
           return false;
          }
         
     }
 }


?>