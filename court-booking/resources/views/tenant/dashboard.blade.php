<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Tenant Dashboard</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased">
        <div class="min-h-screen bg-gray-100">
            <nav class="bg-white shadow-sm">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex">
                            <div class="flex-shrink-0 flex items-center">
                                <h1 class="text-xl font-bold">Tenant Dashboard</h1>
                            </div>
                        </div>
                        <div class="flex items-center">
                            <form method="POST" action="{{ route('tenant.logout') }}">
                                @csrf
                                <button type="submit" class="text-gray-600 hover:text-gray-900">Logout</button>
                            </form>
                        </div>
                    </div>
                </div>
            </nav>

            <main>
                <div class="py-12">
                    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6 bg-white border-b border-gray-200">
                                <h2 class="text-2xl font-bold mb-4">Welcome, {{ session('tenant')->name }}!</h2>
                                <p>You are logged in as a tenant.</p>
                                <div class="mt-4">
                                    <h3 class="text-lg font-semibold">Your Information:</h3>
                                    <ul class="mt-2">
                                        <li><strong>Domain:</strong> {{ session('tenant')->domain }}</li>
                                        <li><strong>Email:</strong> {{ session('tenant')->email }}</li>
                                        <li><strong>Database:</strong> {{ session('tenant')->database_name }}</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </body>
</html> 