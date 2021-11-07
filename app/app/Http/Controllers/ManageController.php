<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\File;
use App\Models\Purchase;
use App\Models\Cards;
use Illuminate\Support\Facades\Storage;

class ManageController extends Controller {
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index(Request $req) {
    $files = array_filter(Storage::disk('uploads')->files(), function ($item) {
      return strpos($item, '.json');
    });
    foreach($files as $key => $file){
      $details = File::where('name', $file)->find(1);
      if($details->exists()) {
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
      print_r($file);
      if($file->count() == 0){
        $file = File::insert(['name' => $filename]);
        if(!$file->exists()){
          $this->send_message('CLOSE', 'Error', 0);
        }
      }
      
      $json_file = Storage::disk('uploads')->get($filename);
      $json = json_decode($json_file);
      
      for ($i = $file['completed_rows']; $i <= count($json); $i++) {
        $item = $json[$i];
      // foreach($json as $i => $item) {
        
        $purchase = (array) $item;
        unset($purchase['credit_card']);
        $credit_card = (array) $item->credit_card;
        $purchase['date_of_birth'] = date('Y-m-d H:i:s', strtotime($purchase['date_of_birth']));
        $purchaseID = Purchase::insertGetId($purchase);
        $credit_card['purchase_id'] = $purchaseID;
        // $credit_card['number'] = Cards::encrypt($credit_card['number'], 'password');
        $card = Cards::updateOrInsert($credit_card);
        if($card){
          $file->completed_rows = $i;
          if($i == count($json)) {
            $file->completed = true;
          }
          $file->save();
          $this->send_message($i, $item->name . ' on iteration ' . $i . ' of ' . count($json) , round(($i/count($json))*100, 2)); 
        }
        
        
        /*
stdClass Object
(
    [name] => Prof. Simeon Green
    [address] => 328 Bergstrom Heights Suite 709 49592 Lake Allenville
    [checked] => 
    [description] => Voluptatibus nihil dolor quaerat. Reprehenderit est molestias quia nihil consectetur voluptatum et.<br>Ea officiis ex ea suscipit dolorem. Ut ab vero fuga.<br>Quam ipsum nisi debitis repudiandae quibusdam. Sint quisquam vitae rerum nobis.
    [interest] => 
    [date_of_birth] => 1989-03-21T01:11:13+00:00
    [email] => nerdman@cormier.net
    [account] => 556436171909
    [credit_card] => stdClass Object
        (
            [type] => Visa
            [number] => 4532383564703
            [name] => Brooks Hudson
            [expirationDate] => 12/19
        )

)
        */
        
        
      }
    } else {
      $this->send_message('CLOSE', 'No File', 0);
    }
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
