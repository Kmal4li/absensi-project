<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\HolidayController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\PresenceController;
use App\Http\Controllers\PerjalananController;
use App\Exports\PresencesExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Presence; 
use Illuminate\Support\Facades\Route;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;

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


Route::middleware('auth')->group(function () {
    Route::middleware('role:admin,operator')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
        // positions
        Route::resource('/positions', PositionController::class)->only(['index', 'create']);
        Route::get('/positions/edit', [PositionController::class, 'edit'])->name('positions.edit');
        // employees
        Route::resource('/employees', EmployeeController::class)->only(['index', 'create']);
        Route::get('/employees/edit', [EmployeeController::class, 'edit'])->name('employees.edit');
        // holidays (hari libur)
        Route::resource('/holidays', HolidayController::class)->only(['index', 'create']);
        Route::get('/holidays/edit', [HolidayController::class, 'edit'])->name('holidays.edit');
        // attendances (absensi)
        Route::resource('/attendances', AttendanceController::class)->only(['index', 'create']);
        Route::get('/attendances/edit', [AttendanceController::class, 'edit'])->name('attendances.edit');

        // presences (kehadiran)
        Route::resource('/presences', PresenceController::class)->only(['index']);
        Route::get('/presences/qrcode', [PresenceController::class, 'showQrcode'])->name('presences.qrcode');
        Route::get('/presences/qrcode/download-pdf', [PresenceController::class, 'downloadQrCodePDF'])->name('presences.qrcode.download-pdf');
        Route::get('/presences/{attendance}', [PresenceController::class, 'show'])->name('presences.show');
        Route::get('/presences/{attendance}/presence', [PresenceController::class, 'showPresenceData'])
        ->name('presences.presence');
        // not present data
        Route::get('/presences/{attendance}/not-present', [PresenceController::class, 'notPresent'])->name('presences.not-present');
        Route::post('/presences/{attendance}/not-present', [PresenceController::class, 'notPresent']);
        // present (url untuk menambahkan/mengubah user yang tidak hadir menjadi hadir)
        Route::post('/presences/{attendance}/present', [PresenceController::class, 'presentUser'])->name('presences.present');
        Route::post('/presences/{attendance}/acceptPermission', [PresenceController::class, 'acceptPermission'])->name('presences.acceptPermission');
        // employees permissions

        Route::get('/presences/{attendance}/permissions', [PresenceController::class, 'permissions'])->name('presences.permissions');
    });

    Route::middleware('role:user')->name('home.')->group(function () {
        Route::get('/', [HomeController::class, 'index'])->name('index');
        // desctination after scan qrcode
        Route::post('/absensi/qrcode', [HomeController::class, 'sendEnterPresenceUsingQRCode'])->name('sendEnterPresenceUsingQRCode');
        Route::post('/absensi/qrcode/out', [HomeController::class, 'sendOutPresenceUsingQRCode'])->name('sendOutPresenceUsingQRCode');

        Route::get('/absensi/{attendance}', [HomeController::class, 'show'])->name('show');
        Route::get('/absensi/{attendance}/permission', [HomeController::class, 'permission'])->name('permission');
    });

    Route::delete('/logout', [AuthController::class, 'logout'])->name('auth.logout');
});

Route::middleware('guest')->group(function () {
    // auth
    Route::get('/login', [AuthController::class, 'index'])->name('auth.login');
    Route::post('/login', [AuthController::class, 'authenticate']);
});

Route::get('/export/presences', function () {
    // Ambil data kehadiran dari database
    $presenceData = Presence::with('users')->get(); // Sesuaikan dengan relasi yang ada di model Presence

    // Ekspor data ke file Excel
    return Excel::download(new PresencesExport($presenceData), 'presences.xlsx');
})->name('presences.export');

    
Route::get('/presences/{id}/export', [PresenceController::class, 'export'])->name('presences.export');
Route::get('/presences/export/{attendance}', [PresenceController::class, 'export'])->name('presences.export');

//Perjalanan dinas
Route::get('/perjalanan', [PerjalananController::class, 'index'])->name('perjalanan.index');
Route::resource('/perjalanan', PerjalananController::class)->only(['index', 'create']); // Create form
Route::post('/perjalanan', [PerjalananController::class, 'store'])->name('perjalanan.store'); // Store new perjalanan
Route::get('/perjalanan/{id}/edit', [PerjalananController::class, 'edit'])->name('perjalanan.edit'); // Edit form
Route::put('/perjalanan/{id}', [PerjalananController::class, 'update'])->name('perjalanan.update'); // Update perjalanan
Route::get('/perjalanan/{id}', [PerjalananController::class, 'show'])->name('perjalanan.show');
Route::get('/perjalanan/{id}/download', [PerjalananController::class, 'download'])->name('perjalanan.download');
Route::get('perjalanan/{id}/download-laporan', [PerjalananController::class, 'downloadLaporan'])->name('perjalanan.downloadLaporan');
Route::get('/perjalanan/{id}/upload-laporan', [PerjalananController::class, 'uploadLaporan'])->name('perjalanan.uploadLaporan');
Route::post('/perjalanan/{id}/store-laporan', [PerjalananController::class, 'storeLaporan'])->name('perjalanan.storeLaporan');






