<?php
/**
 * Created by PhpStorm.
 * User: oshry
 * Date: 19/08/2016
 * Time: 11:27 AM
 */

namespace Layout\Admin;
use Layout\Modules;
use Common;

use PDO;
class AdminPanel{
    public function __construct($db){
        $this->repo = $db;
        //commonly use'd methods
        $this->common = new Common\CommonMethods;
    }
    public function load($method, $paths, $m){
        try{
            switch($method){
                case 'GET':
                    //die('Here');
                    if($this->common->isAjaxing()){
                        if($paths[1] === 'api' && $paths[2] === 'modules_list'){
                            $results['modules_list'] = $this->get_modules();
                            die(json_encode($results));
                        }elseif($paths[1] === 'api' && $paths[2] === 'load_configuration_form'){
                            $lala = $this->load_configuration_form($paths[3], $paths[4]);
                            die($lala);
                        }
                        throw new \Exception('unknown ajax request');
                    }else{
                        $results = $this->init();
                        $results['admin_header'] = 'Admin Header';
                        $results['assets'] = BASE.'assets/';
                        $results['css_file'] = BASE.'assets/css/admin.css';
                        $view_products = $m->render('admin', $results);
                        echo $view_products;
                        die();
                    }
                    break;
                case 'POST':
                    if($this->common->isAjaxing()){
                        if($paths[1] === 'api' && $paths[2] === 'insert_area'){
                            //insert area and return area id for changing it's config id  after added
                            echo $this->insert_area();
                            die();
                            //die(print_r($_POST));
                            //$results['modules_list'] = $admin->get_modules();
                            //die(json_encode($results));
                        }elseif($paths[1] === 'api' && $paths[2] === 'submit_configuration_form'){
                            //insert area configuration
                            $lala = $this->submit_configuration_form($paths[3]);
                            die($lala);
                        }elseif($paths[1] === 'api' && $paths[2] === 'create_page'){
                            //insert area and return area id for changing it's config id  after added
                            echo $this->create_page();
                            die();
                            //die(print_r($_POST));
                            //$results['modules_list'] = $admin->get_modules();
                            //die(json_encode($results));
                        }
                        throw new \Exception('unknown ajax request');
                    }
                    break;
                default:
                    echo 'default';
                    break;
            }
        }catch(Exception $e){
            $message = $e->getMessage();
        }
    }
    public function init(){
        $output = '';
        //get all pages
        $statement = $this->repo->db->prepare("SELECT * FROM `pages`");
        $statement->execute();
        $output['content']['list'] = $statement->fetchAll();
        //get all layouts
        $statement = $this->repo->db->prepare("SELECT * FROM `layouts`");
        $statement->execute();
        $output['layouts_list'] = $statement->fetchAll();
        $output['content']['form'] = true;
        return $output;
    }
    //count number of areas in a layout
//    public function count_layout_modules($id){
//        $statement = $this->repo->db->prepare("SELECT * FROM `layouts` WHERE id=:id");
//        $statement->bindValue(':id', $id);
//        $statement->execute();
//        $result = $statement->fetchColumn(3);
//        $num = substr_count($result, '{{module');
//        return $num;
//    }
    //get all modules
    public function get_modules(){
        $statement = $this->repo->db->prepare("SELECT id, name FROM `modules`");
        $statement->execute();
        $results = $statement->fetchAll();
        return $results;
    }
    //load module configuration form with area hidden field for future update
    public function load_configuration_form($module_id, $area_id){
        $pdo_errors = [];
        //check if inserted module needs configuration
        $stmt = $this->repo->db->prepare("SELECT * FROM `modules` WHERE `id` = :id");
        $stmt->bindValue(':id', $module_id);
        $stmt->execute();
        $results = $stmt->fetchAll();
        if($results[0]['configuration'] == 1){
            //module needs configuration. get configuration table fields
            $q = $this->repo->db->prepare("DESCRIBE {$results[0]['table_name']}");
            $q->execute();
            $table_fields = $q->fetchAll(PDO::FETCH_COLUMN);
            //create configuration form
            $form = '<form class="configuration_form1" method="post" onsubmit="event.preventDefault();">';
            $form .="<input type=\"hidden\" name=\"area_id\" value=\"{$area_id}\"/>";
            foreach($table_fields as $k){
                if($k == 'id'){
                    continue;
                }else{
                    $form.="<div class=\"line\"><label for=\"{$k}\">{$k}</label></div>";
                    $form.="<div class=\"line\"><input type=\"text\" id=\"{$k}\" name=\"{$k}\"/></div>";
                }
            }
            $form.='<input type="submit" name="submit" class="button" id="submit_btn" value="Submit" />';
            $form.= '</form>';
            $form.='<script>
                    $(".configuration_form1").on("submit", function(e){
                        e.preventDefault();
                        var data = $(this).serialize();
                        var _this = this;
                        $.ajax({
                            type:"POST",
                            url:"/tests/2/admin/api/submit_configuration_form/'.$module_id.'",
                            data: data,
                            success:function(data){
                                $(_this).find("input[type=submit]").prop("disabled", true);

                                console.log("success222");
                            },
                            error:function(){
                                console.log("error222");
                            }
                        });
                        console.log(data);
                    });       
                    </script>';
            return $form;
        }else{
            //save module -> no configuration needed
        }
        //echo "<pre>",print_r($results), "</pre>";
        //die('sdsdsd');

        if(!$stmt->errorCode() == 0) {
            $errors = $stmt->errorInfo();
            $pdo_errors[] = $errors[2];
        }
    }
    //insert area return area id
    public function create_page(){
        $pdo_errors = [];
        //insert area
        $statement = $this->repo->db->prepare("INSERT INTO `pages` (page_name, url, layout_id, active) values (:page_name, :url, :layout_id, :active)");
        $statement->bindValue(':page_name', $_POST['name']);
        $statement->bindValue(':url', $_POST['url']);
        $statement->bindValue(':layout_id', $_POST['layout_id']);
        $statement->bindValue(':active', 1);
        $statement->execute();
//        if(!$statement->errorCode() == 0) {
//            $errors = $statement->errorInfo();
//            $pdo_errors[] = $errors[2];
//            print_r($pdo_errors);
//            die();
//        }

        $lastId = $this->repo->db->lastInsertId();
        return $lastId;
    }
    //insert area return area id
    public function insert_area(){
        $pdo_errors = [];
        //insert area
        $statement = $this->repo->db->prepare("INSERT INTO `areas` (page_id, layout_areas, module_type, module_configuration_id) values (:page_id, :layout_id, :module_type, :config_id)");
        $statement->bindValue(':page_id', $_POST['page_id']);
        $statement->bindValue(':layout_id', $_POST['layout_areas']);
        $statement->bindValue(':module_type', $_POST['select-module']);
        $statement->bindValue(':config_id', 1);
        $statement->execute();
//        if(!$statement->errorCode() == 0) {
//            $errors = $statement->errorInfo();
//            $pdo_errors[] = $errors[2];
//            print_r($pdo_errors);
//            die();
//        }

        $lastId = $this->repo->db->lastInsertId();
        return $lastId;
    }
    //submit module configuration file
    public function submit_configuration_form($id){
        //check if inserted module needs configuration
        $stmt = $this->repo->db->prepare("SELECT * FROM `modules` WHERE `id` = :id");
        $stmt->bindValue(':id', $id);
        $stmt->execute();
        $results = $stmt->fetchAll();
        //switch between modules
        switch($results[0]['name']){
            case 'Externalurl':
                $module = new Modules\ExternalUrl($this->repo);
                break;
            default:
                break;
        }
        //insert configuration
        $module->insert();
        //update area configuration id

        die();
    }

}