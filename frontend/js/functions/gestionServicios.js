document.addEventListener('DOMContentLoaded', () => {
    const tbody = document.querySelector("tbody");
    const selectVenderDescripcion = document.getElementById('venderDescripcion'); // <select> del modal de venta
    const inputVenderPrecio = document.getElementById('venderPrecioUnitario'); // input para mostrar precio

    // Cargar servicios (tabla + select completo)
    function cargarServicios() {
        fetch('../../backend/api/controllers/gestionServicio.php')
            .then(res => res.json())
            .then(data => {
                tbody.innerHTML = '';
                selectVenderDescripcion.innerHTML = '<option value="">Seleccione un servicio</option>';

                data.forEach(s => {
                    // Agregar fila a la tabla
                    tbody.innerHTML += 
                        `<tr>
                            <td>${s.idServicio}</td>
                            <td>${s.descripcion}</td>
                            <td>${s.precioUnitario}</td>
                            <td><span class="badge bg-${s.estado === 'activo' ? 'success' : 'secondary'}">${s.estado}</span></td>
                            <td>
                                <button class="btn btn-success p-1" onclick='abrirEditar(${JSON.stringify(s)})' data-bs-toggle="modal" data-bs-target="#modalEditar">Editar</button>
                                <button class="btn btn-danger p-1" onclick="eliminarServicio(${s.idServicio})">Eliminar</button>
                            </td>
                        </tr>`;

                    // Agregar opción al select de venta solo si está activo
                    if (s.estado === 'activo') {
                        selectVenderDescripcion.innerHTML += `<option value="${s.idServicio}" data-precio="${s.precioUnitario}">${s.descripcion}</option>`;
                    }
                });
            });
    }

    cargarServicios();

    // Actualizar precio al cambiar descripción en el modal de venta
    selectVenderDescripcion.addEventListener('change', function () {
        const precio = this.options[this.selectedIndex].dataset.precio;
        inputVenderPrecio.value = precio ?? '';

        const inputTotal = document.getElementById('venderTotal');
        if (inputTotal) {
            inputTotal.value = precio ?? '';
        }
    });

    // Agregar servicio
    document.querySelector('#modalAgregar form').addEventListener('submit', e => {
        e.preventDefault();
        const descripcion = document.getElementById('descripcion').value;
        const precioUnitario = document.getElementById('precioUnitario').value;
        const estado = document.querySelector('input[name="estado"]:checked').value;
        fetch('../../backend/api/controllers/gestionServicio.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ descripcion, precioUnitario, estado })
        }).then(res => res.json())
            .then(() => {
                cargarServicios();
                document.getElementById('descripcion').value = '';
                document.getElementById('precioUnitario').value = '';
                document.querySelector('#modalAgregar .btn-close').click();
            });
    });

    // Abrir modal de editar
    window.abrirEditar = (s) => {
        document.getElementById('editarId').value = s.idServicio;
        document.getElementById('editarDescripcion').value = s.descripcion;
        document.getElementById('editarPrecioUnitario').value = s.precioUnitario;
        document.getElementById('editarEstado' + (s.estado === 'activo' ? 'Activo' : 'Inactivo')).checked = true;
    };

    // Editar servicio
    document.querySelector('#modalEditar form').addEventListener('submit', e => {
        e.preventDefault();
        const idServicio = document.getElementById('editarId').value;
        const descripcion = document.getElementById('editarDescripcion').value;
        const precioUnitario = document.getElementById('editarPrecioUnitario').value;
        const estado = document.querySelector('input[name="editarEstado"]:checked').value;
        fetch('../../backend/api/controllers/gestionServicio.php', {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ idServicio, descripcion, precioUnitario, estado })
        }).then(res => res.json())
            .then(() => {
                cargarServicios();
                document.querySelector('#modalEditar .btn-close').click();
            });
    });

    // Eliminar servicio
    window.eliminarServicio = (idServicio) => {
        if (confirm("¿Estás seguro de eliminar este servicio?")) {
            fetch('../../backend/api/controllers/gestionServicio.php', {
                method: 'DELETE',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `idServicio=${idServicio}`
            }).then(res => res.json())
                .then(() => cargarServicios());
        }
    };

    document.getElementById('btnRegistrarVenta').addEventListener('click', () => {
        const select = document.getElementById('venderDescripcion');
        const idServicio = select.value;
        const descripcion = select.options[select.selectedIndex].text;
        const precioUnitario = document.getElementById('venderPrecioUnitario').value;
        const fechaVenta = document.getElementById('venderfechaVenta').value;
        const total = document.getElementById('venderTotal').value;
    
        if (!idServicio || !fechaVenta) {
            alert('Complete todos los campos');
            return;
        }
    
        fetch('../../backend/api/controllers/ventaServicio.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ idServicio, descripcion, precioUnitario, fechaVenta, total })
        })
        .then(res => res.json())
        .then(res => {
            if (res.success) {
                alert("Venta registrada exitosamente");
                document.querySelector('#modalVender .btn-close').click();
                document.getElementById('venderDescripcion').value = '';
                document.getElementById('venderPrecioUnitario').value = '';
                document.getElementById('venderfechaVenta').value = '';
                document.getElementById('venderTotal').value = '';
            } else {
                alert("Error al registrar la venta");
            }
        });
    });
    
});
