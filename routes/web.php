<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request; //Request package
use Illuminate\Support\Facades\Hash; // Hashing
use App\Models\User; //users

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController; // controleer s for the login
use App\Http\Controllers\DivisionController;

Route::get('/', function () {
    return view('home');
})->name("home");

// loguin
// v2
Route::post("/form_login", [AuthController::class, "login"])->name("form-login");

Route::middleware(['auth', 'nocache'])->group(function () { //protected routes
    Route::middleware('auth')->group(function () {

    Route::get('/division', [DivisionController::class, 'admin'])
        ->middleware('role:admin')
        ->name('division.admin');

    Route::get('/division/{name}', [DivisionController::class, 'show'])
        ->whereIn('name', ['niisd', 'database', 'development'])
        ->middleware('division')
        ->name('division.show');

});

// logout
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

});



// register

Route::get('/register_user', [AuthController::class, 'showDivision'])->name('register');

Route::post("/register_user", [AuthController::class, "register"])->name("form-register");

// v1
// Route::post("/form_login", function (Request $request){

//     $request->validate([
//         "email" => "required|email|max:50",
//         "password" => "required"
//     ]); // email also if want validation of emial

//     $email = $request->input("email");    
//     $password = $request->input("password");

//     // $hashedPassword = $hash->make($password);

//     $user = User::where("email", $email)->first();
    
//     if(!$user){
//         return "Invalid Email or password";
//     }

//     if(Hash::check($password, $user->password)){
//         return "Login Success!";
//     }

//     return "Invalid Email or password";

// })->name("form-login");



// Route::post("/form_register", function (Request $request){
//     $request->validate([
//         "email" => "required|email|unique:users,email",
//         "name" => "required|min:3|max:50",
//         "password" => "required|confirmed",
//     ]);

//     $email = $request->input("email");
//     $name = $request->input("name");
//     $password = $request->input("password");

//     $user = new User();
//     $user->email = $email;
//     $user->name = $name;
//     $user->password = Hash::make($password);
//     $user->save();

//     return  "Registered Sucessfully!";
    
// })->name("form-register");

// Route::prefix('division')->group(function () {

//     Route::get('/niisd', function () {
//         return view('division.niisd');
//     });

//     Route::get('/database', function () {
//         return view('division.database');
//     });
        
//     Route::get('/development', function () {
//         return view('division.development');
//     });

// });
