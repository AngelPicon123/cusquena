const API_URL =
  "http://localhost:8080/PRACTICAS/5TO-CICLO/cusquena/backend/api/controllers/gestionUsuario.php";

document.addEventListener("DOMContentLoaded", function () {
  listarUsuarios();

  // Buscar usuario
  document.querySelector(".btn-primary").addEventListener("click", function () {
    const termino = document.querySelector(
      'input[placeholder="Buscar usuario"]'
    ).value;
    listarUsuarios(termino);
  });

  // Agregar usuario
  document
    .querySelector("#modalAgregar form")
    .addEventListener("submit", function (e) {
      e.preventDefault();

      const data = {
        usuario: document.getElementById("usuario").value,
        contrasena: document.getElementById("contrasena").value,
        correo: document.getElementById("correo").value,
        rol: document.getElementById("rol").value,
        estado: document.querySelector('input[name="estado"]:checked').value,
      };

      fetch(API_URL, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(data),
      })
        .then((res) => res.json())
        .then((res) => {
          showToastAgregar(res.message);
          listarUsuarios();
          document.querySelector("#modalAgregar .btn-close").click();
          document.querySelector("#modalAgregar form").reset();
        });
    });

  // Editar usuario
  document
    .querySelector("#modalEditar form")
    .addEventListener("submit", function (e) {
      e.preventDefault();

      const id = document.getElementById("editarId").value;
      const data = {
        id,
        usuario: document.getElementById("editarUsuario").value,
        contrasena: document.getElementById("editarContrasena").value,
        correo: document.getElementById("editarCorreo").value,
        rol: document.getElementById("editarRol").value,
        estado: document.querySelector('input[name="editarEstado"]:checked')
          .value,
      };

      fetch(API_URL, {
        method: "PUT",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(data),
      })
        .then((res) => res.json())
        .then((res) => {
          showToastEditar(res.message);
          listarUsuarios();
          document.querySelector("#modalEditar .btn-close").click();
        });
    });
});

// Función para listar usuarios
function listarUsuarios(buscar = "") {
  const tbody = document.querySelector("tbody");
  tbody.innerHTML = "";

  const url = buscar
    ? `${API_URL}?buscar=${encodeURIComponent(buscar)}`
    : API_URL;

  fetch(url)
    .then((res) => res.json())
    .then((data) => {
      data.forEach((usuario) => {
        const fila = document.createElement("tr");
        fila.innerHTML = `
              <td>${usuario.id}</td>
              <td>${usuario.usuario}</td>
              <td>${usuario.contrasena}</td>
              <td>${usuario.correo}</td>
              <td>${usuario.rol}</td>
              <td><span class="badge ${
                usuario.estado === "activo" ? "bg-success" : "bg-danger"
              }">${usuario.estado}</span></td>
              <td>
                <button class="btn btn-success btn-sm mb-1" onclick='llenarModalEditar(${JSON.stringify(
                  usuario
                )})' data-bs-toggle="modal" data-bs-target="#modalEditar">Editar</button>
                <button class="btn btn-danger btn-sm" onclick="confirmarEliminacion(${
                  usuario.id
                })">Eliminar</button>
              </td>
            `;
        tbody.appendChild(fila);
      });
    });
}

// Llenar modal de edición
function llenarModalEditar(usuario) {
  document.getElementById("editarId").value = usuario.id;
  document.getElementById("editarUsuario").value = usuario.usuario;
  document.getElementById("editarContrasena").value = usuario.contrasena;
  document.getElementById("editarCorreo").value = usuario.correo;
  document.getElementById("editarRol").value = usuario.rol;
  document.getElementById("editarEstadoActivo").checked =
    usuario.estado === "activo";
  document.getElementById("editarEstadoInactivo").checked =
    usuario.estado === "inactivo";
}

// Eliminar usuario
function eliminarUsuario(id) {
  fetch(API_URL, {
    method: "DELETE",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ id }),
  })
    .then((res) => res.json())
    .then((res) => {
      showToastEliminar(res.message);
      listarUsuarios();
    })
    .catch((error) => {
      console.error("Error al eliminar:", error);
    });
}

// toast message Agregar
function showToastAgregar(message) {
  const toastElement = document.getElementById("toastAgregar");
  const toastMessage = document.getElementById("toastMessageAgregar");

  toastMessage.textContent = message;

  const toast = new bootstrap.Toast(toastElement);
  toast.show();
}

// toast message Editar
function showToastEditar(message) {
  // Obtener el Toast y el mensaje
  const toastElement = document.getElementById("toastEditar");
  const toastMessage = document.getElementById("toastMessageEditar");

  // Establecer el mensaje
  toastMessage.textContent = message;

  // Mostrar el Toast
  const toast = new bootstrap.Toast(toastElement);
  toast.show();
}

// toast message Eliminar
function showToastEliminar(message) {
  const toastElement = document.getElementById("toastEliminar");
  const toastMessage = document.getElementById("toastMessageEliminar");

  toastMessage.textContent = message;

  const toast = new bootstrap.Toast(toastElement);
  toast.show();
}

// ventana modal de confirmacion
function confirmarEliminacion(id) {
  const modalElement = document.getElementById("modalEliminarConfirmacion");
  const modal = new bootstrap.Modal(modalElement);
  modal.show();

  const btnConfirmar = document.getElementById("btnConfirmarEliminar");

  // Elimina cualquier evento anterior para evitar múltiples ejecuciones
  const nuevoBoton = btnConfirmar.cloneNode(true);
  btnConfirmar.parentNode.replaceChild(nuevoBoton, btnConfirmar);

  // Botón "Sí, eliminar"
  nuevoBoton.addEventListener("click", () => {
    eliminarUsuario(id); // Ejecuta la eliminación
    modal.hide(); // Cierra el modal
  });
}
