<?php $currentPage = 'dashboard'; require APPROOT . '/views/inc/header.php'; ?>

<!-- Dashboard de Botones Grandes y Elegantes -->
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-900 mb-text-900">¿Qué deseas hacer hoy?</h2>
    <p class="text-sm text-gray-600 mb-text-500 mt-1">Selecciona una de las opciones del módulo del sistema.</p>
</div>

<!-- Rejilla de Botones Grandes y Elegantes -->
<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
    
    <!-- Dashboard -->
    <a href="<?= URLROOT; ?>/pages/dashboard" class="group bg-white p-6 rounded-2xl border border-gray-200 shadow-sm hover:shadow-xl hover:border-indigo-500 hover:-translate-y-1 transition-all duration-300 flex flex-col items-center text-center justify-center min-h-[160px]">
        <div class="p-4 bg-indigo-50 rounded-xl text-indigo-600 transition-colors duration-300 mb-4 group-hover-bg-indigo-600 group-hover-text-white">
            <i data-lucide="gauge" class="w-8 h-8"></i>
        </div>
        <span class="text-base font-semibold text-gray-800 group-hover-text-indigo-600 transition-colors">Dashboard</span>
    </a>
    
    <!-- Usuarios -->
    <a href="<?= URLROOT; ?>/users" class="group bg-white p-6 rounded-2xl border border-gray-200 shadow-sm hover:shadow-xl hover:border-blue-500 hover:-translate-y-1 transition-all duration-300 flex flex-col items-center text-center justify-center min-h-[160px]">
        <div class="p-4 bg-blue-50 rounded-xl text-blue-600 transition-colors duration-300 mb-4 group-hover-bg-blue-600 group-hover-text-white">
            <i data-lucide="users" class="w-8 h-8"></i>
        </div>
        <span class="text-base font-semibold text-gray-800 group-hover-text-blue-600 transition-colors">Usuarios</span>
    </a>
    
    <!-- Categorías -->
    <a href="<?= URLROOT; ?>/categories" class="group bg-white p-6 rounded-2xl border border-gray-200 shadow-sm hover:shadow-xl hover:border-emerald-500 hover:-translate-y-1 transition-all duration-300 flex flex-col items-center text-center justify-center min-h-[160px]">
        <div class="p-4 bg-emerald-50 rounded-xl text-emerald-600 transition-colors duration-300 mb-4 group-hover-bg-emerald-600 group-hover-text-white">
            <i data-lucide="tags" class="w-8 h-8"></i>
        </div>
        <span class="text-base font-semibold text-gray-800 group-hover-text-emerald-600 transition-colors">Categorías</span>
    </a>
    
    <!-- Productos -->
    <a href="<?= URLROOT; ?>/products" class="group bg-white p-6 rounded-2xl border border-gray-200 shadow-sm hover:shadow-xl hover:border-amber-500 hover:-translate-y-1 transition-all duration-300 flex flex-col items-center text-center justify-center min-h-[160px]">
        <div class="p-4 bg-amber-50 rounded-xl text-amber-600 transition-colors duration-300 mb-4 group-hover-bg-amber-600 group-hover-text-white">
            <i data-lucide="box" class="w-8 h-8"></i>
        </div>
        <span class="text-base font-semibold text-gray-800 group-hover-text-amber-600 transition-colors">Productos</span>
    </a>
    
    <!-- Proveedores -->
    <a href="<?= URLROOT; ?>/providers" class="group bg-white p-6 rounded-2xl border border-gray-200 shadow-sm hover:shadow-xl hover:border-violet-500 hover:-translate-y-1 transition-all duration-300 flex flex-col items-center text-center justify-center min-h-[160px]">
        <div class="p-4 bg-violet-50 rounded-xl text-violet-600 transition-colors duration-300 mb-4 group-hover-bg-violet-600 group-hover-text-white">
            <i data-lucide="truck" class="w-8 h-8"></i>
        </div>
        <span class="text-base font-semibold text-gray-800 group-hover-text-violet-600 transition-colors">Proveedores</span>
    </a>
    
    <!-- Clientes -->
    <a href="<?= URLROOT; ?>/clients" class="group bg-white p-6 rounded-2xl border border-gray-200 shadow-sm hover:shadow-xl hover:border-sky-500 hover:-translate-y-1 transition-all duration-300 flex flex-col items-center text-center justify-center min-h-[160px]">
        <div class="p-4 bg-sky-50 rounded-xl text-sky-600 transition-colors duration-300 mb-4 group-hover-bg-sky-600 group-hover-text-white">
            <i data-lucide="user-check" class="w-8 h-8"></i>
        </div>
        <span class="text-base font-semibold text-gray-800 group-hover-text-sky-600 transition-colors">Clientes</span>
    </a>
    
    <!-- Compras -->
    <a href="<?= URLROOT; ?>/purchases" class="group bg-white p-6 rounded-2xl border border-gray-200 shadow-sm hover:shadow-xl hover:border-rose-500 hover:-translate-y-1 transition-all duration-300 flex flex-col items-center text-center justify-center min-h-[160px]">
        <div class="p-4 bg-rose-50 rounded-xl text-rose-600 transition-colors duration-300 mb-4 group-hover-bg-rose-600 group-hover-text-white">
            <i data-lucide="shopping-bag" class="w-8 h-8"></i>
        </div>
        <span class="text-base font-semibold text-gray-800 group-hover-text-rose-600 transition-colors">Compras</span>
    </a>
    
    <!-- Inventario -->
    <a href="<?= URLROOT; ?>/inventories" class="group bg-white p-6 rounded-2xl border border-gray-200 shadow-sm hover:shadow-xl hover:border-orange-500 hover:-translate-y-1 transition-all duration-300 flex flex-col items-center text-center justify-center min-h-[160px]">
        <div class="p-4 bg-orange-50 rounded-xl text-orange-600 transition-colors duration-300 mb-4 group-hover-bg-orange-600 group-hover-text-white">
            <i data-lucide="clipboard-list" class="w-8 h-8"></i>
        </div>
        <span class="text-base font-semibold text-gray-800 group-hover-text-orange-600 transition-colors">Inventario</span>
    </a>
    
    <!-- Punto de Venta -->
    <a href="<?= URLROOT; ?>/pos" class="group bg-white p-6 rounded-2xl border border-gray-200 shadow-sm hover:shadow-xl hover:border-cyan-500 hover:-translate-y-1 transition-all duration-300 flex flex-col items-center text-center justify-center min-h-[160px] md:col-span-1 lg:col-span-1">
        <div class="p-4 bg-cyan-50 rounded-xl text-cyan-600 transition-colors duration-300 mb-4 group-hover-bg-cyan-600 group-hover-text-white">
            <i data-lucide="monitor" class="w-8 h-8"></i>
        </div>
        <span class="text-base font-semibold text-gray-800 group-hover-text-cyan-600 transition-colors">Punto de Venta</span>
    </a>
    
    <!-- Ventas -->
    <a href="<?= URLROOT; ?>/sales" class="group bg-white p-6 rounded-2xl border border-gray-200 shadow-sm hover:shadow-xl hover:border-teal-500 hover:-translate-y-1 transition-all duration-300 flex flex-col items-center text-center justify-center min-h-[160px]">
        <div class="p-4 bg-teal-50 rounded-xl text-teal-600 transition-colors duration-300 mb-4 group-hover-bg-teal-600 group-hover-text-white">
            <i data-lucide="circle-dollar-sign" class="w-8 h-8"></i>
        </div>
        <span class="text-base font-semibold text-gray-800 group-hover-text-teal-600 transition-colors">Ventas</span>
    </a>
    
    <!-- Reportes -->
    <a href="<?= URLROOT; ?>/reports" class="group bg-white p-6 rounded-2xl border border-gray-200 shadow-sm hover:shadow-xl hover:border-purple-500 hover:-translate-y-1 transition-all duration-300 flex flex-col items-center text-center justify-center min-h-[160px]">
        <div class="p-4 bg-purple-50 rounded-xl text-purple-600 transition-colors duration-300 mb-4 group-hover-bg-purple-600 group-hover-text-white">
            <i data-lucide="bar-chart-3" class="w-8 h-8"></i>
        </div>
        <span class="text-base font-semibold text-gray-800 group-hover-text-purple-600 transition-colors">Reportes</span>
    </a>
    
    <!-- Configuración -->
    <a href="<?= URLROOT; ?>/settings" class="group bg-white p-6 rounded-2xl border border-gray-200 shadow-sm hover:shadow-xl hover:border-gray-500 hover:-translate-y-1 transition-all duration-300 flex flex-col items-center text-center justify-center min-h-[160px]">
        <div class="p-4 bg-gray-100 rounded-xl text-gray-600 transition-colors duration-300 mb-4 group-hover-bg-gray-700 group-hover-text-white">
            <i data-lucide="settings" class="w-8 h-8"></i>
        </div>
        <span class="text-base font-semibold text-gray-800 group-hover-text-gray-700 transition-colors">Configuración</span>
    </a>
    
</div>

<?php require APPROOT . '/views/inc/footer.php'; ?>