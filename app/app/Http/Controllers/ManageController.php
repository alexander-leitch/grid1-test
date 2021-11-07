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
    
    if($req) {
      
      $exists = Storage::disk('uploads')->exists($req->file);
      if($exists) {
        $file = Storage::disk('uploads')->get($req->file);
        // dd(json_decode($file)[0]);
        foreach(json_decode($file) as $item) {
          print_r($item);
          die();
        }
        // $json = json_encode($file, true);
        // echo $json;
        // dd($req->file);
      }

    }
    
    // $contents = Storage::get('/');
    // $directory = storage_path('app');
    // dump($directory);
    // $files = Storage::disk('public')->files($directory);
    
    $files = array_filter(Storage::disk('uploads')->files(), function ($item) {
      dump($item);
      $check = File::where('name', $item)->take(1)->get();
      return $check;
    });
    
    return $this->template('welcome', compact('files'));
  }
  
  public function process($file = null) {
    header('Content-Type: text/event-stream');
    header('Cache-Control: no-cache');
    $exists = Storage::disk('uploads')->exists($file);
    if($exists) {
      
      $check = File::where('name', $file)->take(1)->get();
      // dump($check);
      if($check->count() == 0){
        $check = File::insert(['name' => $file]);
        dd($check->exists());
      }
      // $this->send_message(0, $check->name, 0);
      
      $file = Storage::disk('uploads')->get($file);
      foreach(json_decode($file) as $i => $item) {
        
        $purchase = (array) $item;
        unset($purchase['credit_card']);
        $credit_card = (array) $item->credit_card;
        
        $purchase['date_of_birth'] = strtotime($purchase['date_of_birth']);
        print_r($purchase);
        print_r($credit_card);
        
        $purchaseID = Purchase::insertGetId($purchase);
        $credit_card['purchase_id'] = $purchaseID;
        // $credit_card['number'] = Cards::encrypt($credit_card['number'], 'password');
        $card = Cards::updateOrInsert($credit_card);
        if($card){
          echo 'Good';
        }
        
        print_r($purchaseID);
        print_r($card);
        
        
        
        die();
        
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
        
        $this->send_message($i, $item->name . ' on iteration ' . $i . ' of ' . count(json_decode($file)) , round(($i/count(json_decode($file)))*100, 2)); 
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
