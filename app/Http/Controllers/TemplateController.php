<?php

namespace App\Http\Controllers;

use App\Mail\TemplateEmailMail;
use App\Models\Template;
use App\Services\TemplateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Browsershot\Browsershot;
use Barryvdh\DomPDF\Facade\Pdf;
use Symfony\Component\Process\Process;
use Illuminate\Support\Facades\Mail;
use Spatie\Mjml\Mjml;

class TemplateController extends Controller
{

    protected $templateService;

    public function __construct(TemplateService $templateService)
    {
        $this->templateService = $templateService;
    }

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

    public function getTemplates()
    {
        $templates = Template::where('is_active', true)->get();

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

    public function save(Template $template,Request $request): \Illuminate\Http\JsonResponse
    {
        $data = $request->input('project');
        $content = $request->input('content');

        try{
            DB::beginTransaction();

            $template->update([
                'type' => 'email',
                //'name' => 'test',
                'is_active' => true,
                'gjs_data' => $data,
                'contents' => $content
            ]);

            DB::commit();

            return response()->json(['message' => 'Saved']);
        }catch (\Exception $e){
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()]);
        }
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

    public function generateBadge(Template $template)
    {
        $content = $template->contents;

        //$data = $template->data ? json_decode($template->data, true) : [];

        $placeholders = [
            '{{firstname}}' => 'ANTETOKOUNMPO',
            '{{lastname}}' => 'Giannis',
            '{{qrcode}}' => 'https://netbyus.com/assets/images/nbu_logo_white.png',
            '{{society}}' => 'NETBYUS',
            '{{logo}}' => 'http://localhost:8000/storage/assets/STY6EbAHVKrJ1tntqkgZsJ8KdA6TOIfL6gLyJzYo.png'
        ];

        $html = $content;
        foreach ($placeholders as $key => $value) {
            $html = str_replace($key, e($value), $html);
        }

        $html = $this->templateService->convertImagesToBase64($html);

        $path = storage_path("app/public/badge_{$template->id}.pdf");

        Browsershot::html($html)
        ->setOption('width', 576)
        ->setOption('height', 576)
        ->margins(0, 0, 0, 0)
        ->save($path);

        return response()->download($path);
    }

    public function sendEmail(Request $request)
    {
        $template = Template::find('bc194220-236d-4ea2-972b-74a9b90a644d');
        $subject = 'Test invitation';
        $content = $template->contents;

        $placeholders = [
            '{{name}}' => 'ANTETOKOUNMPO',
        ];

        $html = $content;
        foreach ($placeholders as $key => $value) {
            $html = str_replace($key, e($value), $html);
        }

        $html = $this->templateService->convertImagesToBase64($html);

        try{
            Mail::to('destinataire@example.com')->send(new TemplateEmailMail($subject, $html));
            return response()->json(['message' => 'Email envoyé avec succès']);

        }catch (\Exception $exception){
            dd($exception->getMessage());
        }
    }

}
