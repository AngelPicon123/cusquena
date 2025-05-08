const API_INGRESO_PRODUCTO = 'http://localhost/cusquena/backend/api/controllers/gestionHistorialIngreso.php';
const API_PRODUCTO = 'http://localhost/cusquena/backend/api/controllers/gestionProducto.php';

// Inicializar Flatpickr para el campo de fecha en el modal de Agregar
document.addEventListener('DOMContentLoaded', () => {
    flatpickr("#fechaIngreso", {
        dateFormat: "d/m/Y", // Formato dd/mm/yyyy
        allowInput: true, // Permitir entrada manual
        placeholder: "dd/mm/aaaa"
    });

    listarIngresos();
    cargarProductosEnSelect('idProducto');
    cargarProductosEnSelect('editarIdProducto');
});

// Buscar ingreso
document.getElementById('btnBuscar').addEventListener('click', () => {
    const termino = document.getElementById('buscarIngreso').value.trim();
    listarIngresos(termino);
});

// Agregar ingreso
document.getElementById('formAgregar').addEventListener('submit', async (e) => {
    e.preventDefault();
    const fechaInput = document.getElementById('fechaIngreso').value; // Fecha en formato dd/mm/yyyy
    let fechaDB;

    // Convertir fecha de dd/mm/yyyy a yyyy-mm-dd para el backend
    if (fechaInput) {
        const [day, month, year] = fechaInput.split('/');
        fechaDB = `${year}-${month}-${day}`;
    }

    const data = {
        fechaIngreso: fechaDB,
        stock: parseInt(document.getElementById('stock').value),
        precioCompra: parseFloat(document.getElementById('precioCompra').value),
        idProducto: document.getElementById('idProducto').value,
        detalle: document.getElementById('detalle').value
    };

    if (!data.fechaIngreso || !data.stock || !data.precioCompra || !data.idProducto) {
        alert("Por favor, completa todos los campos requeridos.");
        return;
    }

    try {
        const res = await fetch(API_INGRESO_PRODUCTO, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });
        const result = await res.json();
        alert(result.message);
        listarIngresos();
        document.getElementById('formAgregar').reset();
        document.querySelector('#miModal .btn-close').click();
    } catch (error) {
        alert('Error al agregar el ingreso.');
        console.error(error);
    }
});

// Editar ingreso
document.getElementById('formEditar').addEventListener('submit', async (e) => {
    e.preventDefault();
    const data = {
        idIngresoProducto: document.getElementById('editarIdIngresoProducto').value,
        fechaIngreso: document.getElementById('editarFechaIngreso').value,
        stock: parseInt(document.getElementById('editarstock').value),
        precioCompra: parseFloat(document.getElementById('editarPrecioCompra').value),
        idProducto: document.getElementById('editarIdProducto').value,
        detalle: document.getElementById('editarDetalle').value
    };

    try {
        const res = await fetch(API_INGRESO_PRODUCTO, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });
        const result = await res.json();
        alert(result.message);
        listarIngresos();
        document.querySelector('#modalEditar .btn-close').click();
    } catch (error) {
        alert('Error al actualizar el ingreso.');
        console.error(error);
    }
});

async function listarIngresos(buscar = '') {
    const tbody = document.getElementById('tablaIngresos');
    tbody.innerHTML = '';
    const url = buscar ? `${API_INGRESO_PRODUCTO}?buscar=${encodeURIComponent(buscar)}` : API_INGRESO_PRODUCTO;

    try {
        const res = await fetch(url);
        const data = await res.json();
        
        if (data.length === 0) {
            tbody.innerHTML = '<tr><td colspan="7">No se encontraron resultados</td></tr>';
            return;
        }

        data.forEach(ingreso => {
            const fila = document.createElement('tr');
            fila.innerHTML = `
                <td>${ingreso.idIngresoProducto}</td>
                <td>${formatearFechaVista(ingreso.fechaIngreso)}</td>
                <td>${ingreso.stock}</td>
                <td>S/.${parseFloat(ingreso.precioCompra).toFixed(2)}</td>
                <td>${ingreso.productoDescripcion}</td>
                <td>${ingreso.detalle || '-'}</td>
                <td>
                    <button class="btn btn-success btn-sm" onclick='llenarModalEditar(${JSON.stringify(ingreso)})' data-bs-toggle="modal" data-bs-target="#modalEditar">Editar</button>
                    <button class="btn btn-danger btn-sm" onclick="eliminarIngreso(${ingreso.idIngresoProducto})">Eliminar</button>
                </td>
            `;
            tbody.appendChild(fila);
        });
    } catch (error) {
        tbody.innerHTML = '<tr><td colspan="7">Error al cargar los datos</td></tr>';
        console.error(error);
    }
}

function llenarModalEditar(ingreso) {
    document.getElementById('editarIdIngresoProducto').value = ingreso.idIngresoProducto;
    document.getElementById('editarFechaIngreso').value = ingreso.fechaIngreso;
    document.getElementById('editarstock').value = ingreso.stock;
    document.getElementById('editarPrecioCompra').value = ingreso.precioCompra;
    cargarProductosEnSelect('editarIdProducto', ingreso.idProducto);
    document.getElementById('editarDetalle').value = ingreso.detalle || '';
}

function formatearFechaVista(fechaBD) {
    const [a, m, d] = fechaBD.split("-");
    return `${d}-${m}-${a}`;
}

async function cargarProductosEnSelect(idSelect, idSeleccionado = null) {
    try {
        const res = await fetch(API_PRODUCTO);
        const data = await res.json();
        const select = document.getElementById(idSelect);
        select.innerHTML = '<option value="">--SELECCIONAR--</option>';
        data.forEach(prod => {
            const option = document.createElement('option');
            option.value = prod.idProducto;
            option.textContent = prod.descripcion;
            if (idSeleccionado && prod.idProducto == idSeleccionado) {
                option.selected = true;
            }
            select.appendChild(option);
        });
    } catch (error) {
        console.error('Error al cargar productos:', error);
    }
}

async function eliminarIngreso(idIngresoProducto) {
    if (!confirm('¿Estás seguro de eliminar este ingreso?')) return;

    try {
        const res = await fetch(API_INGRESO_PRODUCTO, {
            method: 'DELETE',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ idIngresoProducto })
        });
        const result = await res.json();
        alert(result.message);
        listarIngresos();
    } catch (error) {
        alert('Error al eliminar el ingreso.');
        console.error(error);
    }
}