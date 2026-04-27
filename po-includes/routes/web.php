<?php

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

//buat ngeprank hackel
Route::redirect('admin/sql', 'https://www.youtube.com/watch?v=3PMLwe_C-F0');
Route::redirect('po-content/filemanager', 'https://www.facebook.com');
Route::redirect('config/database.php', 'https://www.facebook.com');
Route::redirect('api-keys.json', 'https://www.facebook.com');
Route::redirect('po-content/shell.php', 'https://www.facebook.com');
Route::redirect('po-content/konten.php', 'https://www.facebook.com');

//frontend
Route::match(['get', 'post'], '/', 'HomeController@index');
Route::get('home', 'HomeController@index')->name('home');
Route::get('sekolah', 'SekolahController@index')->name('sekolah');
Route::get('/sekolah/data', 'SekolahController@data')->name('sekolah.data');
Route::get('/sekolah/detail/{id}', 'SekolahController@detail')->name('sekolah.detail');
Route::get('pages/{seotitle}', 'PagesController@index');
Route::get('category/{seotitle}', 'CategoryController@index');
Route::get('tag/{seotitle}', 'TagController@index');
Route::get('detailpost/{seotitle}', 'PostController@index');
Route::get('post/{seotitle}', 'PostController@index');
Route::get('post/{seotitle}-{id}', 'PostController@index');
Route::get('article/{year}/{month}/{day}/{seotitle}', 'PostController@article');
//Route::post('comment/send/{seotitle}', 'PostController@send');
Route::get('album/{seotitle}', 'GalleryController@index');
Route::get('404', 'HomeController@error404')->name('404');
Route::get('contact', 'ContactController@index');
Route::post('contact/send', 'ContactController@send');
Route::get('tracking', 'ContactController@tracking');
Route::get('menu', 'MenuController@index');
//Route::post('subscribe', 'HomeController@subscribe');

//data kelurahan
Route::get('get-kelurahan/{id}', function ($id) {
	return \App\Wilayah::where('parent_id', $id)
		->where('child_id', '!=', 0)
		->pluck('nama_wilayah', 'id');
});

if(getSetting('member_registration') == 'Y') {
	Auth::routes(['verify' => true]);
} else {
	Auth::routes(['register' => false]);
}

//backend
Route::group(['middleware' => ['auth']], function () {
	Route::get('dashboard', 'Backend\BackendController@index');
	// Route::get('dashboard/analytics', 'Backend\BackendController@analytics');
	Route::get('forbidden', 'Backend\BackendController@forbidden');

	//user akun
	Route::get('dashboard/users/index', 'Backend\UsersController@index');
	Route::get('dashboard/users/table', 'Backend\UsersController@getIndex');
	Route::get('dashboard/users/data', 'Backend\UsersController@anyData');
	Route::get('dashboard/users/get-user','Backend\UsersController@getUser');
	Route::get('dashboard/users/get-user-not-me','Backend\UsersController@getUserNotMe');
	Route::post('dashboard/users/deleteall', 'Backend\UsersController@deleteAll');
	Route::resource('dashboard/users', 'Backend\UsersController');

	//banner
	Route::get('dashboard/banner/index', 'Backend\BannerController@index');
	Route::get('dashboard/banner/table','Backend\BannerController@getIndex');
	Route::get('dashboard/banner/data','Backend\BannerController@anyData');
	Route::post('dashboard/banner/deleteall', 'Backend\BannerController@deleteAll');
	Route::resource('dashboard/banner', 'Backend\BannerController');

	//infografis
	Route::get('dashboard/infografis/index', 'Backend\InfografisController@index');
	Route::get('dashboard/infografis/table','Backend\InfografisController@getIndex');
	Route::get('dashboard/infografis/data','Backend\InfografisController@anyData');
	Route::post('dashboard/infografis/deleteall', 'Backend\InfografisController@deleteAll');
	Route::resource('dashboard/infografis', 'Backend\InfografisController');

	//role akses
	Route::get('dashboard/roles/index','Backend\RolesController@index');
	Route::get('dashboard/roles/table','Backend\RolesController@getIndex');
	Route::get('dashboard/roles/data','Backend\RolesController@anyData');
	Route::post('dashboard/roles/deleteall', 'Backend\RolesController@deleteAll');
	Route::resource('dashboard/roles', 'Backend\RolesController');

	//permissions
	Route::get('dashboard/permissions/index','Backend\PermissionsController@index');
	Route::get('dashboard/permissions/table','Backend\PermissionsController@getIndex');
	Route::get('dashboard/permissions/data','Backend\PermissionsController@anyData');
	Route::post('dashboard/permissions/deleteall', 'Backend\PermissionsController@deleteAll');
	Route::resource('dashboard/permissions', 'Backend\PermissionsController');

	//setting atau pengaturan
	Route::get('dashboard/settings/index','Backend\SettingsController@getIndex');
	Route::get('dashboard/settings/table','Backend\SettingsController@getIndex');
	Route::get('dashboard/settings/data','Backend\SettingsController@anyData');
	Route::get('dashboard/settings/sitemap','Backend\SettingsController@sitemap');
	Route::get('dashboard/settings/backup','Backend\SettingsController@backup');
	Route::post('dashboard/settings/deleteall', 'Backend\SettingsController@deleteAll');
	Route::resource('dashboard/settings', 'Backend\SettingsController');

	//menu subscribe
	Route::get('dashboard/subscribes/index','Backend\SubscribeController@getIndex');
	Route::get('dashboard/subscribes/table','Backend\SubscribeController@getIndex');
	Route::get('dashboard/subscribes/data','Backend\SubscribeController@anyData');
	Route::post('dashboard/subscribes/deleteall', 'Backend\SubscribeController@deleteAll');
	Route::resource('dashboard/subscribes', 'Backend\SubscribeController');

	//post
	Route::get('dashboard/posts/index','Backend\PostController@index');
	Route::get('dashboard/posts/table','Backend\PostController@getIndex');
	Route::get('dashboard/posts/data','Backend\PostController@anyData');
	Route::get('dashboard/posts/subscribes/{id}','Backend\PostController@subscribes');
	Route::post('dashboard/posts/deleteall', 'Backend\PostController@deleteAll');
	Route::post('dashboard/posts/create-gallery', 'Backend\PostController@createGallery');
	Route::post('dashboard/posts/delete-gallery', 'Backend\PostController@deleteGallery');
	Route::resource('dashboard/posts', 'Backend\PostController');

	//category
	Route::get('dashboard/categories/index','Backend\CategoryController@index');
	Route::get('dashboard/categories/table','Backend\CategoryController@getIndex');
	Route::get('dashboard/categories/data','Backend\CategoryController@anyData');
	Route::post('dashboard/categories/deleteall', 'Backend\CategoryController@deleteAll');
	Route::resource('dashboard/categories', 'Backend\CategoryController');

	//tags post
	Route::get('dashboard/tags/index','Backend\TagsController@index');
	Route::get('dashboard/tags/table','Backend\TagsController@getIndex');
	Route::get('dashboard/tags/data','Backend\TagsController@anyData');
	Route::get('dashboard/tags/get-tag','Backend\TagsController@getTag');
	Route::post('dashboard/tags/deleteall', 'Backend\TagsController@deleteAll');
	Route::resource('dashboard/tags', 'Backend\TagsController');

	//Running Text
	Route::get('dashboard/runningtext/index','Backend\RunningtextController@index');
	Route::get('dashboard/runningtext/table','Backend\RunningtextController@getIndex');
	Route::get('dashboard/runningtext/data','Backend\RunningtextController@anyData');
	Route::post('dashboard/runningtext/deleteall', 'Backend\RunningtextController@deleteAll');
	Route::resource('dashboard/runningtext', 'Backend\RunningtextController');
	
	//laporan
	// Route::get('dashboard/laporanopd/index','Backend\LaporanopdController@index');
	// Route::get('dashboard/laporanopd/table','Backend\LaporanopdController@getIndex');
	// Route::get('dashboard/laporanopd/data','Backend\LaporanopdController@anyData');
	// Route::resource('dashboard/laporanopd', 'Backend\LaporanopdController');

	//pejabatopd
	// Route::get('dashboard/pejabatopd/index','Backend\PejabatOpdController@index');
	// Route::get('dashboard/pejabatopd/table','Backend\PejabatOpdController@getIndex');
	// Route::get('dashboard/pejabatopd/show/{id}','Backend\PejabatOpdController@show');
	// Route::resource('dashboard/pejabatopd', 'Backend\PejabatOpdController');

	//viewpage
	Route::get('dashboard/viewpage/index','Backend\ViewPageController@index');
	Route::get('dashboard/viewpage/table','Backend\ViewPageController@getIndex');
	Route::get('dashboard/viewpage/data','Backend\ViewPageController@anyData');
	Route::get('dashboard/viewpage/cetak','Backend\ViewPageController@cetak_viewpage');
	Route::get('dashboard/viewpage/cetak_aksi','Backend\ViewPageController@cetak_viewpage_aksi');
	//Route::get('dashboard/viewpage/cetaktes','Backend\ViewPageController@tescetak');
	Route::post('dashboard/viewpage/deleteall','Backend\ViewPageController@deleteAll');
	Route::resource('dashboard/viewpage','Backend\ViewPageController');

	//comment
	Route::get('dashboard/comments/index','Backend\CommentController@index');
	Route::get('dashboard/comments/table','Backend\CommentController@getIndex');
	Route::get('dashboard/comments/data','Backend\CommentController@anyData');
	Route::get('dashboard/comments/reply/{id}','Backend\CommentController@reply');
	Route::post('dashboard/comments/post-reply', 'Backend\CommentController@postReply');
	Route::post('dashboard/comments/deleteall', 'Backend\CommentController@deleteAll');
	Route::resource('dashboard/comments', 'Backend\CommentController');

	//pages
	Route::get('dashboard/pages/index','Backend\PagesController@index');
	Route::get('dashboard/pages/table','Backend\PagesController@getIndex');
	Route::get('dashboard/pages/data','Backend\PagesController@anyData');
	Route::post('dashboard/pages/deleteall', 'Backend\PagesController@deleteAll');
	Route::resource('dashboard/pages', 'Backend\PagesController');

	//themes
	Route::get('dashboard/themes/index','Backend\ThemeController@index');
	Route::get('dashboard/themes/table','Backend\ThemeController@getIndex');
	Route::get('dashboard/themes/data','Backend\ThemeController@anyData');
	Route::get('dashboard/themes/active/{id}','Backend\ThemeController@active');
	// Route::get('dashboard/themes/install','Backend\ThemeController@install');
	// Route::post('dashboard/themes/process-install','Backend\ThemeController@processInstall');
	// Route::post('dashboard/themes/deleteall', 'Backend\ThemeController@deleteAll');
	Route::resource('dashboard/themes', 'Backend\ThemeController');

	//menumanager
	Route::get('dashboard/menu-manager/index','Backend\MenuController@index');
	Route::get('dashboard/menu-manager/table','Backend\\MenuController@getIndex');
	Route::get('dashboard/menu-manager/data','Backend\MenuController@anyData');
	Route::get('dashboard/menu-manager/menusort','Backend\\MenuController@menusort');
	Route::post('dashboard/menu-manager/menusort', 'Backend\\MenuController@menusort');
	Route::post('dashboard/menu-manager/deleteall', 'Backend\MenuController@deleteAll');
	Route::resource('dashboard/menu-manager', 'Backend\MenuController');

	//components
	Route::get('dashboard/components/index','Backend\ComponentController@index');
	Route::get('dashboard/components/table','Backend\ComponentController@getIndex');
	Route::get('dashboard/components/data','Backend\ComponentController@anyData');
	// Route::get('dashboard/components/install','Backend\ComponentController@install');
	// Route::post('dashboard/components/process-install','Backend\ComponentController@processInstall');
	// Route::post('dashboard/components/deleteall', 'Backend\ComponentController@deleteAll');
	Route::resource('dashboard/components', 'Backend\ComponentController');

	//gallery
	Route::get('dashboard/gallerys/index','Backend\GalleryController@index');
	Route::get('dashboard/gallerys/table','Backend\GalleryController@getIndex');
	Route::get('dashboard/gallerys/data','Backend\GalleryController@anyData');
	Route::post('dashboard/gallerys/deleteall', 'Backend\GalleryController@deleteAll');
	Route::resource('dashboard/gallerys', 'Backend\GalleryController');

	//album
	Route::get('dashboard/albums/index','Backend\AlbumController@index');
	Route::get('dashboard/albums/table','Backend\AlbumController@getIndex');
	Route::get('dashboard/albums/data','Backend\AlbumController@anyData');
	Route::get('dashboard/albums/get-album','Backend\AlbumController@getAlbum');
	Route::post('dashboard/albums/deleteall', 'Backend\AlbumController@deleteAll');
	Route::resource('dashboard/albums', 'Backend\AlbumController');

	//contacts
	Route::get('dashboard/contacts/index','Backend\ContactController@index');
	Route::get('dashboard/contacts/table','Backend\ContactController@getIndex');
	Route::get('dashboard/contacts/data','Backend\ContactController@anyData');
	Route::get('dashboard/contacts/reply/{id}','Backend\ContactController@reply');
	Route::post('dashboard/contacts/post-reply', 'Backend\ContactController@postReply');
	Route::post('dashboard/contacts/deleteall', 'Backend\ContactController@deleteAll');
	Route::resource('dashboard/contacts', 'Backend\ContactController');
	
	//menu sekolah
	Route::get('dashboard/sekolahs/index','Backend\SekolahController@getIndex');
	Route::get('dashboard/sekolahs/table','Backend\SekolahController@getIndex');
	Route::get('dashboard/sekolahs/data','Backend\SekolahController@anyData');
	Route::get('dashboard/sekolahs/get-sekolahs','Backend\SekolahController@getSekolah');
	Route::post('dashboard/sekolahs/deleteall', 'Backend\SekolahController@deleteAll');
	Route::resource('dashboard/sekolahs', 'Backend\SekolahController');

	//menu sppg
	Route::get('dashboard/sppgs/index','Backend\SppgController@getIndex');
	Route::get('dashboard/sppgs/table','Backend\SppgController@getIndex');
	Route::get('dashboard/sppgs/data','Backend\SppgController@anyData');
	Route::post('dashboard/sppgs/deleteall', 'Backend\SppgController@deleteAll');
	Route::resource('dashboard/sppgs', 'Backend\SppgController');

	//menu harian
	Route::get('dashboard/menuharians/index','Backend\MenuharianController@getIndex');
	Route::get('dashboard/menuharians/table','Backend\MenuharianController@getIndex');
	Route::get('dashboard/menuharians/data','Backend\MenuharianController@anyData');
	Route::post('dashboard/menuharians/deleteall', 'Backend\MenuharianController@deleteAll');
	Route::resource('dashboard/menuharians', 'Backend\MenuharianController');

	//delivery
	Route::get('dashboard/deliverys/index','Backend\DeliveryController@getIndex');
	Route::get('dashboard/deliverys/table','Backend\DeliveryController@getIndex');
	Route::get('dashboard/deliverys/data','Backend\DeliveryController@anyData');
	Route::post('dashboard/deliverys/deleteall', 'Backend\DeliveryController@deleteAll');
	Route::resource('dashboard/deliverys', 'Backend\DeliveryController');
	Route::get('dashboard/deliverys/get-sekolah-by-sppg/{id}', 'Backend\DeliveryController@getSekolahBySppg');

	//aduan
	Route::get('dashboard/aduans/index','Backend\AduanController@getIndex');
	Route::get('dashboard/aduans/table','Backend\AduanController@getIndex');
	Route::get('dashboard/aduans/data','Backend\AduanController@anyData');
	Route::post('dashboard/aduans/deleteall', 'Backend\AduanController@deleteAll');
	Route::resource('dashboard/aduans', 'Backend\AduanController');
	Route::put('dashboard/aduans/{id}/proses', 'Backend\AduanController@prosesAduan');
	Route::put('dashboard/aduans/{id}/respon-awal', 'Backend\AduanController@responAduan');
	Route::put('dashboard/aduans/{id}/respon-akhir', 'Backend\AduanController@responAkhir');
});