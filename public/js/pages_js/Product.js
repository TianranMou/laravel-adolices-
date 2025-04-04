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
        let shop_id = $('input[name="shop_id"]').val(); // Valeur hardcodée dans le contrôleur

        // Effectuer la requête AJAX pour envoyer les données
        $.ajax({
            url: "/edit-boutique/product/" + shop_id ,  // Utilise la route définie dans web.php
            type: "POST",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                product_name: product_name,
                shop_id: $('input[name="shop_id"]').val(), // Valeur hardcodée dans ton contrôleur
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
                        response.product.dematerialized ? 'dématérialisé' : 'physique',  // Catégorie
                        response.product.nbTickets,  // Nombre de tickets
                        `<button class="btn btn-sm btn-primary edit-product" data-id="${response.product.product_id}">
                            <i class="fa fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-primary gestion-ticket" data-id="${ response.product.product_id }">
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



    // Ouvrir le modal d'édition et pré-remplir les champs
    $(document).on('click', '.edit-product', function () {
        let productId = $(this).data('id');

        $.ajax({
            url: `/get-product/${productId}`, // Route pour récupérer les infos du produit
            type: "GET",
            success: function (response) {
                if (response.success) {
                    let product = response.product;

                    $('#edit_product_id').val(product.product_id);
                    $('#edit_product_name').val(product.product_name);
                    $('#edit_withdrawal_method').val(product.withdrawal_method);
                    $('#edit_subsidized_price').val(product.subsidized_price);
                    $('#edit_price').val(product.price);
                    $('#edit_dematerialized').prop('checked', product.dematerialized == 1);

                    $('#editProductModal').modal('show'); // Afficher le modal
                }
            },
            error: function (error) {
                console.log(error);
            }
        });
    });

    $('#updateProduct').click(function (e) {
        e.preventDefault();

        // Récupérer les valeurs des champs du formulaire d'édition
        let productId = $('#edit_product_id').val();
        console.log("ID du produit à mettre à jour :", productId); // Vérifier la valeur de productId
        let product_name = $('#edit_product_name').val();
        let withdrawal_method = $('#edit_withdrawal_method').val();
        let subsidized_price = $('#edit_subsidized_price').val();
        let price = $('#edit_price').val();
        let dematerialized = $('#edit_dematerialized').is(':checked') ? 1 : 0;
        let shop_id = $('input[name="shop_id"]').val(); // Valeur hardcodée dans le contrôleur
        //let quota_id = $('#edit_quota_id').val();  // Ajouter quota_id depuis le formulaire

        if (!productId || !shop_id) {
            console.log("Paramètres manquants : shop_id ou product_id");
        return;
    }

        // Effectuer la requête AJAX pour mettre à jour le produit
        $.ajax({
            url: `/produit-update/${shop_id}`,  // Route pour mettre à jour le produit
            type: "POST",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                _method: 'POST',  // Utiliser POST pour correspondre à la méthode définie dans la route
                product_id: productId,  // ID du produit à mettre à jour
                product_name: product_name,
                withdrawal_method: withdrawal_method,
                subsidized_price: subsidized_price,
                price: price,
                dematerialized: dematerialized,
                shop_id: shop_id,  // Ajouter shop_id dans les données envoyées
                quota_id: 1,  // Ajouter quota_id dans les données envoyées
            },
            success: function (response) {
                if (response.success) {
                    // Mettre à jour la ligne du produit dans DataTables sans recharger la page
                    let row = $('#productsTable').find(`[data-id='${productId}']`).closest('tr');
                    let rowIndex = table.row(row).index();

                    if (rowIndex !== -1) {
                        table.row(rowIndex).data([
                            response.product.product_name,
                            response.product.subsidized_price + ' €',
                            response.product.price + ' €',
                            response.product.withdrawal_method,
                            response.product.dematerialized ? 'dématérialisé' : 'physique',
                            response.product.nbTickets,
                            `<button class="btn btn-sm btn-primary edit-product" data-id="${response.product.product_id}">
                                <i class="fa fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-primary gestion-ticket" data-id="${response.product.product_id}">
                                <i class="fa fa-plus"></i>
                            </button>
                            <button class="btn btn-sm btn-danger delete-product" data-id="${response.product.product_id}">
                                <i class="fa fa-trash"></i>
                            </button>`
                        ]).draw(false);

                        updateNbTickets(response.product.product_id);

                        // Re-attacher l'événement gestion-ticket pour les nouveaux éléments
                        $('.gestion-ticket').click(function () {
                            let productId = $(this).data('id');
                            window.location.href = `/choisir-type-ticket/${productId}`;
                        });
                        }

                        $('#editProductModal').modal('hide'); // Fermer le modal d'édition
                    } else {
                        alert("Erreur lors de la mise à jour du produit.");
                    }
            },
            error: function (error) {
                console.log("Erreur:", error);
                alert('Une erreur est survenue, veuillez réessayer.');
            }
        });
    });

    // Ajouter l'événement de suppression du produit
    $(document).on('click', '.delete-product', function () {
        let productId = $(this).data('id');  // Récupérer l'ID du produit à supprimer
        if (confirm('Êtes-vous sûr de vouloir supprimer ce produit ?')) {
            $.ajax({
                url: `/produit-delete/${productId}`,  // Route pour supprimer le produit
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    if (response.success) {
                        // Supprimer la ligne du tableau sans recharger la page
                        let row = $('#productsTable').find(`[data-id='${productId}']`).closest('tr');
                        table.row(row).remove().draw();
                    } else {
                        alert('Erreur lors de la suppression du produit.');
                    }
                },
                error: function (error) {
                    console.log(error);
                    alert('Une erreur est survenue, veuillez réessayer.');
                }
            });
        }
    });



});
