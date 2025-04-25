<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>List template</title>
</head>
<body>
<div class="container mx-auto px-4 py-6">
    <h1 class="text-2xl font-bold mb-6">Liste des Templates</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach ($templates as $template)
            <div class="border rounded-lg shadow p-4 bg-white">
                @php
                    $gjs = $template->gjs_data;
                    $html = $gjs['html'] ?? '';
                    $css = $gjs['css'] ?? '';
                @endphp

                <iframe 
                    srcdoc="<style>{{ $css }}</style>{{ $html }}" 
                    class="w-full h-64 border mb-4" 
                    sandbox="allow-same-origin"
                ></iframe>

                <div class="text-center font-semibold">
                    {{ $template->name }}
                </div>
                <div class="flex justify-center space-x-2">
                    <a 
                        href="{{ route('template.show', ['template' => $template->id]) }}" 
                        class="bg-gray-200 hover:bg-gray-300 text-sm px-4 py-1 rounded"
                        target="_blank"
                    >
                        Preview
                    </a>

                    <a 
                        href="{{ url('/pages/' . $template->id). '/editor' }}" 
                        class="bg-blue-500 hover:bg-blue-600 text-white text-sm px-4 py-1 rounded"
                    >
                        Edit
                    </a>
                </div>
            </div>
        @endforeach
    </div>
</div>

</body>
</html>
