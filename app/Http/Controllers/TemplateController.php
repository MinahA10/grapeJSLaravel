<?php

namespace App\Http\Controllers;

use App\Models\Template;
use Dotlogics\Grapesjs\App\Traits\EditorTrait;
use Illuminate\Http\Request;
use Spatie\Browsershot\Browsershot;
use Barryvdh\DomPDF\Facade\Pdf;
use Symfony\Component\Process\Process;

class TemplateController extends Controller
{
    use EditorTrait;

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|string',
            'name' => 'required|string',
            'is_active' => 'required|boolean'
        ]);

        $template = Template::create($validated);
        return response()->json($template, 201);
    }

    public function editor(Request $request, Template $template)
    {
        return $this->show_gjs_editor($request, $template);
    }

    public function getTemplates()
    {
        $templates = Template::where('is_active', true)->get();
        //return response()->json($templates);

        return view('pages.list', [
            'templates' => $templates,
        ]);
    }

    public function show(Request $request, Template $template)
    {
        $placeholders = [
            '{{ name }}' => 'Minah',
            '{{ email }}' => 'minah@gmail.com',
        ];

        $html = $template->getHtmlAttribute();
        foreach ($placeholders as $key => $value) {
            $html = str_replace($key, e($value), $html);
        }

        return view('pages.show', [
            'template' => $template,
            'html' => $html
        ]);
    }

    public function save(Template $template, Request $request)
    {
        $data = $request->input('project');
        $content = $request->input('content');

        $template->update([
            'type' => 'badge',
            'name' => 'test',
            'is_active' => true,
            'data' => $data,
            'content' => $content
        ]);

        return response()->json(['message' => 'Saved', 'template' => $template], 200);
    }

    public function load(Template $template)
    {
        $data = $template->data ? json_decode($template->data, true) : [];

        return response()->json(['project' => $data]);
    }

    public function getList(): \Illuminate\Http\JsonResponse
    {
        $templates = Template::all();

        return response()->json(['templates' => $templates]);
    }

    public function generatContentPdf(Template $template)
    {
        $content = $template->content;

        $placeholders = [
            '{{firstname}}' => 'ANTETOKOUNMPO',
            '{{lastname}}' => 'Giannis',
            '{{qrcode}}' => 'https://netbyus.com/assets/images/nbu_logo_white.png',
            '{{society}}' => 'Netbyus',
            '{{logo}}' => 'http://localhost:8000/storage/assets/STY6EbAHVKrJ1tntqkgZsJ8KdA6TOIfL6gLyJzYo.png'
        ];

        $html = $content;
        foreach ($placeholders as $key => $value) {
            $html = str_replace($key, e($value), $html);
        }

        $html = preg_replace_callback('/<img[^>]+src=["\'](http:\/\/localhost:8000\/storage\/[^"\']+)["\']/i', function ($matches) {
            $url = $matches[1];
    
            $relativePath = str_replace('http://localhost:8000/storage/', '', $url);
            $filePath = public_path('storage/' . $relativePath);
    
            if (file_exists($filePath)) {
                $mimeType = mime_content_type($filePath);
                $base64 = base64_encode(file_get_contents($filePath));
                return str_replace($url, "data:$mimeType;base64,$base64", $matches[0]);
            }
    
            return $matches[0]; 
        }, $html);

        $path = storage_path("app/public/badge_{$template->id}.pdf");

        Browsershot::html($html)
        ->setOption('width', 300) 
        ->setOption('height', 450) 
        ->margins(0, 0, 0, 0) 
            ->save($path);

        return response()->download($path);
    }
}
