@extends('template')

@section('title')
    Mes achats
@endsection

@section('head')
    <link rel="stylesheet" href="{{ asset('css/pages_css/achats.css') }}">
    <script src="{{ asset('js/pages_js/achats.js') }}"></script>
@endsection

@section('content')
    <h3 class="achats-title">Mes achats</h3>
    <div class="achats-container">
        @if(count($aggregatedTickets) > 0)
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Produit</th>
                        <th>Site</th>
                        <th>Nombre d'achats</th>
                        <th>Prix Total</th>
                        <th>Détails</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($aggregatedTickets as $aggregatedTicket)
                        <tr>
                            <td>{{ $aggregatedTicket['product_name'] }}</td>
                            <td>{{ $aggregatedTicket['site_label'] }}</td>
                            <td>{{ $aggregatedTicket['total_quantity'] }}</td>
                            <td>{{ $aggregatedTicket['total_price'] }} €</td>
                            <td>
                                <button class="btn btn-sm btn-primary details-button" type="button" data-target="ticketDetails{{ $loop->index }}">
                                    <i class="fa fa-caret-down"></i>
                                </button>
                                <div class="ticket-details" id="ticketDetails{{ $loop->index }}">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Date d'achat</th>
                                                <th>Prix unitaire</th>
                                                <th>Ticket</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($aggregatedTicket['tickets'] as $ticket)
                                                <tr>
                                                    <td>{{ $ticket['purchase_date'] }}</td>
                                                    <td>{{ $ticket['price'] }} €</td>
                                                    <td>
                                                        @if($ticket['ticket_link'])
                                                            <a href="{{ route('tickets.view', ['ticketId' => $ticket['ticket_id']]) }}" target="_blank" class="btn btn-sm btn-success">
                                                                <i class="fa fa-eye"></i> Voir
                                                            </a>
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p class="no-achats">Vous n'avez effectué aucun achat pour le moment.</p>
        @endif
    </div>
@endsection

@section('scripts')
    <!-- jQuery (required for DataTables) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            var table = $('#ticketsTable').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json"
                },
                "pageLength": 10,
                "order": [[5, "desc"]],
                "columnDefs": [
                    { "orderable": false, "targets": 3 }
                ]
            });


            $('#showAllTickets').on('change', function() {
                table.draw();
            });

            $.fn.dataTable.ext.search.push(
                function(settings, data, dataIndex) {
                    var showAll = $('#showAllTickets').is(':checked');
                    var ticketType = $(table.row(dataIndex).node()).data('ticket-type');

                    if (showAll) {
                        return true;
                    }
                    return ticketType === 'dematerialized';
                }
            );
            table.draw();
        });
    </script>
@endsection
