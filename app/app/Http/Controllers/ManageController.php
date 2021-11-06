<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\File;
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
      return $item;
    });
    
    return $this->template('welcome', compact('files'));
  }
  
  public function process($file = null) {
    header('Content-Type: text/event-stream');
    header('Cache-Control: no-cache');
    $exists = Storage::disk('uploads')->exists($file);
    if($exists) {
      $file = Storage::disk('uploads')->get($file);
      foreach(json_decode($file) as $i => $item) {
        $this->send_message($i, 'on iteration ' . $i . ' of ' . count(json_decode($file)) , round(($i/count(json_decode($file)))*100, 2)); 
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
