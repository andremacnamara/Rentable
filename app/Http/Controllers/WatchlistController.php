<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\user;
use App\PropertyAdvert;
use App\Watchlists;

class WatchlistController extends Controller
{
  public function __construct()
  {
      $this->middleware('auth');
  }

  //Crud Operations for Watchlist Form

  public function index(){
    $user = Auth::user();
    $Watchlists = Watchlists::where('user_id', Auth::id())->get();

    return view('/pages/watchlist/index', compact('Watchlists', 'user'));
  }

  public function create(){
    $user = Auth::user();
    return view('/pages/watchlist/create', compact('user'));
  }

  public function store(Request $request){

    $this->validate($request, [
      "title" => "required",
      "active" => "required",
    ]);

    $Watchlists = new Watchlists();
    if($request->active == 1){
      Watchlists::where('user_id', Auth::id())->where('active', 1)->update(["active" => 0]);
    }

    $Watchlists::create([
      "title"   => $request->title,
      "user_id" => Auth::id(),
      "active"  => $request->active
    ]);

    return redirect('watchlist');

  }

  public function show($id){
    $Watchlists = Watchlists::where('id', $id)->first();
    $user = Auth::user();
    return view('pages/watchlist/show', compact('Watchlists', 'user'));
  }

  public function edit($id){
    $Watchlists = Watchlists::where('id', $id)->first();
    $user = Auth::user();
    return view('pages/watchlist/edit', compact('Watchlists', 'user'));
  }

  public function update(Request $request, $id){

    $this->validate($request, [
      "title" => "required",
      "active" => "required",
    ]);
    
    if($request->active == 1) {
        Watchlists::where('user_id', Auth::id())->where('active', 1)->update(["active" => 0]);
      }
      Watchlists::where('id', $id)->where('user_id', Auth::id())->update([
        "title" => $request->title,
        "active" => $request->active,
      ]);

      return redirect('watchlist');
  }

  public function destroy($id){

    $user = Auth::user();
    $Watchlist = Watchlists::where('id', $id)->first();

    if($Watchlist->user_id == Auth::id()){
      $Watchlist->delete();
      return redirect('watchlist');
    } else {
      return redirect('watchlist');
    }

    if($user->WatchedProperties == $user){
      return redirect('watchlist');
    } else {
      return redirect('watchlist');
    }
  }
}
