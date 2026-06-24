<?php

use App\Controllers\UsersController;
use App\Controllers\ProductsController;
use App\Controllers\SalesController;
use App\Controllers\ClientsController;
use App\Controllers\ProvidersController;
use App\Controllers\CategoriesController;
use App\Controllers\PurchasesController;
use App\Controllers\InventoryController;
use App\Controllers\ReportsController;
use App\Controllers\SettingsController;
use App\Controllers\PosController;
use App\Middleware\AuthMiddleware;
use App\Middleware\AdminMiddleware;

$router = $app->getRouter();

// Public routes
$router->get('', 'App\Controllers\PagesController@index');
$router->get('dashboard', 'App\Controllers\PagesController@dashboard');
$router->get('login', UsersController::class . '@login');
$router->post('login', UsersController::class . '@login');
$router->get('logout', UsersController::class . '@logout');
$router->get('recover', UsersController::class . '@recover');
$router->post('recover', UsersController::class . '@recover');
$router->get('reset/{token}', UsersController::class . '@reset');
$router->post('reset/{token}', UsersController::class . '@reset');

// Protected routes - require authentication
// Users
$router->get('users', UsersController::class . '@index');
$router->get('users/add', UsersController::class . '@add');
$router->post('users/add', UsersController::class . '@add');
$router->get('users/edit/{id}', UsersController::class . '@edit');
$router->post('users/edit/{id}', UsersController::class . '@edit');
$router->post('users/toggle/{id}', UsersController::class . '@toggle');

// Products
$router->get('products', ProductsController::class . '@index');
$router->get('products/add', ProductsController::class . '@add');
$router->post('products/add', ProductsController::class . '@add');
$router->get('products/edit/{id}', ProductsController::class . '@edit');
$router->post('products/edit/{id}', ProductsController::class . '@edit');
$router->post('products/delete/{id}', ProductsController::class . '@delete');
$router->get('products/barcode/{id}', ProductsController::class . '@barcode');

// Categories
$router->get('categories', CategoriesController::class . '@index');
$router->get('categories/add', CategoriesController::class . '@add');
$router->post('categories/add', CategoriesController::class . '@add');
$router->get('categories/edit/{id}', CategoriesController::class . '@edit');
$router->post('categories/edit/{id}', CategoriesController::class . '@edit');
$router->post('categories/delete/{id}', CategoriesController::class . '@delete');

// Clients
$router->get('clients', ClientsController::class . '@index');
$router->get('clients/add', ClientsController::class . '@add');
$router->post('clients/add', ClientsController::class . '@add');
$router->get('clients/edit/{id}', ClientsController::class . '@edit');
$router->post('clients/edit/{id}', ClientsController::class . '@edit');
$router->post('clients/toggle/{id}', ClientsController::class . '@toggle');

// Providers
$router->get('providers', ProvidersController::class . '@index');
$router->get('providers/add', ProvidersController::class . '@add');
$router->post('providers/add', ProvidersController::class . '@add');
$router->get('providers/edit/{id}', ProvidersController::class . '@edit');
$router->post('providers/edit/{id}', ProvidersController::class . '@edit');
$router->get('providers/history/{id}', ProvidersController::class . '@history');

// Purchases
$router->get('purchases', PurchasesController::class . '@index');
$router->get('purchases/add', PurchasesController::class . '@add');
$router->post('purchases/add', PurchasesController::class . '@add');

// Sales
$router->get('sales', SalesController::class . '@index');
$router->get('pos', PosController::class . '@index');
$router->post('pos/process', PosController::class . '@process');
$router->get('pos/searchProduct', PosController::class . '@searchProduct');
$router->post('pos/save', PosController::class . '@save');
$router->post('pos/printLastReceipt', PosController::class . '@printLastReceipt');
$router->get('pos/getFrequentProducts', PosController::class . '@getFrequentProducts');
$router->get('sales/invoice/{id}', SalesController::class . '@invoice');
$router->get('sales/invoice-pdf/{id}', SalesController::class . '@invoicePdf');

// Inventory
$router->get('inventory', InventoryController::class . '@index');
$router->get('inventory/kardex/{id}', InventoryController::class . '@kardex');

// Reports
$router->get('reports', ReportsController::class . '@index');
$router->post('reports/sales', ReportsController::class . '@sales');
$router->post('reports/purchases', ReportsController::class . '@purchases');
$router->post('reports/inventory', ReportsController::class . '@inventory');

// Settings
$router->get('settings', SettingsController::class . '@index');
$router->post('settings', SettingsController::class . '@update');
