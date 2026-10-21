<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request; //Request package
use Illuminate\Support\Facades\Hash; // Hashing
use App\Models\User; //users

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController; // controleer s for the login
use App\Http\Controllers\DivisionController;
use App\Http\Controllers\ProjectController;

use App\Http\Controllers\AdminController;

Route::get('/', function () {
    return view('home');
})->name("home");

// loguin
// v2
Route::post("/form_login", [AuthController::class, "login"])->name("form-login");

Route::middleware(['auth', 'nocache'])->group(function () { //protected routes
    Route::middleware('auth')->group(function () {

    // logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // admin
    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
        Route::get('/admin/manage_users', [AdminController::class, 'manageUsers'])->name('admin.manage_users');
        Route::patch('/admin/users/{user}/role', [AdminController::class, 'updateRole'])->name('admin.users.role');
        Route::get('/admin/tracking', [AdminController::class, 'trackingProjects'])->name('admin.tracking');
    });

    // head
    // Route::middleware('role:head')->group(function (){
    //     Route::get('');
    // });

    // divisions
    Route::get('/division/{name}', [DivisionController::class, 'show'])
        ->whereIn('name', ['niisd', 'dmsd', 'sdmd', 'icts-od'])
        ->middleware('division')
        ->name('division.show');

    // projects creation
    // Route::get('/projects/create', [ProjectController::class, 'create'])->name('projects.create');
    // Route::post('/projects', [ProjectController::class, 'store'])->name('projects.store');
    // Route::get('/projects/{project}', [ProjectController::class, 'show'])->name('projects.show');
    // Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
    
    Route::middleware(['auth'])->get('/admin/projects', [ProjectController::class, 'adminIndex'])->name('admin.projects');
    Route::get('projects/check-contract', [ProjectController::class, 'checkContract'])->name('projects.check-contract');
    Route::resource('projects', ProjectController::class);
    Route::put('projects/{project}', [ProjectController::class, 'update'])->name('projects.update');
    Route::put('projects/{project}/schedule', [ProjectController::class, 'updateSchedule'])->name('projects.schedule');
    Route::delete('/documents/{document}', [ProjectController::class, 'destroyDocument'])->name('documents.destroy');

    // Route::patch('/admin/users/{user}/role', [AdminController::class, 'updateRole'])
    // ->middleware('role:admin')
    // ->name('admin.users.role');

});

});

// register

Route::get('/register_user', [AuthController::class, 'showDivision'])->name('register');

Route::post("/register_user", [AuthController::class, "register"])->name("form-register");


// admin manage user
// Route::get('/admin/manage-users', [
//     AdminController::class,
//     'manage_users'
// ])->middleware('auth')->name('admin.manage_users');

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
