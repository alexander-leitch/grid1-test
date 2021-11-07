<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\File;
use App\Models\Purchase;
use App\Models\Cards;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class ManageController extends Controller {

  public function index(Request $req) {
    $files = array_filter(Storage::disk('uploads')->files(), function ($item) {
      return strpos($item, '.json');
    });
    foreach($files as $key => $file){
      $details = File::where('name', $file)->find(1);
      if($details) {
        $files[$key] = $details->toArray();
      } else {
        $files[$key] = ['name' => $file];
      }
    }
    return $this->template('welcome', compact('files'));
  }
  
  public function process($filename = null) {
    header('Content-Type: text/event-stream');
    header('Cache-Control: no-cache');
    $exists = Storage::disk('uploads')->exists($filename);
    if($exists) {
      $file = File::where('name', $filename)->find(1);
      if(!$file){
        $file = File::insert(['name' => $filename, 'completed_rows' => 0]);
        if(!$file){
          $this->send_message('CLOSE', 'Error', 0);
        } else {
          $file = File::where('name', $filename)->find(1);
        }
      }
      
      // TODO Update this section to change for different types of imput files
      $json_file = Storage::disk('uploads')->get($filename);
      $json = json_decode($json_file);
      $json_total = count($json)-1;
      $start_from = ($file['completed_rows'] == 0) ? 0 : $file['completed_rows']+1;
      // TODO end section.
      
      if($json_total == $start_from) {
        // Already Completed
        $file->completed = 1;
        $file->save();
        $this->send_message('CLOSE', ' Already Completed file. ', 100); 
      } else {
        for ($i = $start_from; $i <= $json_total; $i++) {
          $item = $json[$i];
          $purchase = (array) $item;
          unset($purchase['credit_card']);
          $credit_card = (array) $item->credit_card;
          $age = date_diff(date_create(date('Y-m-d H:i:s', strtotime($purchase['date_of_birth']))), date_create('now'))->y;
          if($purchase['date_of_birth'] == '' || ($age >= 18 && $age <= 65)) {
            
            $lookup = Purchase::where($purchase)->find(1);
            if($lookup) {
              // Duplicate, ignore.
              $file->completed_rows = $i;
              $file->save();
              $this->send_message($i, $purchase['name'] . ' DUPLICATE on iteration ' . $i . ' of ' . $json_total , round(($i/$json_total)*100, 2)); 
            } else {
              
              DB::beginTransaction();
              // TODO Additional check section
              $purchaseID = Purchase::insertGetId($purchase);
              $credit_card['purchase_id'] = $purchaseID;
              $card = Cards::updateOrInsert($credit_card);
              if($card){
                $file->completed_rows = $i;
                $file->save();
                $this->send_message($i, $item->name . ' on iteration ' . $i . ' of ' . $json_total , round(($i/$json_total)*100, 2)); 
              }
              DB::commit();
              
            }
            
          } else {
            $file->completed_rows = $i;
            $file->save();
            $this->send_message($i, $item->name . ' too young ' . $i . ' of ' . $json_total , round(($i/$json_total)*100, 2)); 
          }
        }
      }
    } else {
      $this->send_message('CLOSE', 'No File', 0);
    }
    $file->completed = 1;
    $file->save();
    $this->send_message('CLOSE', 'Process complete', 100);
  }
  
  function send_message($id, $message, $progress) {
    $d = array('message' => $message , 'progress' => $progress);
    echo "id: $id" . PHP_EOL;
    echo "data: " . json_encode($d) . PHP_EOL;
    echo PHP_EOL;
   
    ob_flush();
    flush();
  }
}
