<?php

/**
 * Created by PhpStorm.
 * User: oshry
 * Date: 17/08/2016
 * Time: 4:59 PM
 */
namespace Layout\Modules;
class ExternalUrl{
    protected $_table_name = 'external_url';
    public function __construct($repo){
        $this->repo = $repo;
    }
    public function init($confid){
        $statement = $this->repo->db->prepare("SELECT * FROM {$this->_table_name} eu WHERE eu.id = :id");
        $statement->bindValue(':id', $confid);
        $statement->execute();
        $results = $statement->fetchAll();
        $output = "<a href=\"{$results[0]['link']}\">{$results[0]['label']}</a>";
        return $output;
    }
    //update area configuration id to the new module configuration.
    //when creating an area the configuration id is set to 1 by default
    public function update_area_configuration($area_id, $configuration_id){
        $stmt = $this->repo->db->prepare("UPDATE `areas` SET `module_configuration_id` = :configuration_id WHERE `id` = :area_id");
        $stmt->bindValue(':configuration_id', $configuration_id);
        $stmt->bindValue(':area_id', $area_id);
        $stmt->execute();
    }
    //insert configuration and return configuration id
    public function insert(){
        $pdo_errors = [];
        //insert area
        $statement = $this->repo->db->prepare("INSERT INTO {$this->_table_name} (label, link, target) values (:label, :link, :target)");
        $statement->bindValue(':label', $_POST['label']);
        $statement->bindValue(':link', $_POST['link']);
        $statement->bindValue(':target', '_blank');
        $statement->execute();
        if(!$statement->errorCode() == 0) {
            $errors = $statement->errorInfo();
            $pdo_errors[] = $errors[2];
        }
        $lastId = $this->repo->db->lastInsertId();
        $this->update_area_configuration($_POST['area_id'],$lastId);
        return $lastId;
    }
}