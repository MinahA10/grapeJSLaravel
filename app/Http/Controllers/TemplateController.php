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

    function mjmlArrayToString(array $element): string
    {
        $type = $element['type'] ?? ($element['tagName'] ?? 'div');

        $attributes = '';
        if (isset($element['attributes'])) {
            foreach ($element['attributes'] as $key => $value) {
                $attributes .= " {$key}=\"{$value}\"";
            }
        }

        $content = '';
        if (isset($element['components'])) {
            foreach ($element['components'] as $child) {
                $content .= $this->mjmlArrayToString($child);
            }
        }

        $void = $element['void'] ?? false;
        if ($void || in_array($type, ['mj-spacer', 'mj-image', 'mj-divider'])) {
            return "<{$type}{$attributes} />";
        }

        return "<{$type}{$attributes}>{$content}</{$type}>";
    }

    public function generatePdf(Template $template)
    {
        $data = $template->data;
        $replacedVariable = $data; // Initialiser avec les données d'origine

        $placeholders = [
            '{{firstname}}' => 'ANTETOKOUNMPO',
            '{{lastname}}' => 'Giannis',
            '{{qrcode}}' => 'http://localhost:8000/storage/assets/1i6eNfVjenbiuN6UAxchuIZcrKEGqyPnpnrrUmj9.png'
        ];

        // Effectuer tous les remplacements dans la variable
        foreach ($placeholders as $key => $value) {
            $replacedVariable = str_replace($key, e($value), $replacedVariable);
        }

        $dataToArray = json_decode($replacedVariable, true);

        // Vérifier si le JSON est valide et contient les éléments attendus
        if (!$dataToArray || !isset($dataToArray['pages'][0]['frames'][0]['component']['components'][0])) {
            throw new \Exception("Format de données invalide ou incomplet");
        }

        $mjmlRoot = $dataToArray['pages'][0]['frames'][0]['component']['components'][0];

        $mjmlString = $this->mjmlArrayToString($mjmlRoot);

        $mjmlPath = storage_path("app/template_{$template->id}.mjml");
        file_put_contents($mjmlPath, $mjmlString);

        $htmlPath = storage_path("app/template_{$template->id}.html");
        $process = new Process(['mjml', $mjmlPath, '-o', $htmlPath]);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new \Exception("Erreur lors de la conversion MJML: " . $process->getErrorOutput());
        }

        // Vérifier que le fichier HTML existe
        if (!file_exists($htmlPath)) {
            throw new \Exception("Le fichier HTML n'a pas été généré");
        }

        $html = file_get_contents($htmlPath);

        $pdf = Pdf::loadHTML($html);

        // Nettoyer les fichiers temporaires
        @unlink($mjmlPath);
        @unlink($htmlPath);

        return $pdf->download("badge_{$template->id}.pdf");
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
            ->save($path);

        return response()->download($path);
    }
}
