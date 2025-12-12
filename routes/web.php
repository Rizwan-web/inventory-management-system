<?php

use App\Http\Controllers\{
    DashboardController,
    CategoryController,
    ReportController,
    ProductController,
    MemberController,
    ExpenseController,
    PurchaseController,
    PurchaseDetailController,
    SalesController,
    SalesDetailController,
    SettingController,
    SupplierController,
    UserController,
};
use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return redirect()->route('login');
});

Route::group(['middleware' => 'auth'], function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::group(['middleware' => 'level:1'], function () {
        Route::get('/category/data', [CategoryController::class, 'data'])->name('category.data');
        Route::resource('/category', CategoryController::class);

        Route::get('/product/data', [ProductController::class, 'data'])->name('product.data');
        Route::post('/product/delete-selected', [ProductController::class, 'deleteSelected'])->name('product.delete_selected');
        Route::post('/product/cetak-barcode', [ProductController::class, 'cetakBarcode'])->name('product.cetak_barcode');
        Route::resource('/product', ProductController::class);

        Route::get('/member/data', [MemberController::class, 'data'])->name('member.data');
        Route::post('/member/cetak-member', [MemberController::class, 'cetakMember'])->name('member.cetak_member');
        Route::resource('/member', MemberController::class);

        Route::get('/supplier/data', [SupplierController::class, 'data'])->name('supplier.data');
        Route::resource('/supplier', SupplierController::class);

        Route::get('/expense/data', [ExpenseController::class, 'data'])->name('expense.data');
        Route::resource('/expense', ExpenseController::class);

        Route::get('/purchase/data', [PurchaseController::class, 'data'])->name('purchase.data');
        Route::get('/purchase/{id}/create', [PurchaseController::class, 'create'])->name('purchase.create');
        Route::resource('/purchase', PurchaseController::class)
            ->except('create');

        Route::get('/purchase_detail/{id}/data', [PurchaseDetailController::class, 'data'])->name('purchase_detail.data');
        Route::get('/purchase_detail/loadform/{discount}/{total}', [PurchaseDetailController::class, 'loadForm'])->name('purchase_detail.load_form');
        Route::resource('/purchase_detail', PurchaseDetailController::class)
            ->except('create', 'show', 'edit');

        Route::get('/sales/data', [SalesController::class, 'data'])->name('sales.data');
        Route::get('/sales', [SalesController::class, 'index'])->name('sales.index');
        Route::get('/sales/{id}', [SalesController::class, 'show'])->name('sales.show');
        Route::delete('/sales/{id}', [SalesController::class, 'destroy'])->name('sales.destroy');
    });

    Route::group(['middleware' => 'level:1,2'], function () {
        Route::get('/transaksi/baru', [SalesController::class, 'create'])->name('transaksi.baru');
        Route::post('/transaksi/simpan', [SalesController::class, 'store'])->name('transaksi.simpan');
        Route::get('/transaksi/selesai', [SalesController::class, 'selesai'])->name('transaksi.selesai');
        Route::get('/transaksi/batal', [SalesController::class, 'cancel'])->name('transaksi.batal');
        Route::get('/transaksi/dc-numbers', [SalesController::class, 'getDcNumbers'])->name('transaksi.dc-numbers');
    Route::get('/transaksi/dc-sales', [SalesController::class, 'getSalesByDc'])->name('transaksi.dc-sales');
    Route::get('/transaksi/all-sales', [SalesController::class, 'getAllSales'])->name('transaksi.all-sales');
    Route::get('/transaksi/members', [SalesController::class, 'getMembers'])->name('transaksi.members');
    Route::get('/transaksi/member-sales', [SalesController::class, 'getSalesByMember'])->name('transaksi.member-sales');
        Route::get('/transaksi/nota-kecil', [SalesController::class, 'notaKecil'])->name('transaksi.nota_kecil');
        Route::get('/transaksi/nota-besar', [SalesController::class, 'notaBesar'])->name('transaksi.nota_besar');
        Route::post('/transaksi/generate-invoice', [SalesController::class, 'generateInvoice'])->name('transaksi.generate-invoice');
        Route::get('/transaksi/download-invoice', [SalesController::class, 'downloadGeneratedInvoice'])->name('transaksi.download-invoice');

        Route::get('/transaksi/{id}/data', [SalesDetailController::class, 'data'])->name('transaksi.data');
        Route::get('/transaksi/loadform/{discount}/{total}/{diterima}', [SalesDetailController::class, 'loadForm'])->name('transaksi.load_form');
        Route::resource('/transaksi', SalesDetailController::class)
            ->except('create', 'show', 'edit');
    });

    Route::group(['middleware' => 'level:1'], function () {
        Route::get('/report', [ReportController::class, 'index'])->name('report.index');
        Route::get('/report/data/{awal}/{akhir}', [ReportController::class, 'data'])->name('report.data');
        Route::get('/report/pdf/{awal}/{akhir}', [ReportController::class, 'exportPDF'])->name('report.export_pdf');

        Route::get('/user/data', [UserController::class, 'data'])->name('user.data');
        Route::resource('/user', UserController::class);

        Route::get('/setting', [SettingController::class, 'index'])->name('setting.index');
        Route::get('/setting/first', [SettingController::class, 'show'])->name('setting.show');
        Route::post('/setting', [SettingController::class, 'update'])->name('setting.update');
    });
 
    Route::group(['middleware' => 'level:1,2'], function () {
        Route::get('/profil', [UserController::class, 'profil'])->name('user.profil');
        Route::post('/profil', [UserController::class, 'updateProfil'])->name('user.update_profil');
    });
});