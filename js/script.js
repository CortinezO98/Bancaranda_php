document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('searchForm');
    if (form) {
        form.addEventListener('submit', function(event) {
            event.preventDefault();
            const itemId = form.querySelector('input[name="item_id"]').value;
            const encodedItemId = encodeURIComponent(itemId);

            fetch(`includes/query.php?item_id=${encodedItemId}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('La respuesta de la red no fue correcta');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.error) {
                        console.error('Error:', data.error);
                        return;
                    }

                    const container = document.querySelector('.results-container');
                    container.innerHTML = ''; 

                    if (data.length > 0) {
                        const table = document.createElement('table');
                        table.classList.add('table', 'table-responsive');
                        const thead = document.createElement('thead');
                        thead.innerHTML = `
                            <tr>
                                <th scope="col">Caso</th>
                                <th scope="col">Fecha Registro</th>
                                <th scope="col">Asunto</th>
                                <th scope="col">Estado</th>
                                <th scope="col">Id</th>
                                <th scope="col">Acciones</th>
                            </tr>
                        `;
                        table.appendChild(thead);

                        const tbody = document.createElement('tbody');
                        data.forEach(item => {
                            const date = new Date(item.FechaRegistroCaso);
                            const formattedDate = `${date.getDate().toString().padStart(2, '0')}/${(date.getMonth() + 1).toString().padStart(2, '0')}/${date.getFullYear()}`;

                            const row = document.createElement('tr');
                            row.innerHTML = `
                                <td>${escapeHtml(item.Caso)}</td>
                                <td>${escapeHtml(formattedDate)}</td>
                                <td>${escapeHtml(item.Asunto)}</td>
                                <td>${escapeHtml(item.EstadoAbiertoCerrado)}</td>
                                <td>${escapeHtml(item.ItemId)}</td>
                                <td><a href="detalle_caso.php?item_id=${encodeURIComponent(item.ItemId)}" class="btn btn-sm btn-outline-info">Ver más Detalles</a></td>
                            `;
                            tbody.appendChild(row);
                        });
                        table.appendChild(tbody);

                        container.appendChild(table);
                    } else {
                        container.innerHTML = '<p class="text-danger text-center">No se encontraron casos para el número de cédula especificado.</p>';
                    }
                })
                .catch(error => console.error('Error:', error));
        });
    }
});


function escapeHtml(text) {
    if (text == null) return '';
    return text
        .toString()
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}
