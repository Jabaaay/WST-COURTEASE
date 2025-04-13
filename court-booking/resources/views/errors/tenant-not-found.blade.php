<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Tenant Not Found</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
            <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
                <div class="text-center">
                    <h2 class="text-2xl font-bold text-red-600 mb-4">Tenant Not Found</h2>
                    <p class="text-gray-600 mb-4">The domain <strong>{{ $domain }}</strong> is not registered or not yet approved.</p>
                    <p class="text-gray-600">Please contact the administrator if you believe this is an error.</p>
                </div>
            </div>
        </div>
    </body>
</html> 