<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;

class Ticket extends Model
{
    use HasFactory;
    protected $table = 'ticket';
    protected $primaryKey = 'ticket_id';
    public $timestamps = true;

    protected $fillable = [
        'product_id',
        'user_id',
        'site_id',
        'ticket_link',
        'partner_code',
        'partner_id',
        'validity_date',
        'purchase_date',
    ];

    protected $casts = [
        'validity_date' => 'date',
        'purchase_date' => 'date'
    ];

        public function produit(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }

        public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

        public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class, 'site_id', 'site_id');
    }

        public function scopeDematerialized($query)
    {
        return $query->join('product', 'ticket.product_id', '=', 'product.product_id')
                     ->where('product.dematerialized', true)
                     ->select('ticket.*');
    }

    /**
     * Get all dematerialized tickets, optionally filtered by user ID.
     *
     * @param int|null $user_id
     * @return \Illuminate\Support\Collection
     */
    public static function getDematerializedTickets($user_id = null)
    {
        $query = static::dematerialized()->with(['produit', 'site', 'user']);

        if ($user_id) {
            $query->where('ticket.user_id', $user_id);
        }

        return $query->get();
    }

    /**
     * Get a specific dematerialized ticket by its ID.
     *
     * @param int $ticket_id
     * @return \App\Models\Ticket|null
     */
    public static function getDematerializedTicketById($ticket_id)
    {
        return static::dematerialized()
                     ->where('ticket.ticket_id', $ticket_id)
                     ->with(['produit', 'site', 'user'])
                     ->first();
    }

    /**
     * Get all tickets for a specific user by their ID.
     *
     * @param int $user_id
     * @return \Illuminate\Support\Collection
     */
    public static function getTicketByUserId($user_id)
    {
        return static::where('user_id', $user_id)->get();
    }

    /**
     * Get all tickets with their related models.
     *
     * @return \Illuminate\Support\Collection
     */
    public static function getAllTicketsWithRelations()
    {
        return static::with(['produit', 'user', 'site'])->get();
    }

    /**
     * Find a specific ticket with its related models by its ID.
     *
     * @param int $ticket_id
     * @return \App\Models\Ticket|null
     */
    public static function findTicketWithRelations($ticket_id)
    {
        return static::with(['produit', 'user', 'site'])->find($ticket_id);
    }

    /**
     * Create a new ticket with the given data.
     *
     * @param array $data
     * @return \App\Models\Ticket
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public static function createTicket(array $data)
    {
        $validator = Validator::make($data, [
            'product_id' => 'required|exists:product,product_id',
            'user_id' => 'nullable|exists:users,user_id',
            'site_id' => 'required|exists:site,site_id',
            'ticket_link' => 'required|string|max:255',
            'partner_code' => 'required|string|max:255',
            'partner_id' => 'required|string|max:255',
            'validity_date' => 'required|date',
            'purchase_date' => 'required|date',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return static::create($data);
    }

    /**
     * Update the current ticket with the given data.
     *
     * @param array $data
     * @return bool
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function updateTicket(array $data)
    {
        $validator = Validator::make($data, [
            'product_id' => 'required|exists:product,product_id',
            'user_id' => 'nullable|exists:users,user_id',
            'site_id' => 'required|exists:site,site_id',
            'ticket_link' => 'required|string|max:255',
            'partner_code' => 'required|string|max:255',
            'partner_id' => 'required|string|max:255',
            'validity_date' => 'required|date',
            'purchase_date' => 'required|date',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $this->update($data);
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            $validator = Validator::make($model->getAttributes(), [
                'product_id' => 'required|exists:product,product_id',
                'user_id' => 'nullable|exists:users,user_id',
                'site_id' => 'required|exists:site,site_id',
                'ticket_link' => 'nullable|string',
                'partner_code' => 'nullable|string',
                'partner_id' => 'nullable|string',
                'validity_date' => 'nullable|date',
                'purchase_date' => 'nullable|date'
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }
        });
    }

    public static function getNbTicketByProductId($product_id){
        return Ticket::where('product_id', $product_id)
                     ->count();
    }

    /**
     * Get aggregated tickets for a specific user.
     *
     * @param int $user_id
     * @return \Illuminate\Support\Collection
     */
    public static function getAggregatedTicketsForUser(int $user_id): Collection
    {
        $tickets = static::where('user_id', $user_id)
            ->with(['produit', 'site'])
            ->orderBy('purchase_date', 'desc')
            ->get();

        return self::aggregateTicketsByProduct($tickets);
    }

    /**
     * Aggregate tickets by product.
     *
     * @param \Illuminate\Support\Collection $tickets
     * @return \Illuminate\Support\Collection
     */
    private static function aggregateTicketsByProduct(Collection $tickets): Collection
    {
        return $tickets->groupBy('product_id')->map(function ($productTickets) {
            $firstTicket = $productTickets->first();
            $totalPrice = $productTickets->sum(function ($ticket) {
                return $ticket->produit->price;
            });

            return [
                'product_name' => $firstTicket->produit->product_name,
                'site_label' => $firstTicket->site->label_site,
                'total_quantity' => $productTickets->count(),
                'total_price' => $totalPrice,
                'tickets' => $productTickets->map(function ($ticket) {
                    return [
                        'ticket_id' => $ticket->ticket_id,
                        'purchase_date' => $ticket->purchase_date->format('d/m/Y H:i'),
                        'price' => $ticket->produit->price,
                        'ticket_link' => $ticket->ticket_link,
                    ];
                }),
            ];
        })->sortByDesc(function ($aggregatedTicket) {
            return $aggregatedTicket['tickets']->max('purchase_date');
        })->values();
    }

}

