$(document).ready(function () {
    // Initialisation de DataTable et stockage dans une variable
    var table = $('#productsTable').DataTable(); 

    // Appliquer un espace au-dessus de "Show entries" et de la barre de recherche
    $('.dataTables_wrapper .dataTables_length').css('margin-top', '20px');
    $('.dataTables_wrapper .dataTables_filter').css('margin-top', '20px');


    // Ajouter le bouton dans la barre de recherche
    let addButton = `
        <button class="btn btn-primary ms-2" id="addProductBtn">
            <i class="fa fa-plus"></i> Ajouter un produit
        </button>
    `;

    $('#productsTable_filter').append(addButton);

    // Fonction pour ajouter le symbole Euro aux prix
    function addEuroSymbol() {
        $('#productsTable tbody tr').each(function () {
            $(this).find('td').eq(1).text($(this).find('td').eq(1).text().replace(/€$/, '') + ' €'); // Prix adhérent
            $(this).find('td').eq(2).text($(this).find('td').eq(2).text().replace(/€$/, '') + ' €'); // Prix non adhérent
        });
    }

    // Appel initial pour afficher les euros sur les prix existants
    addEuroSymbol();

    // L'événement 'draw' est déclenché chaque fois que la table est redessinée (changement de page, recherche, ajout de produit, etc.)
    table.on('draw', function () {
        addEuroSymbol();  // Applique à nouveau le symbole euro après chaque redessin
    });

    // L'événement 'page' est déclenché lorsque l'on change de page dans la DataTable
    table.on('page', function () {
        addEuroSymbol();  // Applique à nouveau le symbole euro après un changement de page
    });

    // Ouvrir le modal lorsque le bouton "Ajouter produit" est cliqué
    $('#addProductBtn').click(function () {
        $('#addProductModal').modal('show');  // Afficher le modal
    });

    // Fonction pour mettre à jour le nombre de tickets en temps réel
    function updateNbTickets(product_id) {
        console.log("Product ID reçu :", product_id); // Vérifier la valeur de product_id

        $.ajax({
            url: `/get-product/${product_id}`,  // Route pour récupérer les détails du produit
            type: "GET",
            success: function (response) {
                if (response.success) {
                    let row = $('#productsTable').find(`[data-id='${product_id}']`).closest('tr');
                    let rowIndex = table.row(row).index();

                    if (rowIndex !== -1) {
                        table.cell(rowIndex, 5).data(response.product.nbTickets).draw(false); // MAJ nbTickets sans recharger
                    }
                }
            },
            error: function (error) {
                console.log(error);
            }
        });
    }

    // Sauvegarder un produit
    $('#saveProduct').click(function (e) {
        e.preventDefault();

        // Récupérer les valeurs des champs du formulaire
        let product_name = $('#product_name').val();
        let withdrawal_method = $('#withdrawal_method').val();
        let subsidized_price = $('#subsidized_price').val();
        let price = $('#price').val();
        let dematerialized = $('#dematerialized').is(':checked') ? 1 : 0; // Si la case est cochée, la valeur est 1 (true), sinon 0 (false)

        // Effectuer la requête AJAX pour envoyer les données
        $.ajax({
            url: "/ajouter-produit",  // Utilise la route définie dans web.php
            type: "POST",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                product_name: product_name,
                shop_id: 1, // Valeur hardcodée dans ton contrôleur
                quota_id: 1, // Valeur hardcodée dans ton contrôleur
                withdrawal_method: withdrawal_method,
                price: price,
                subsidized_price: subsidized_price,
                dematerialized: dematerialized  
            },

            success: function (response) {
                if (response.success) {
                    // Ajouter le produit dans le tableau sans recharger la page
                    table.row.add([
                        response.product.product_name,  // Nom du produit
                        response.product.subsidized_price + ' €',  // Prix subsidized avec euro
                        response.product.price + ' €',  // Prix non subsidized avec euro
                        response.product.withdrawal_method,  // Méthode de retrait
                        response.product.dematerialized,  // Catégorie
                        response.product.nbTickets,  // Nombre de tickets
                        `<button class="btn btn-sm btn-primary edit-product" data-id="${response.product.product_id}">
                            <i class="fa fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-primary gestion-ticket" data-id="{{ $product->product_id }}">
                            <i class="fa fa-plus"></i>
                        </button>
                        <button class="btn btn-sm btn-danger delete-product" data-id="${response.product.product_id}">
                            <i class="fa fa-trash"></i>
                        </button>`
                    ]).draw();

                    updateNbTickets(response.product.product_id);

                    // Re-attacher l'événement gestion-ticket pour les nouveaux éléments
                    $('.gestion-ticket').click(function () {
                        let productId = $(this).data('id');
                        window.location.href = `/choisir-type-ticket/${productId}`;
                    });

                    // Fermer le modal et réinitialiser le formulaire
                    $('#addProductModal').modal('hide');
                    $('#addProductForm')[0].reset();

                    
                }
            },
            error: function (error) {
                console.log(error);
            }
        });
    });
});
