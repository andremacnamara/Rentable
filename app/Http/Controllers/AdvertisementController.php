<?php

namespace App\Http\Controllers;


use Auth;
use DB;

use App\PropertyAdvert;
use App\PropertyAdvertPhoto;
use App\User;

use App\Mail\AdvertPosted;


use App\Http\Requests\UploadRequest;
use Illuminate\Http\Request;



class AdvertisementController extends Controller
{

    public function index(){
      $user = Auth::user();
      $Advert = PropertyAdvert::get();
      return view('pages/advert/index', compact('Advert', 'user'));
    }

    public function create(){
     
      $county = DB::table('county')->get();
      $types  = DB::table('property_type')->get();
      $towns = DB::table('town')->get();
      $specs  = DB::table('property_specs')->get();
      $user = Auth::user();
      

      //Only allows landlords to post
      if($user->hasRole('Tenant')){
          flash('Access Denied')->warning();

          return redirect('/');
      }
      else {
        return view('pages/advert/create', compact('user', 'county', 'types', 'specs', 'towns'));
      }
    }

    public function store(Request $request){

      $this->validate($request, [
        "photo" => "required|url",
        "address" => "required",
        "county" => "required",
        "town" => "required",
        "type" => "required",
        "rent" => "required",
        "date" => "required|date",
        "bedrooms" => "required",
        "bathrooms" => "required",
        "furnished" => "required",
        "description" => "required",
      ]);
      
      //Stores data from form
      $Advert = PropertyAdvert::create([
          "photo"       => $request->photo,
          "address"     => $request->address,
          "county"      => $request->county,
          "town"        => $request->town,
          "type"        => $request->type,
          "rent"        => $request->rent,
          "date"        => $request->date,
          "bedrooms"    => $request->bedrooms,
          "bathrooms"   => $request->bathrooms,
          "furnished"   => $request->furnished,
          "description" => $request->description,
          "user_id" => Auth::id(),
      ]);

      
      //Sends email off to user
      \Mail::to(auth()->user())->send(new AdvertPosted(auth()->user(), $Advert));

      //Gets the advertid for redirect to show page
      $id = $Advert->id;

    return redirect("/property/$id");
  }

    public function show($id){
      //Shows add user clicked on based on id
      $Advert = PropertyAdvert::find($id);
      $user = $Advert->user;

      return view('pages/advert/show', compact('Advert', 'user'));
      
    }

    public function edit($id){
      //Similar to create
      //Form with prepopulated data
      $user = Auth::user();

      $types    = DB::table('property_type')->get();
      $specs    = DB::table('property_specs')->get();
      $counties = DB::table('county')->get();
      $towns    = DB::table('town')->get();
      $Advert = PropertyAdvert::where('id', $id)->first();

      return view ('pages/advert/edit', compact('Advert', 'user', 'types', 'specs', 'counties', 'towns'));
    }

    public function update(Request $request, $id){
      //Similar to Stores
      //Posts updated data

      $this->validate($request, [
        "photo" => "required|url",
        "address" => "required",
        "county" => "required",
        "town" => "required",
        "type" => "required",
        "rent" => "required",
        "date" => "required|date",
        "bedrooms" => "required",
        "bathrooms" => "required",
        "furnished" => "required",
        "description" => "required",
      ]);
      
      PropertyAdvert::where('id', $id)->where('user_id', Auth::id())->update([
          "photo"       => $request->photo,
          "address"     => $request->address,
          "county"      => $request->county,
          "town"        => $request->town,
          "type"        => $request->type,
          "rent"        => $request->rent,
          "date"        => $request->date,
          "bedrooms"    => $request->bedrooms,
          "bathrooms"   => $request->bathrooms,
          "furnished"   => $request->furnished,
          "description" => $request->description,
          "user_id" => Auth::id(),
      ]);
      return redirect("/property/$id");
    }

    public function archive(){
      return "Advert has been archived";
    }


    // public function townload(Request $request){
    //   //Typehead function.
    //   //Route => Route::get('/townsearch', 'AdvertisementController@townload');
    //   //
    // 	$data = [];
    //     if($request->has('q')){
    //         $search = $request->q;
    //         $data = DB::table("town")
    //         		->select("id","name")
    //         		->where('name','LIKE',"%".$search."%")
    //         		->get();
    //     }
    //     return response()->json($data);
    // }
}
