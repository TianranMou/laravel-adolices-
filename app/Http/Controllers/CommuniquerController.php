<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Models\MailTemplate;
use App\Services\RocketChatService;

class CommuniquerController extends Controller
{
    protected $rocketChatService;

    public function __construct(RocketChatService $rocketChatService)
    {
        $this->rocketChatService = $rocketChatService;
    }

    /**
     * Affiche la page de création de communication
     */
    public function index(Request $request)
    {
        try {
            $templates = MailTemplate::all();
            $oldValues = [];

            // Vérifier si on revient de la prévisualisation
            if ($request->has('from_preview')) {
                // Récupérer les données de communication depuis la session
                $oldValues = $request->session()->get('communicationData', []);
            } else {
                // Les valeurs précédentes normales (en cas de rechargement ou d'erreur)
                $oldValues = old();
            }

            try {
                $channels = $this->rocketChatService->getChannels();
            } catch (\Exception $e) {
                Log::error('Failed to fetch RocketChat channels', ['error' => $e->getMessage()]);
                $channels = [];
            }

            return view('Communiquer', compact('templates', 'oldValues', 'channels'));
        } catch (\Exception $e) {
            Log::error('Error in CommuniquerController@index', ['error' => $e->getMessage()]);
            return view('Communiquer', ['templates' => [], 'oldValues' => [], 'channels' => []]);
        }
    }

    /**
     * Prévisualise la communication avant envoi
     */
    public function sendCommunication(Request $request)
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:100',
            'content' => 'required|string',
            'email_addresses' => 'nullable|array',
            'email_addresses.*' => 'email',
            'rocket_chat_type' => 'nullable|in:channel,direct',
            'rocket_channels' => 'nullable|array|required_if:rocket_chat_type,channel',
            'rocket_users' => 'nullable|array|required_if:rocket_chat_type,direct',
        ]);

        if (!empty($validated['subject']) && !empty($validated['content'])) {
            // Store all data in session
            $communicationData = [
                'subject' => $validated['subject'],
                'content' => $validated['content'],
                'email_addresses' => $validated['email_addresses'] ?? [],
                'communication_type' => $request->input('communication_type', 'email'),
                'rocket_chat_type' => $validated['rocket_chat_type'] ?? null,
                'rocket_channels' => $validated['rocket_channels'] ?? [],
                'rocket_users' => $validated['rocket_users'] ?? [],
            ];

            $request->session()->put('communicationData', $communicationData);

            $recipients = $validated['email_addresses'] ?? [];

            return view('emailPreview', [
                'subject' => $validated['subject'],
                'content' => $validated['content'],
                'recipients' => $recipients,
                'rocketChatType' => $validated['rocket_chat_type'] ?? null,
                'rocketChannels' => $validated['rocket_channels'] ?? [],
                'rocketUsers' => $validated['rocket_users'] ?? []
            ]);
        }

        return redirect()->route('communiquer')->with('error', 'Veuillez remplir tous les champs requis');
    }

    /**
     * Envoie la communication après validation de la preview
     */
    public function confirmSendCommunication(Request $request)
    {
        $communicationData = $request->session()->get('communicationData', []);

        if (empty($communicationData)) {
            return redirect()->route('communiquer')->with('error', 'Aucune donnée de communication trouvée');
        }

        try {
            $successCount = 0;
            $errorCount = 0;

            // Send emails if email addresses are provided
            if (!empty($communicationData['email_addresses'])) {
                foreach ($communicationData['email_addresses'] as $email) {
                    try {
                        // Mail::to($email)->send(new \App\Mail\CommunicationMail($communicationData));
                        $successCount++;
                    } catch (\Exception $e) {
                        Log::error("Erreur d'envoi d'email à {$email}: " . $e->getMessage());
                        $errorCount++;
                    }
                }
            }

            // Send RocketChat messages
            if (!empty($communicationData['rocket_chat_type'])) {
                if ($communicationData['rocket_chat_type'] === 'channel' && !empty($communicationData['rocket_channels'])) {
                    foreach ($communicationData['rocket_channels'] as $channelId) {
                        try {
                            $this->rocketChatService->sendMessage($channelId, $communicationData['content']);
                            Log::info("Message RocketChat envoyé au canal {$channelId}");
                            $successCount++;
                        } catch (\Exception $e) {
                            Log::error("Erreur d'envoi RocketChat au canal {$channelId}: " . $e->getMessage());
                            $errorCount++;
                        }
                    }
                } elseif ($communicationData['rocket_chat_type'] === 'direct' && !empty($communicationData['rocket_users'])) {
                    foreach ($communicationData['rocket_users'] as $username) {
                        try {
                            $this->rocketChatService->createAndSendDirectMessage($username, $communicationData['content']);

                            Log::info("Message RocketChat envoyé à l'utilisateur {$username}");
                            $successCount++;
                        } catch (\Exception $e) {
                            Log::error("Erreur d'envoi RocketChat à l'utilisateur {$username}: " . $e->getMessage(), [
                                'exception' => $e,
                                'trace' => $e->getTraceAsString()
                            ]);
                            $errorCount++;
                        }
                    }
                }
            }

            // Clear the session data
            $request->session()->forget('communicationData');

            if ($errorCount > 0) {
                $message = "Communications partiellement envoyées. Succès: {$successCount}, Échecs: {$errorCount}";
                return redirect()->route('communiquer')->with('warning', $message);
            }

            return redirect()->route('communiquer')->with('success', 'Communications envoyées avec succès');
        } catch (\Exception $e) {
            Log::error("Erreur générale d'envoi de communication: " . $e->getMessage());
            return redirect()->route('communiquer')->with('error', 'Erreur lors de l\'envoi des communications');
        }
    }

    /**
     * Enregistre un modèle d'email
     */
    public function saveTemplate(Request $request)
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:100',
            'content' => 'required|string',
        ]);

        try {
            $template = new MailTemplate();
            $template->subject = $validated['subject'];
            $template->content = $validated['content'];
            $template->save();

            return response()->json([
                'success' => true,
                'message' => 'Modèle d\'email enregistré avec succès',
                'template' => [
                    'mail_template_id' => $template->mail_template_id,
                    'subject' => $template->subject,
                    'content' => $template->content
                ]
            ]);
        } catch (\Exception $e) {
            Log::error("Erreur lors de l'enregistrement du modèle: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'enregistrement du modèle'
            ], 500);
        }
    }
}
