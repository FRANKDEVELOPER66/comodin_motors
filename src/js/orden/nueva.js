// ============================================
// GESTIÓN DE SERVICIOS DINÁMICOS
// ============================================

// Array para almacenar servicios
let serviciosAgregados = [];
let servicioCounter = 0;

// Elementos DOM
const buscarServicio = document.getElementById('buscarServicio');
const resultadosServicios = document.getElementById('resultadosServicios');
const tablaServicios = document.getElementById('tablaServicios');
const totalServicios = document.getElementById('totalServicios');
const btnAgregarServicioManual = document.getElementById('btnAgregarServicioManual');

// ============================================
// BÚSQUEDA DE SERVICIOS (AUTOCOMPLETE)
// ============================================

let timeoutBusqueda = null;

buscarServicio.addEventListener('input', (e) => {
    const termino = e.target.value.trim();

    clearTimeout(timeoutBusqueda);

    if (termino.length < 2) {
        resultadosServicios.classList.remove('show');
        return;
    }

    timeoutBusqueda = setTimeout(() => {
        buscarEnCatalogo(termino);
    }, 300);
});

// Cerrar autocomplete al hacer clic fuera
document.addEventListener('click', (e) => {
    if (!e.target.closest('#buscarServicio') && !e.target.closest('#resultadosServicios')) {
        resultadosServicios.classList.remove('show');
    }
});

async function buscarEnCatalogo(termino) {
    try {
        const url = `/comodin_motors/API/servicios/buscar?q=${encodeURIComponent(termino)}`;
        const respuesta = await fetch(url);
        const data = await respuesta.json();

        if (data.codigo === 1 && data.datos && data.datos.length > 0) {
            mostrarResultadosBusqueda(data.datos);
        } else {
            resultadosServicios.innerHTML = `
                <div class="autocomplete-item" style="text-align: center; color: #666;">
                    No se encontraron servicios. Presione "Agregar Manual"
                </div>
            `;
            resultadosServicios.classList.add('show');
        }
    } catch (error) {
        console.error('Error al buscar servicios:', error);
    }
}

function mostrarResultadosBusqueda(servicios) {
    resultadosServicios.innerHTML = '';

    servicios.forEach(servicio => {
        const item = document.createElement('div');
        item.className = 'autocomplete-item';
        item.innerHTML = `
            <div class="servicio-codigo">${servicio.codigo}</div>
            <div class="servicio-descripcion">${servicio.descripcion}</div>
            <div class="servicio-precio">Q ${parseFloat(servicio.precio_sugerido).toFixed(2)}</div>
        `;

        item.addEventListener('click', () => {
            agregarServicioDesdeC

            atalogo(servicio);
            buscarServicio.value = '';
            resultadosServicios.classList.remove('show');
        });

        resultadosServicios.appendChild(item);
    });

    resultadosServicios.classList.add('show');
}

function agregarServicioDesdeCatalogo(servicio) {
    const servicioData = {
        id: ++servicioCounter,
        descripcion: servicio.descripcion,
        cantidad: 1,
        precio_unitario: parseFloat(servicio.precio_sugerido),
        subtotal: parseFloat(servicio.precio_sugerido),
        tipo: 'servicio'
    };

    serviciosAgregados.push(servicioData);
    renderizarTablaServicios();
    calcularTotal();

    Toast.fire({
        icon: 'success',
        title: 'Servicio agregado',
        timer: 1500
    });
}

// ============================================
// AGREGAR SERVICIO MANUAL
// ============================================

btnAgregarServicioManual.addEventListener('click', async () => {
    const { value: formValues } = await Swal.fire({
        title: 'Agregar servicio manualmente',
        html: `
            <div class="mb-3 text-start">
                <label class="form-label" style="color: #b0b0b0;">Descripción del servicio</label>
                <textarea id="swal-descripcion" class="swal2-input" rows="3" 
                    style="height: auto; background: #2a2a2a; border: 1px solid #3a3a3a; color: #fff;"
                    placeholder="Ejemplo: Cambio de aceite sintético"></textarea>
            </div>
            <div class="row">
                <div class="col-6 mb-3 text-start">
                    <label class="form-label" style="color: #b0b0b0;">Cantidad</label>
                    <input id="swal-cantidad" type="number" class="swal2-input" value="1" min="0.01" step="0.01"
                        style="background: #2a2a2a; border: 1px solid #3a3a3a; color: #fff;">
                </div>
                <div class="col-6 mb-3 text-start">
                    <label class="form-label" style="color: #b0b0b0;">Precio unitario</label>
                    <input id="swal-precio" type="number" class="swal2-input" min="0.01" step="0.01"
                        style="background: #2a2a2a; border: 1px solid #3a3a3a; color: #fff;"
                        placeholder="0.00">
                </div>
            </div>
            <div class="mb-3 text-start">
                <label class="form-label" style="color: #b0b0b0;">Tipo</label>
                <select id="swal-tipo" class="swal2-select" 
                    style="background: #2a2a2a; border: 1px solid #3a3a3a; color: #fff;">
                    <option value="servicio">Servicio</option>
                    <option value="repuesto">Repuesto</option>
                    <option value="mano_obra">Mano de Obra</option>
                </select>
            </div>
        `,
        confirmButtonText: 'Agregar',
        cancelButtonText: 'Cancelar',
        showCancelButton: true,
        confirmButtonColor: '#00ff00',
        background: '#1a1a1a',
        color: '#fff',
        preConfirm: () => {
            const descripcion = document.getElementById('swal-descripcion').value.trim();
            const cantidad = parseFloat(document.getElementById('swal-cantidad').value);
            const precio = parseFloat(document.getElementById('swal-precio').value);
            const tipo = document.getElementById('swal-tipo').value;

            if (!descripcion) {
                Swal.showValidationMessage('La descripción es requerida');
                return false;
            }
            if (!cantidad || cantidad <= 0) {
                Swal.showValidationMessage('La cantidad debe ser mayor a 0');
                return false;
            }
            if (!precio || precio <= 0) {
                Swal.showValidationMessage('El precio debe ser mayor a 0');
                return false;
            }

            return { descripcion, cantidad, precio, tipo };
        }
    });

    if (formValues) {
        const servicioData = {
            id: ++servicioCounter,
            descripcion: formValues.descripcion,
            cantidad: formValues.cantidad,
            precio_unitario: formValues.precio,
            subtotal: formValues.cantidad * formValues.precio,
            tipo: formValues.tipo
        };

        serviciosAgregados.push(servicioData);
        renderizarTablaServicios();
        calcularTotal();

        Toast.fire({
            icon: 'success',
            title: 'Servicio agregado correctamente'
        });
    }
});

// ============================================
// RENDERIZAR TABLA
// ============================================

function renderizarTablaServicios() {
    if (serviciosAgregados.length === 0) {
        tablaServicios.innerHTML = `
            <tr class="empty-state">
                <td colspan="6" class="text-center" style="color: #666; padding: 2rem;">
                    <i class="bi bi-inbox" style="font-size: 3rem; display: block; margin-bottom: 1rem;"></i>
                    No hay servicios agregados
                </td>
            </tr>
        `;
        return;
    }

    tablaServicios.innerHTML = '';

    serviciosAgregados.forEach((servicio, index) => {
        const fila = document.createElement('tr');
        fila.innerHTML = `
            <td>${index + 1}</td>
            <td>
                <input type="text" class="form-control-sm" value="${servicio.descripcion}" 
                    onchange="actualizarDescripcionServicio(${servicio.id}, this.value)" 
                    style="background: #2a2a2a; border: 1px solid #3a3a3a; color: #fff; width: 100%; padding: 0.5rem;">
            </td>
            <td>
                <input type="number" class="form-control-sm" value="${servicio.cantidad}" 
                    min="0.01" step="0.01"
                    onchange="actualizarCantidadServicio(${servicio.id}, this.value)"
                    style="background: #2a2a2a; border: 1px solid #3a3a3a; color: #fff; width: 100%; padding: 0.5rem;">
            </td>
            <td>
                <input type="number" class="form-control-sm" value="${servicio.precio_unitario}" 
                    min="0.01" step="0.01"
                    onchange="actualizarPrecioServicio(${servicio.id}, this.value)"
                    style="background: #2a2a2a; border: 1px solid #3a3a3a; color: #fff; width: 100%; padding: 0.5rem;">
            </td>
            <td style="color: #00ff00; font-weight: 700;">
                Q ${servicio.subtotal.toFixed(2)}
            </td>
            <td>
                <button type="button" class="btn-eliminar-servicio" onclick="eliminarServicio(${servicio.id})">
                    <i class="bi bi-trash"></i>
                </button>
            </td>
        `;
        tablaServicios.appendChild(fila);
    });
}

// ============================================
// ACTUALIZAR SERVICIOS
// ============================================

window.actualizarDescripcionServicio = (id, nuevaDescripcion) => {
    const servicio = serviciosAgregados.find(s => s.id === id);
    if (servicio) {
        servicio.descripcion = nuevaDescripcion;
    }
};

window.actualizarCantidadServicio = (id, nuevaCantidad) => {
    const servicio = serviciosAgregados.find(s => s.id === id);
    if (servicio) {
        servicio.cantidad = parseFloat(nuevaCantidad) || 1;
        servicio.subtotal = servicio.cantidad * servicio.precio_unitario;
        renderizarTablaServicios();
        calcularTotal();
    }
};

window.actualizarPrecioServicio = (id, nuevoPrecio) => {
    const servicio = serviciosAgregados.find(s => s.id === id);
    if (servicio) {
        servicio.precio_unitario = parseFloat(nuevoPrecio) || 0;
        servicio.subtotal = servicio.cantidad * servicio.precio_unitario;
        renderizarTablaServicios();
        calcularTotal();
    }
};

window.eliminarServicio = (id) => {
    serviciosAgregados = serviciosAgregados.filter(s => s.id !== id);
    renderizarTablaServicios();
    calcularTotal();

    Toast.fire({
        icon: 'info',
        title: 'Servicio eliminado',
        timer: 1500
    });
};

// ============================================
// CALCULAR TOTAL
// ============================================

function calcularTotal() {
    const total = serviciosAgregados.reduce((sum, servicio) => sum + servicio.subtotal, 0);
    totalServicios.textContent = `Q ${total.toFixed(2)}`;

    // Actualizar el costo_total del formulario
    const inputCostoTotal = document.getElementById('costo_total');
    if (inputCostoTotal) {
        inputCostoTotal.value = total.toFixed(2);
    }
}

// ============================================
// OBTENER SERVICIOS PARA ENVIAR AL SERVIDOR
// ============================================

function obtenerServiciosParaGuardar() {
    return serviciosAgregados.map(servicio => ({
        descripcion: servicio.descripcion,
        cantidad: servicio.cantidad,
        precio_unitario: servicio.precio_unitario,
        subtotal: servicio.subtotal,
        tipo: servicio.tipo
    }));
}

console.log('✅ Sistema de servicios dinámicos cargado');