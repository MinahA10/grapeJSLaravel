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
            'is_active' => 'required|boolean'
        ]);

        $template = Template::create($validated);
        return response()->json($template, 201);
    }

    public function editor(Request $request, Template $template)
    {
        return $this->show_gjs_editor($request, $template);
    }

    public function getTemplates(){
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

    public function save(Template $template,Request $request)
    {
        $data = $request->input('project');

        $template->update([
            'type' => 'badge',
            'name' => 'test',
            'is_active' => true,
            'data' => $data
        ]);

        return response()->json(['message' => 'Saved', 'template' => $template], 200);
    }

    public function load(Template $template)
    {
        $data = $template->data ? json_decode($template->data, true) : [];

        return response()->json(['project' => $data]);
    }
}
