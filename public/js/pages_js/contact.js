document.addEventListener('DOMContentLoaded', function() {
    const shopSelect = document.getElementById('shop-select');
    const administratorsContainer = document.getElementById('administrators-container');
    const administratorsList = document.getElementById('administrators-list');
    const noAdminsMessage = document.getElementById('no-admins-message');
    const adminTitle = document.getElementById('admin-title');
    const shopColumnHeader = document.getElementById('shop-column-header');

    shopSelect.addEventListener('change', function() {
        const shopId = this.value;

        if (!shopId) {
            administratorsContainer.classList.add('d-none');
            return;
        }

        // Set the endpoint based on selection
        let endpoint = '';
        if (shopId === 'all') {
            endpoint = '/contact/all-administrators';
            adminTitle.textContent = 'Tous les gestionnaires';
            shopColumnHeader.classList.remove('d-none');
        } else {
            endpoint = `/contact/shop/${shopId}/`;
            adminTitle.textContent = 'Gestionnaires de la boutique';
            shopColumnHeader.classList.add('d-none');
        }

        // Fetch administrators
        fetch(endpoint)
            .then(response => response.json())
            .then(data => {
                administratorsContainer.classList.remove('d-none');
                administratorsList.innerHTML = '';

                if (data.length === 0) {
                    noAdminsMessage.classList.remove('d-none');
                } else {
                    noAdminsMessage.classList.add('d-none');

                    data.forEach(admin => {
                        const row = document.createElement('tr');
                        let rowContent = `
                            <td>${admin.last_name}</td>
                            <td>${admin.first_name}</td>
                            <td>${admin.email}</td>
                        `;

                        // Add shop column for "all" option
                        if (shopId === 'all' && admin.shop_name) {
                            rowContent += `<td>${admin.shop_name}</td>`;
                        }

                        row.innerHTML = rowContent;
                        administratorsList.appendChild(row);
                    });
                }
            })
            .catch(error => {
                console.error('Error fetching administrators:', error);
                administratorsList.innerHTML = '<tr><td colspan="4" class="text-danger">Une erreur est survenue lors du chargement des gestionnaires.</td></tr>';
            });
    });
});
