<?php


use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Illuminate\Support\Facades\Route;
use App\Models\Doctor;
use App\Models\Section;
use App\Models\Service;
use App\Http\Controllers\ProfileController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::group(
    [
        'prefix' => LaravelLocalization::setLocale(),
        'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath']
    ],
    function () {

        Route::get('/', function () {
            $sections = Section::with(['doctors' => function ($query) {
                $query->where('status', 1)->with('image');
            }])->get()->each(function ($section) {
                $section->setRelation('doctors', $section->doctors->filter(function ($doctor) {
                    return filled($doctor->name);
                }));
            })->filter(function ($section) {
                return filled($section->name) && $section->doctors->isNotEmpty();
            });
            $doctors = Doctor::where('status', 1)->with(['section', 'image'])->get()->filter(function ($doctor) {
                return filled($doctor->name) && $doctor->section && filled($doctor->section->name);
            });
            $services = Service::where('status', 1)->get()->filter(function ($service) {
                return filled($service->name) && is_numeric($service->price);
            });

            return view('Dashboard.User.auth.signin', compact('sections', 'doctors', 'services'));
        });


            Route::middleware('auth')->group(function () {
                Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
                Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
                Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
            });

        }
    );
