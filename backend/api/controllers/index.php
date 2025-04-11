<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Usuarios</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <h1>Gestión de Usuarios</h1>

    <div class="form-container">
        <h2>Formulario de Usuario</h2>
        <form id="formUsuario">
            <input type="hidden" id="idUsuario" name="idUsuario">
            
            <div class="form-group">
                <label for="usuario">Usuario:</label>
                <input type="text" id="usuario" name="usuario" required>
            </div>
            
            <div class="form-group">
                <label for="contrasena">Contraseña:</label>
                <input type="password" id="contrasena" name="contrasena" placeholder="Dejar vacío para no cambiar">
            </div>
            
            <div class="form-group">
                <label for="correo">Correo:</label>
                <input type="email" id="correo" name="correo" required>
            </div>
            
            <div class="form-group">
                <label for="rol">Rol:</label>
                <select id="rol" name="rol" required>
                    <option value="">Seleccione un rol</option>
                    <option value="Administrador">Administrador</option>
                    <option value="Secretaria">Secretaria</option>
                </select>
            </div>
            
            <button type="button" id="btnGuardar">Guardar</button>
            <button type="button" id="btnCancelar" style="display:none;">Cancelar</button>
        </form>
    </div>
    <h2>Usuarios Registrados</h2>
    <table id="tablaUsuarios">
        <thead>
            <tr>
                <th>ID</th>
                <th>Usuario</th>
                <th>Correo</th>
                <th>Rol</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>

    <script>
        $(document).ready(function() {
            cargarUsuarios();
            
            // Configurar el formulario
            $('#btnGuardar').click(guardarUsuario);
            $('#btnCancelar').click(limpiarFormulario);
        });

        function cargarUsuarios() {
            $.getJSON('usuariosController.php?accion=listar', function(usuarios) {
                let html = '';
                usuarios.forEach(u => {
                    html += `
                        <tr>
                            <td>${u.idUsuario}</td>
                            <td>${u.usuario}</td>
                            <td>${u.correo}</td>
                            <td>${u.rol}</td>
                            <td class="actions">
                                <button class="btnEditar" data-id="${u.idUsuario}">Editar</button>
                                <button class="btnEliminar" data-id="${u.idUsuario}">Eliminar</button>
                            </td>
                        </tr>
                    `;
                });
                $('#tablaUsuarios tbody').html(html);
                
                // Configurar eventos de los botones
                $('.btnEditar').click(editarUsuario);
                $('.btnEliminar').click(eliminarUsuario);
            }).fail(function() {
                alert('Error al cargar los usuarios');
            });
        }

        function editarUsuario() {
            const id = $(this).data('id');
            $.getJSON('usuariosController.php?accion=obtener&id=' + id, function(usuario) {
                if (usuario && usuario.idUsuario) {
                    $('#idUsuario').val(usuario.idUsuario);
                    $('#usuario').val(usuario.usuario);
                    $('#correo').val(usuario.correo);
                    $('#rol').val(usuario.rol);
                    $('#contrasena').val('').attr('placeholder', 'Dejar vacío para no cambiar');
                    $('#btnCancelar').show();
                    $('html, body').animate({ scrollTop: 0 }, 'slow');
                } else {
                    alert('Usuario no encontrado');
                }
            }).fail(function() {
                alert('Error al obtener los datos del usuario');
            });
        }

        function eliminarUsuario() {
            const id = $(this).data('id');
            if (confirm('¿Estás seguro de eliminar este usuario?')) {
                $.post('usuariosController.php', { 
                    accion: 'eliminar', 
                    id: id 
                }, function(res) {
                    if (res.status === 'ok') {
                        alert(res.message);
                        cargarUsuarios();
                        limpiarFormulario();
                    } else {
                        alert('Error: ' + (res.message || 'Error desconocido'));
                    }
                }, 'json').fail(function() {
                    alert('Error en la comunicación con el servidor');
                });
            }
        }

        function guardarUsuario() {
            // Validación básica
            if (!$('#usuario').val() || !$('#correo').val() || !$('#rol').val()) {
                alert('Por favor complete todos los campos requeridos');
                return;
            }

            const id = $('#idUsuario').val();
            const datos = {
                accion: id ? 'editar' : 'agregar',
                id: id,
                usuario: $('#usuario').val(),
                correo: $('#correo').val(),
                rol: $('#rol').val()
            };

            // Solo agregar contraseña si es nuevo usuario o si se proporcionó una
            if (!id || $('#contrasena').val()) {
                if (!id && !$('#contrasena').val()) {
                    alert('La contraseña es requerida para nuevos usuarios');
                    return;
                }
                datos.contrasena = $('#contrasena').val();
            }

            $.post('usuariosController.php', datos, function(res) {
                if (res.status === 'ok') {
                    alert(res.message || (id ? 'Usuario actualizado correctamente' : 'Usuario agregado correctamente'));
                    limpiarFormulario();
                    cargarUsuarios();
                } else {
                    alert('Error: ' + (res.message || 'Error desconocido'));
                }
            }, 'json').fail(function() {
                alert('Error en la comunicación con el servidor');
            });
        }

        function limpiarFormulario() {
            $('#formUsuario')[0].reset();
            $('#idUsuario').val('');
            $('#btnCancelar').hide();
            $('#contrasena').attr('placeholder', 'Dejar vacío para no cambiar');
        }
    </script>
</body>
</html>