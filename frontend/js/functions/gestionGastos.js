document.addEventListener("DOMContentLoaded", function () {
    const tablaBody = document.querySelector("tbody");
    const buscarBtn = document.querySelector(".btn-primary[href='#']");
    const inputs = document.querySelectorAll("input");
    const selectTipo = document.getElementById("tipo");
  
    function cargarGastos(filtro = {}) {
      fetch("../../backend/api/controllers/gestionGastos.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ accion: "listar", ...filtro })
      })
        .then(res => res.json())
        .then(data => {
          tablaBody.innerHTML = "";
          data.forEach(gasto => {
            const fila = document.createElement("tr");
            fila.innerHTML = `
              <td>${gasto.id}</td>
              <td>${gasto.descripcion}</td>
              <td>${gasto.tipo}</td>
              <td>S/ ${gasto.monto}</td>
              <td>${gasto.fecha}</td>
              <td>${gasto.detalle}</td>
              <td>
                <a href="#" class="btn btn-success btnEditar p-1" data-id="${gasto.id}">Editar</a>
                <a href="#" class="btn btn-danger btnEliminar p-1" data-id="${gasto.id}">Eliminar</a>
              </td>`;
            tablaBody.appendChild(fila);
          });
        });
    }
  
    document.querySelector("#modalAgregar form").addEventListener("submit", function (e) {
      e.preventDefault();
      const data = {
        accion: "agregar",
        descripcion: document.getElementById("descripcion").value,
        tipo: document.getElementById("tipo").value,
        monto: document.getElementById("monto").value,
        fecha: document.getElementById("fecha").value,
        detalle: document.getElementById("detalle").value,
      };
      fetch("../../backend/api/controllers/gestionGastos.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(data)
      })
        .then(res => res.json())
        .then(() => {
          cargarGastos();
          e.target.reset();
          bootstrap.Modal.getInstance(document.getElementById("modalAgregar")).hide();
        });
    });
  
    tablaBody.addEventListener("click", function (e) {
      if (e.target.classList.contains("btnEditar")) {
        const id = e.target.dataset.id;
        fetch("../../backend/api/controllers/gestionGastos.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ accion: "obtener", id })
        })
          .then(res => res.json())
          .then(data => {
            document.getElementById("editarId").value = data.id;
            document.getElementById("editardescripcion").value = data.descripcion;
            document.getElementById("editartipo").value = data.tipo;
            document.getElementById("editarmonto").value = data.monto;
            document.getElementById("editarfecha").value = data.fecha;
            document.getElementById("editardetalle").value = data.detalle;
            new bootstrap.Modal(document.getElementById("modalEditar")).show();
          });
      }
  
      if (e.target.classList.contains("btnEliminar")) {
        if (confirm("¿Seguro que deseas eliminar este gasto?")) {
          const id = e.target.dataset.id;
          fetch("../php/gestionGastos.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ accion: "eliminar", id })
          })
            .then(res => res.json())
            .then(() => cargarGastos());
        }
      }
    });
  
    document.querySelector("#modalEditar form").addEventListener("submit", function (e) {
      e.preventDefault();
      const data = {
        accion: "editar",
        id: document.getElementById("editarId").value,
        descripcion: document.getElementById("editardescripcion").value,
        tipo: document.getElementById("editartipo").value,
        monto: document.getElementById("editarmonto").value,
        fecha: document.getElementById("editarfecha").value,
        detalle: document.getElementById("editardetalle").value,
      };
      fetch("../../backend/api/controllers/gestionGastos.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(data)
      })
        .then(res => res.json())
        .then(() => {
          cargarGastos();
          bootstrap.Modal.getInstance(document.getElementById("modalEditar")).hide();
        });
    });
  
    buscarBtn.addEventListener("click", function () {
      const filtro = {
        accion: "buscar",
        descripcion: inputs[2].value.trim(),
        inicio: inputs[0].value,
        fin: inputs[1].value
      };
      cargarGastos(filtro);
    });
  
    cargarGastos();
  });
  