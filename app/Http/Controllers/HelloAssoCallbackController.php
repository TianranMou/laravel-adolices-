<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\User;
use App\Models\Site;
use App\Models\Shop;
use App\Models\Quota;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class HelloAssoCallbackController extends Controller
{
    private $apiKey;
    private $apiSecret;
    private $organizationSlug;

    public function __construct()
    {
        $this->apiKey = env('HELLOASSO_API_KEY');
        $this->apiSecret = env('HELLOASSO_API_SECRET');
        $this->organizationSlug = env('HELLOASSO_ORGANIZATION_SLUG');
    }

    public function handleCallback(Request $request)
    {
        try {
            // Get the raw data from the request
            $data = $request->all();

            // Log the callback data for debugging
            Log::info('HelloAsso callback received', $data);

            // Verify the event type
            if (!isset($data['eventType']) || $data['eventType'] !== 'Order') {
                Log::warning('Invalid event type received', ['eventType' => $data['eventType'] ?? 'not set']);
                return response()->json(['error' => 'Invalid event type'], 400);
            }

            // Verify the organization
            if (!isset($data['data']['organizationSlug']) || $data['data']['organizationSlug'] !== $this->organizationSlug) {
                Log::warning('Invalid organization received', ['organization' => $data['data']['organizationSlug'] ?? 'not set']);
                return response()->json(['error' => 'Invalid organization'], 400);
            }

            // Extract relevant information from the callback
            $payer = $data['data']['payer'];
            $items = $data['data']['items'];
            $payment = $data['data']['payments'][0];

            // Find the user by email
            $user = User::where('email', $payer['email'])->first();

            if (!$user) {
                Log::error('User not found for HelloAsso callback', ['email' => $payer['email']]);
                return response()->json(['error' => 'User not found'], 404);
            }

            // Process each item in the order
            foreach ($items as $item) {
                // Get site from custom fields
                $siteField = collect($item['customFields'])->firstWhere('name', 'Site');
                if (!$siteField) {
                    Log::error('Site field not found in custom fields', ['item' => $item]);
                    return response()->json(['error' => 'Site field not found in custom fields'], 400);
                }

                // Find the site by label
                $site = Site::findByLabel($siteField['answer']);
                if (!$site) {
                    Log::error('Site not found', ['site_label' => $siteField['answer']]);
                    return response()->json(['error' => 'Site not found'], 404);
                }

                // Find an available ticket for this product and site
                $ticket = Ticket::where('product_id', $item['id'])
                    ->where('site_id', $site->site_id)
                    ->whereNull('user_id')  // Ticket is not assigned to any user
                    ->first();

                if (!$ticket) {
                    Log::error('No available ticket found', [
                        'product_id' => $item['id'],
                        'site_id' => $site->site_id
                    ]);
                    return response()->json(['error' => 'No available ticket found'], 404);
                }

                // Update the ticket to assign it to the user
                try {
                    $ticket->updateTicket([
                        'product_id' => $item['id'],
                        'site_id' => $site->site_id,
                        'user_id' => $user->user_id,
                        'purchase_date' => Carbon::parse($payment['date']),
                        'validity_date' => Carbon::parse($payment['date'])->addYear(),
                        'ticket_link' => $payment['paymentReceiptUrl'],
                        'partner_code' => 'HELLOASSO',
                        'partner_id' => (string)$payment['id'],
                    ]);
                } catch (\Exception $e) {
                    Log::error('Error updating ticket', [
                        'error' => $e->getMessage(),
                        'data' => [
                            'ticket_id' => $ticket->ticket_id,
                            'user_id' => $user->user_id,
                            'purchase_date' => $payment['date'],
                            'validity_date' => Carbon::parse($payment['date'])->addYear(),
                            'ticket_link' => $payment['paymentReceiptUrl'],
                            'partner_code' => 'HELLOASSO',
                            'partner_id' => (string)$payment['id'],
                        ]
                    ]);
                    throw $e;
                }
            }

            return response()->json(['message' => 'Purchase processed successfully']);

        } catch (\Exception $e) {
            Log::error('Error processing HelloAsso callback', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Test the HelloAsso API connection
     */
    public function testConnection()
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json'
            ])->get("https://api.helloasso.com/v5/organizations/{$this->organizationSlug}/forms");

            if ($response->successful()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Successfully connected to HelloAsso API',
                    'data' => $response->json()
                ]);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to connect to HelloAsso API',
                'error' => $response->json()
            ], $response->status());

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error connecting to HelloAsso API',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test the callback with sample data
     */
    public function testCallback()
    {
        try {
            // Create a test site if it doesn't exist
            $site = Site::findOrCreateByLabel('Douai / Lahure');

            // Create a test shop if it doesn't exist
            $shop = Shop::findOrCreateByName('Test Shop', [
                'short_description' => 'A test shop for testing purposes',
                'long_description' => 'This is a test shop created for testing the HelloAsso callback functionality',
                'min_limit' => 100,
                'end_date' => '2024-12-31',
                'is_active' => true,
                'thumbnail' => 'https://example.com/thumbnail.jpg',
                'doc_link' => 'https://example.com/doc.pdf',
                'bc_link' => 'https://example.com/bc',
                'ha_link' => 'https://example.com/ha',
                'photo_link' => 'https://example.com/photo.jpg'
            ]);

            // Create a test quota if it doesn't exist
            $quota = Quota::findOrCreateByValueAndDuration(100, 365);

            // Create a test product if it doesn't exist
            $product = Product::findOrCreateByName('Concert Tickets', [
                'shop_id' => $shop->shop_id,
                'quota_id' => $quota->quota_id,
                'withdrawal_method' => 'digital',
                'subsidized_price' => 45.00,
                'price' => 89.99,
                'dematerialized' => true
            ]);

            // Create a test user if it doesn't exist
            $user = User::firstOrCreate(
                ['email' => 'testUser@example.com'],
                [
                    'status_id' => 1,
                    'group_id' => 1,
                    'last_name' => 'testUser',
                    'first_name' => 'testUser',
                    'password' => bcrypt('password'),
                    'photo_release' => true,
                    'photo_consent' => true,
                    'is_admin' => false
                ]
            );

            // Create some unassigned tickets for testing
            Ticket::createTicket([
                'product_id' => $product->product_id,
                'site_id' => $site->site_id,
                'user_id' => null, // Unassigned ticket
                'ticket_link' => 'https://example.com/ticket1',
                'partner_code' => 'TEST',
                'partner_id' => 'test1',
                'validity_date' => now()->addYear(),
                'purchase_date' => now(),
            ]);

            $sampleData = [
                "data" => [
                    "payer" => [
                        "email" => "testUser@example.com",
                        "country" => "FRA",
                        "firstName" => "testUser",
                        "lastName" => "testUser"
                    ],
                    "items" => [
                        [
                            "payments" => [["id" => 41918526, "shareAmount" => 8999]],
                            "name" => "Concert Tickets",
                            "user" => [
                                "firstName" => "testUser",
                                "lastName" => "testUser"
                            ],
                            "priceCategory" => "Fixed",
                            "customFields" => [
                                ["id" => 13218565, "name" => "Service/CERI", "type" => "TextInput", "answer" => "DISI"],
                                ["id" => 13218566, "name" => "Site", "type" => "ChoiceList", "answer" => "Douai / Lahure"],
                                ["id" => 13329175, "name" => "Tél. Portable", "type" => "TextInput", "answer" => "06 06 06 06 06"],
                                ["id" => 13218568, "name" => "Mél pro", "type" => "TextInput", "answer" => "testUser@imt.com"],
                                ["id" => 13218569, "name" => "Statut", "type" => "ChoiceList", "answer" => "CDI"],
                                ["id" => 13218570, "name" => "Adresse postale", "type" => "FreeText", "answer" => "59500 Douai"],
                                ["id" => 13218571, "name" => "Mél perso", "type" => "TextInput", "answer" => "testUser@example.com"],
                                ["id" => 13218574, "name" => "J'accepte être éventuellement photographié.e par ADOLICES", "type" => "YesNo", "answer" => "Oui"],
                                ["id" => 13218575, "name" => "J'autorise ADOLICES à diffuser ces visuels sur son site intranet, Facebook ou sur des plaquettes", "type" => "YesNo", "answer" => "Oui"]
                            ],
                            "qrCode" => "OTEwMjAzNjY6NjM4NTE5MDU5MTM1MTYwNjMz",
                            "membershipCardUrl" => "https://www.helloasso.com/associations/adolices/adhesions/adhesion-adolices-2023-2024/carte-adherent?cardId=91020366&ag=91020366",
                            "tierDescription" => "Pour cette année, le tarif est le même pour tous !",
                            "tierId" => 8925431,
                            "id" => $product->product_id,
                            "amount" => 8999,
                            "type" => "Membership",
                            "initialAmount" => 8999,
                            "state" => "Processed"
                        ]
                    ],
                    "payments" => [
                        [
                            "items" => [["id" => $product->product_id, "shareAmount" => 8999, "shareItemAmount" => 8999]],
                            "cashOutState" => "Transfered",
                            "paymentReceiptUrl" => "https://www.helloasso.com/associations/adolices/adhesions/adhesion-adolices-2023-2024/paiement-attestation/91020366",
                            "id" => 41918526,
                            "amount" => 8999,
                            "date" => "2024-05-21T16:34:22.9137455+02:00",
                            "paymentMeans" => "Card",
                            "installmentNumber" => 1,
                            "state" => "Authorized",
                            "meta" => [
                                "createdAt" => "2024-05-21T16:31:53.5160633+02:00",
                                "updatedAt" => "2024-05-21T16:34:22.9925183+02:00"
                            ],
                            "refundOperations" => []
                        ]
                    ],
                    "amount" => ["total" => 8999, "vat" => 0, "discount" => 0],
                    "id" => 91020366,
                    "date" => "2024-05-21T16:34:23.1826474+02:00",
                    "formSlug" => "adhesion-adolices-2023-2024",
                    "formType" => "Membership",
                    "organizationName" => "ADOLICES",
                    "organizationSlug" => "adolices",
                    "organizationType" => "Association1901Rig",
                    "organizationIsUnderColucheLaw" => false,
                    "meta" => [
                        "createdAt" => "2024-05-21T16:31:53.5160633+02:00",
                        "updatedAt" => "2024-05-21T16:34:23.1826474+02:00"
                    ],
                    "isAnonymous" => false,
                    "isAmountHidden" => false
                ],
                "eventType" => "Order"
            ];

            // Create a new request with the sample data
            $request = new Request();
            $request->merge($sampleData);

            // Call the handleCallback method with the request
            return $this->handleCallback($request);

        } catch (\Exception $e) {
            Log::error('Error in testCallback', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}