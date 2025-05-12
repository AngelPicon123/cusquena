document.addEventListener('DOMContentLoaded', function () {
    // Función para obtener los balances de productos
    function obtenerBalances(inicio, fin) {
        const url = `../../backend/api/controllers/balanceProducto.php?inicio=${inicio}&fin=${fin}`;
        console.log('Solicitando datos a:', url);

        fetch(url)
            .then(response => {
                console.log('Respuesta recibida, estado:', response.status);
                if (!response.ok) {
                    throw new Error('Error en la respuesta: ' + response.statusText);
                }
                return response.json();
            })
            .then(data => {
                console.log('Datos recibidos:', data);
                const tbody = document.querySelector('#tablaDatos');
                const totalInput = document.querySelector('#total');
                let total = 0;

                tbody.innerHTML = ''; // Limpiar contenido previo

                if (data.error) {
                    console.log('Error recibido:', data.error);
                    tbody.innerHTML = `<tr><td colspan="5">${data.error}</td></tr>`;
                    return;
                }

                if (!data || data.length === 0) {
                    console.log('No se encontraron datos para mostrar.');
                    tbody.innerHTML = '<tr><td colspan="5">No se encontraron datos</td></tr>';
                    return;
                }

                data.forEach(registro => {
                    console.log('Registro procesado:', registro);
                    const precio = parseFloat(registro.precioUnitario) || 0;
                    const cantidad = parseFloat(registro.cantidad) || 0;
                    const subtotal = parseFloat(registro.subtotal) || 0;
                    let fecha = registro.fecha || 'Sin fecha';

                    // Convertir la fecha al formato dd-mm-yyyy
                    if (fecha !== 'Sin fecha' && fecha) {
                        const fechaObj = new Date(fecha);
                        const day = String(fechaObj.getDate()).padStart(2, '0');
                        const month = String(fechaObj.getMonth() + 1).padStart(2, '0'); // Los meses en JavaScript son 0-indexados
                        const year = fechaObj.getFullYear();
                        fecha = `${day}-${month}-${year}`;
                    }

                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${registro.descripcion || 'Sin descripción'}</td>
                        <td>S/ ${precio.toFixed(2)}</td>
                        <td>${cantidad}</td>
                        <td>S/ ${subtotal.toFixed(2)}</td>
                        <td>${fecha}</td>
                    `;
                    total += subtotal;
                    tbody.appendChild(row);
                });

                totalInput.value = `S/ ${total.toFixed(2)}`;
            })
            .catch(error => {
                console.error('Error al obtener datos de balances:', error.message);
                const tbody = document.querySelector('#tablaDatos');
                tbody.innerHTML = '<tr><td colspan="5">Error al cargar datos: ' + error.message + '</td></tr>';
            });
    }

    // Evento para el botón de buscar
    document.getElementById("btnBuscar").addEventListener("click", function () {
        const inicio = document.getElementById("inicio").value;
        const fin = document.getElementById("fin").value;
        console.log('Fechas seleccionadas:', { inicio, fin });
        obtenerBalances(inicio, fin);
    });

    // Obtener balances inicialmente (sin filtro)
    obtenerBalances('', '');

    // Exportar a PDF
    document.getElementById("exportarPdf").addEventListener("click", function () {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();

    // Obtener datos de filtros
    let fechaInicio = document.getElementById("inicio").value || "No especificada";
    let fechaFin = document.getElementById("fin").value || "No especificada";

    // Convertir fechas al formato dd-mm-yyyy
    if (fechaInicio !== "No especificada") {
        const fechaInicioObj = new Date(fechaInicio);
        fechaInicio = `${String(fechaInicioObj.getDate()).padStart(2, '0')}-${String(fechaInicioObj.getMonth() + 1).padStart(2, '0')}-${fechaInicioObj.getFullYear()}`;
    }

    if (fechaFin !== "No especificada") {
        const fechaFinObj = new Date(fechaFin);
        fechaFin = `${String(fechaFinObj.getDate()).padStart(2, '0')}-${String(fechaFinObj.getMonth() + 1).padStart(2, '0')}-${fechaFinObj.getFullYear()}`;
    }

    // Título
    doc.setFontSize(16);
    doc.text("Balances de productos", 14, 15);

    // Filtros aplicados
    doc.setFontSize(12);
    doc.text(`Fecha Inicio: ${fechaInicio}`, 14, 25);
    doc.text(`Fecha Fin: ${fechaFin}`, 100, 25);

    // Construir tabla
    const headers = [["Descripción", "Precio Unitario", "Cantidad", "Subtotal", "Fecha"]];
    const body = [];
    document.querySelectorAll("#tablaDatos tr").forEach(row => {
        const rowData = [];
        row.querySelectorAll("td").forEach(cell => {
            rowData.push(cell.innerText);
        });
        if (rowData.length > 0) body.push(rowData);
    });

    // Validar si hay datos
    if (body.length === 0) {
        alert("No hay datos para exportar.");
        return;
    }

    // Tabla con AutoTable
    doc.autoTable({
        startY: 35,
        head: headers,
        body: body,
        styles: { fontSize: 10 },
        headStyles: { fillColor: [41, 128, 185] }
    });

    // Total al final
    const finalY = doc.lastAutoTable.finalY + 10;
    doc.setFontSize(12);
    doc.text(`TOTAL: ${document.getElementById("total").value}`, 14, finalY);

    // Guardar PDF
    doc.save("balances_productos.pdf");
});


    // Función para imprimir la tabla de datos
    document.getElementById("btnImprimir").addEventListener("click", function () {
        window.print();
    });
});
