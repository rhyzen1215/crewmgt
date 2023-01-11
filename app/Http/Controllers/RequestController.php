<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Functions\CrewFunction;
use App\Models\Functions\DocumentFunction;
use App\Models\Functions\UserFunction;
use App\Models\Functions\UtilitiesFunction;

class RequestController extends Controller
{
    public function getrequest(Request $request){
      $data =  $request->all();
      $response = array();
      $response['error'] = 0;
      $response['msg'] = "";
      if(static::checkData($data)){
        $response['error'] = 1;
        $response['msg'] = static::errorMsg($data);
      }
      else {
        if($data['type'] == "crew"){
          if($data['option'] == "new") $response['msg'] = CrewFunction::addCrew($data);
          else if($data['option'] == "list") $response['msg'] = CrewFunction::listCrew($data);
          else if($data['option'] == "view") $response['msg'] = CrewFunction::viewCrew($data);
          else if($data['option'] == "update") $response['msg'] = CrewFunction::updateCrew($data);
          else if($data['option'] == "delete") $response['msg'] = CrewFunction::deleteCrew($data);
        }
        else if($data['type'] == "document"){
          if($data['option'] == "save") $response['msg'] = DocumentFunction::addDocument($data);
          else if($data['option'] == "list") $response['msg'] = DocumentFunction::listDocument($data);
          else if($data['option'] == "view") $response['msg'] = DocumentFunction::viewDocument($data);
          else if($data['option'] == "delete") {
            $response['msg'] = DocumentFunction::deleteDocument($data);
          }
          else if($data['option'] == "update") {
            $response['msg'] = DocumentFunction::updateDocument($data);
          }
          else if($data['option'] == "upload"){
            $validatedData = $request->validate(['file' => 'required|mimes:pdf|max:2048',]);
            if(isset($request->lastpath)){
              $lastpath = public_path($request->lastpath);
              $response['msg2'] = $lastpath;
              unlink($lastpath);
            }
            $name = $request->file('file')->getClientOriginalName();
            $folder = '/documents/'.$request->crewid;
            $path = public_path($folder);
            if(!file_exists($path)) mkdir($path, 0777, true);
            $request->file->move($path,$name);
            $response['msg'] = $folder.'/'.$name;
          }
        }
        else if($data['type'] == "user"){
          if($data['option'] == "new") $response['msg'] = UserFunction::addUser($data);
          else if($data['option'] == "list") $response['msg'] = UserFunction::listUser();
          else if($data['option'] == "view") $response['msg'] = UserFunction::viewUser($data);
          else if($data['option'] == "update") $response['msg'] = UserFunction::updateUser($data);
          else if($data['option'] == "delete") $response['msg'] = UserFunction::deleteUser($data);
        }
        else if($data['type'] == "usertype"){
          if($data['option'] == "new") $response['msg'] = UserFunction::addUserType($data);
          else if($data['option'] == "list") $response['msg'] = UserFunction::listUserType();
          else if($data['option'] == "view") $response['msg'] = UserFunction::viewUserType($data);
          else if($data['option'] == "update") $response['msg'] = UserFunction::updateUserType($data);
          else if($data['option'] == "delete") $response['msg'] = UserFunction::deleteUserType($data);
        }
        else if($data['type'] == "rank"){
          if($data['option'] == "new") $response['msg'] = UtilitiesFunction::addRank($data);
          else if($data['option'] == "list") $response['msg'] = UtilitiesFunction::listRank();
          else if($data['option'] == "view") $response['msg'] = UtilitiesFunction::viewRank($data);
          else if($data['option'] == "update") $response['msg'] = UtilitiesFunction::updateRank($data);
          else if($data['option'] == "delete") $response['msg'] = UtilitiesFunction::deleteRank($data);
        }
        else if($data['type'] == "doctype"){
          if($data['option'] == "new") $response['msg'] = UtilitiesFunction::addDocType($data);
          else if($data['option'] == "list") $response['msg'] = UtilitiesFunction::listDocType();
          else if($data['option'] == "view") $response['msg'] = UtilitiesFunction::viewDocType($data);
          else if($data['option'] == "update") $response['msg'] = UtilitiesFunction::updateDocType($data);
          else if($data['option'] == "delete") $response['msg'] = UtilitiesFunction::deleteDocType($data);
        }
        else if($data['type'] == "defaults"){
          if($data['option'] == "all") {
            $response['msg'] = DocumentFunction::getDefaults();
          }
          else if($data['option'] == "usertype") {
            $response['msg'] = UserFunction::listUserType();
          }
        }
        else if($data['type'] == "login"){
          $loginData = UserFunction::login($data);
          if(isset($loginData['fullname'])){
            session(['logindata' => $loginData]);
          }
          else {
            session(['logindata' => null]);
            $response['error'] = 1;
            $response['msg'] = "Unable to Login";
          }
        }
        else if($data['type'] == "logout"){
          session(['logindata' => null]);
        }
      }
      return $response;
    }

    private static function checkData($data){
      $error = false;
      foreach($data as $key => $val){
        if($val == null){
          $error = true;
        }
      }
      if($data['type'] == "user"){
        if($data['option'] == "new" || $data['option'] == "update"){
          $usr = $data['username'];
          $pass = $data['password'];
          $matchuser = preg_match('/[^a-zA-Z]/',$usr);
          $matchpass = preg_match('/[^a-zA-Z0-9]/',$pass);
          if($matchuser > 0) $error = true;
          else if(strlen($usr) > 10 ) $error = true;
          if($matchpass > 0) $error = true;
          else if(strlen($pass) > 20 ) $error = true;
          if($data['password'] != $data['cpassword']) $error = true;
          if($data['option'] == "new"){
            if(UserFunction::isUserExist($usr)) $error = true;
          }
        }
      }
      return $error;
    }

    private static function errorMsg($param){
      $msg = "";
      if($param['type'] == "crew"){
        if($param['option'] == "new") $msg = "Please complete the form!";
        else if($param['option'] == "list") $msg = "No Crew Found!";
      }
      else if($param['type'] == "document"){
        if($param['option'] == "save") $msg = "Please complete the form!";
        else if($param['option'] == "update") $msg = "Please complete the form!";
      }
      else if($param['type'] == "usertype"){
        if($param['option'] == "new") $msg = "Please complete the form!";
        else if($param['option'] == "update") $msg = "Please complete the form!";
      }
      else if($param['type'] == "user"){
        $usr = $param['username'];
        $pass = $param['password'];
        $matchuser = preg_match('/[^a-zA-Z]/',$usr);
        $matchpass = preg_match('/[^a-zA-Z0-9]/',$pass);
        if($matchuser > 0) $msg = "Username accept only string character";
        else if(strlen($usr) > 10 ) $msg = "Username maximum of 10 character";
        if($matchpass > 0) $msg = "Passwords dont accept special character";
        else if(strlen($pass) > 20 ) $msg = "Passwords maximum of 20 character";
        if($param['password'] != $param['cpassword']) $msg = "Passwords do not match";
        if($param['option'] == "new"){
          if(UserFunction::isUserExist($usr)) $msg = "User Already Existed";
        }

      }
      else if($param['type'] == "rank"){
        if($param['option'] == "new") $msg = "Please complete the form!";
        else if($param['option'] == "update") $msg = "Please complete the form!";
      }
      else if($param['type'] == "doctype"){
        if($param['option'] == "new") $msg = "Please complete the form!";
        else if($param['option'] == "update") $msg = "Please complete the form!";
      }
      return $msg;
    }
}
