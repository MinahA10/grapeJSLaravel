<?php

namespace App\Http\Controllers;

use App\Models\Template;
use Dotlogics\Grapesjs\App\Traits\EditorTrait;
use Illuminate\Http\Request;

class TemplateController extends Controller
{
    use EditorTrait;

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|string',
            'name' => 'required|string',
            'is_active' => 'required|boolean',
            'contents' => 'required|string',
        ]);

        $template = Template::create($validated);
        return response()->json($template, 201);
    }
    public function editor(Request $request, Template $template)
    {
        return $this->show_gjs_editor($request, $template);
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
}
