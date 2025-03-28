$('#boutiquesTable').DataTable({
    language: {
        "emptyTable": "Aucune donnée disponible dans le tableau",
        "info": "Affichage de _START_ à _END_ sur _TOTAL_ entrées",
        "infoEmpty": "Affichage de 0 à 0 sur 0 entrée",
        "infoFiltered": "(filtré de _MAX_ entrées au total)",
        "lengthMenu": "Afficher _MENU_ entrées",
        "loadingRecords": "Chargement...",
        "processing": "Traitement...",
        "search": "Rechercher :",
        "zeroRecords": "Aucun élément correspondant trouvé",
        "paginate": {
            "first": "Premier",
            "last": "Dernier",
            "next": "Suivant",
            "previous": "Précédent"
        }
    },
    order: [[9, 'desc']],
    pageLength: 25,
    responsive: true,
    autoWidth: true,
    columnDefs: [
        { className: "text-center align-middle", targets: "_all" }
    ]
});
