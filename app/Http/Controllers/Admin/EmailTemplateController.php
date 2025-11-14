<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmailTemplate;
use Illuminate\Http\Request;

class EmailTemplateController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $templates = EmailTemplate::orderBy('created_at', 'desc')->paginate(15);
        
        $stats = [
            'total' => EmailTemplate::count(),
            'active' => EmailTemplate::where('is_active', true)->count(),
            'newsletter' => EmailTemplate::where('category', 'newsletter')->count(),
            'promotional' => EmailTemplate::where('category', 'promotional')->count(),
        ];
        
        return view('admin.email.templates.index', compact('templates', 'stats'));
    }

    public function create()
    {
        return view('admin.email.templates.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'category' => 'required|in:newsletter,promotional,transactional,announcement',
            'content' => 'required|string',
            'variables' => 'nullable|array',
        ]);

        $validated['is_active'] = $request->has('is_active');

        EmailTemplate::create($validated);

        return redirect()->route('admin.templates.index')
            ->with('success', 'Template berhasil dibuat.');
    }

    public function show($id)
    {
        $template = EmailTemplate::findOrFail($id);
        return view('admin.email.templates.show', compact('template'));
    }

    public function edit($id)
    {
        $template = EmailTemplate::findOrFail($id);
        return view('admin.email.templates.edit', compact('template'));
    }

    public function update(Request $request, $id)
    {
        $template = EmailTemplate::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'category' => 'required|in:newsletter,promotional,transactional,announcement',
            'content' => 'required|string',
            'variables' => 'nullable|array',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $template->update($validated);

        return redirect()->route('admin.templates.index')
            ->with('success', 'Template berhasil diupdate.');
    }

    public function destroy($id)
    {
        $template = EmailTemplate::findOrFail($id);
        $template->delete();

        return redirect()->route('admin.templates.index')
            ->with('success', 'Template berhasil dihapus.');
    }
}

