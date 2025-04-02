<?php

namespace App\Http\Controllers;

use App\Models\MailTemplate;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TemplateMailController extends Controller
{
    /**
     * Affiche la liste des templates mail
     */
    public function index()
    {
        $templates = MailTemplate::with('shop')->get();
        $shops = Shop::all();
        $admin_page = true;
        return view('template_mail', compact('templates', 'shops', 'admin_page'));
    }

    /**
     * Enregistre un nouveau template mail
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:100',
            'content' => 'required|string',
            'shop_id' => 'nullable|exists:shop,shop_id'
        ]);

        try {
            $template = new MailTemplate();
            $template->subject = $validated['subject'];
            $template->content = $validated['content'];
            if (isset($validated['shop_id']) && !is_null($validated['shop_id'])) {
                $template->shop_id = $validated['shop_id'];
            }
            $template->save();

            return response()->json([
                'success' => true,
                'message' => 'Template enregistré avec succès',
                'template' => $template
            ]);
        } catch (\Exception $e) {
            Log::error("Erreur lors de l'enregistrement du template: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => "Erreur lors de l'enregistrement du template",
                'shop_id' => $validated['shop_id']
            ], 500);
        }
    }

    /**
     * Récupère un template mail pour l'édition
     */
    public function show($id)
    {
        try {
            $template = MailTemplate::findOrFail($id);
            return response()->json($template);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Template non trouvé'
            ], 404);
        }
    }

    /**
     * Met à jour un template mail existant
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:100',
            'content' => 'required|string',
            'shop_id' => 'nullable|exists:shop,shop_id'
        ]);

        try {
            $template = MailTemplate::findOrFail($id);
            $template->subject = $validated['subject'];
            $template->content = $validated['content'];
            $template->shop_id = $validated['shop_id'] ?? null;
            $template->save();

            return response()->json([
                'success' => true,
                'message' => 'Template mis à jour avec succès',
                'template' => $template
            ]);
        } catch (\Exception $e) {
            Log::error("Erreur lors de la mise à jour du template: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => "Erreur lors de la mise à jour du template"
            ], 500);
        }
    }

    /**
     * Supprime un template mail
     */
    public function destroy($id)
    {
        try {
            $template = MailTemplate::findOrFail($id);
            $template->delete();

            return response()->json([
                'success' => true,
                'message' => 'Template supprimé avec succès'
            ]);
        } catch (\Exception $e) {
            Log::error("Erreur lors de la suppression du template: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => "Erreur lors de la suppression du template"
            ], 500);
        }
    }
}
