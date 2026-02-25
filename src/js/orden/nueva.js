import { Toast, validarFormulario } from "../funciones";
import Swal from "sweetalert2";

// ============================================
// VARIABLES GLOBALES
// ============================================
let clienteSeleccionado = null;
let vehiculoSeleccionado = null;
let danosRegistrados = [];
let danoCounter = 0;

// ============================================
// ELEMENTOS DEL DOM
// ============================================
const elementos = {
    buscarTelefono: document.getElementById('buscar_telefono'),
    btnBuscarCliente: document.getElementById('btnBuscarCliente'),
    datosCliente: document.getElementById('datosCliente'),
    btnNuevoCliente: document.getElementById('btnNuevoCliente'),
    btnCrearCliente: document.getElementById('btnCrearCliente'),
    idCliente: document.getElementById('id_cliente'),
    clienteNombre: document.getElementById('cliente_nombre'),
    clienteTelefono: document.getElementById('cliente_telefono'),
    clienteEmpresa: document.getElementById('cliente_empresa'),
    clienteDireccion: document.getElementById('cliente_direccion'),
    seccionVehiculo: document.getElementById('seccionVehiculo'),
    vehiculosExistentes: document.getElementById('vehiculosExistentes'),
    listaVehiculos: document.getElementById('listaVehiculos'),
    btnNuevoVehiculo: document.getElementById('btnNuevoVehiculo'),
    formNuevoVehiculo: document.getElementById('formNuevoVehiculo'),
    idVehiculo: document.getElementById('id_vehiculo'),
    nivelCombustible: document.getElementById('nivel_combustible'),
    fuelLevel: document.getElementById('fuelLevel'),
    carCanvas: document.getElementById('carCanvas'),
    damageList: document.getElementById('damageList'),
    damageItems: document.getElementById('damageItems'),
    resumenCliente: document.getElementById('resumen_cliente'),
    resumenTelefono: document.getElementById('resumen_telefono'),
    resumenVehiculo: document.getElementById('resumen_vehiculo'),
    resumenPlacas: document.getElementById('resumen_placas'),
    resumenKm: document.getElementById('resumen_km'),
    resumenCombustible: document.getElementById('resumen_combustible'),
    formularioOrden: document.getElementById('formularioOrden'),
    btnGuardarOrden: document.getElementById('btnGuardarOrden')
};

// ============================================
// INICIALIZACIÓN
// ============================================
document.addEventListener('DOMContentLoaded', () => {
    inicializarEventos();
    inicializarCanvas();
    inicializarCombustible();
    console.log('✅ Sistema de Nueva Orden Inicializado');
});

function inicializarEventos() {
    if (elementos.btnBuscarCliente) {
        elementos.btnBuscarCliente.addEventListener('click', buscarCliente);
    }

    if (elementos.buscarTelefono) {
        elementos.buscarTelefono.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') { e.preventDefault(); buscarCliente(); }
        });
    }

    if (elementos.btnCrearCliente) {
        elementos.btnCrearCliente.addEventListener('click', crearCliente);
    }

    if (elementos.btnNuevoVehiculo) {
        elementos.btnNuevoVehiculo.addEventListener('click', mostrarFormularioNuevoVehiculo);
    }

    const btnAgregarManual = document.getElementById('btnAgregarServicioManual');
    if (btnAgregarManual) {
        btnAgregarManual.addEventListener('click', agregarServicioManual);
    }

    ['cliente_nombre', 'cliente_telefono', 'marca', 'modelo', 'anio', 'placas'].forEach(id => {
        document.getElementById(id)?.addEventListener('input', actualizarResumen);
    });

    document.getElementById('kilometraje_actual')?.addEventListener('input', (e) => {
        if (elementos.resumenKm) {
            elementos.resumenKm.textContent = e.target.value ? `${parseInt(e.target.value).toLocaleString()} km` : '-';
        }
    });

    if (elementos.formularioOrden) {
        elementos.formularioOrden.addEventListener('submit', guardarOrden);
    }

    const inputBuscar = document.getElementById('buscarServicio');
    if (inputBuscar) {
        inputBuscar.addEventListener('input', async (e) => {
            const termino = e.target.value.trim();
            if (termino.length < 2) return;

            const response = await fetch(`/comodin_motors/API/servicios/buscar?q=${encodeURIComponent(termino)}`);
            const data = await response.json();

            const resultados = document.getElementById('resultadosServicios');
            resultados.innerHTML = '';

            if (data.codigo === 1 && data.datos.length > 0) {
                resultados.classList.add('show');
                data.datos.forEach(servicio => {
                    const item = document.createElement('div');
                    item.className = 'autocomplete-item';
                    item.innerHTML = `
                    <div class="servicio-descripcion">${servicio.descripcion}</div>
                    <div class="servicio-precio">Q ${servicio.precio_sugerido}</div>
                `;
                    item.addEventListener('click', () => {
                        agregarServicioATabla(servicio);
                        resultados.classList.remove('show');
                        inputBuscar.value = '';
                    });
                    resultados.appendChild(item);
                });
            } else {
                resultados.classList.remove('show');
            }
        });
    }
}

// ============================================
// BÚSQUEDA Y GESTIÓN DE CLIENTES
// ============================================
async function buscarCliente() {
    const telefono = elementos.buscarTelefono.value.trim();

    if (!telefono) {
        Toast.fire({ icon: 'warning', title: 'Ingrese un teléfono' });
        return;
    }

    try {
        const response = await fetch(`/comodin_motors/API/clientes/buscar?telefono=${encodeURIComponent(telefono)}`);
        const data = await response.json();

        if (data.codigo === 1 && data.datos && data.datos.length > 0) {
            mostrarDatosCliente(data.datos[0]);
            cargarVehiculosCliente(data.datos[0].id_cliente);
        } else {
            mostrarFormularioNuevoCliente(telefono);
        }
    } catch (error) {
        console.error('Error al buscar cliente:', error);
        Toast.fire({ icon: 'error', title: 'Error al buscar cliente' });
    }
}

function mostrarDatosCliente(cliente) {
    console.log('Cliente recibido:', cliente); // ← AGREGAR ESTO
    clienteSeleccionado = cliente;

    elementos.idCliente.value = cliente.id_cliente;
    elementos.clienteNombre.value = cliente.nombre;
    elementos.clienteTelefono.value = cliente.telefono;
    elementos.clienteEmpresa.value = cliente.empresa || '';
    elementos.clienteDireccion.value = cliente.direccion || '';
    elementos.clienteNombre.readOnly = true;
    elementos.clienteTelefono.readOnly = true;

    elementos.datosCliente.style.display = 'block';
    elementos.btnNuevoCliente.style.display = 'none';
    elementos.seccionVehiculo.style.display = 'block';

    actualizarResumen();
    Toast.fire({ icon: 'success', title: 'Cliente encontrado', timer: 1500 });
}

function mostrarFormularioNuevoCliente(telefono) {
    elementos.idCliente.value = '';
    elementos.clienteNombre.value = '';
    elementos.clienteTelefono.value = telefono;
    elementos.clienteEmpresa.value = '';
    elementos.clienteDireccion.value = '';
    elementos.clienteNombre.readOnly = false;
    elementos.clienteTelefono.readOnly = false;

    elementos.datosCliente.style.display = 'block';
    elementos.btnNuevoCliente.style.display = 'block';
    elementos.seccionVehiculo.style.display = 'none';

    Toast.fire({ icon: 'info', title: 'Cliente no encontrado. Complete los datos.' });
}

async function crearCliente() {
    const nombre = elementos.clienteNombre.value.trim();
    const telefono = elementos.clienteTelefono.value.trim();
    const empresa = elementos.clienteEmpresa.value.trim();
    const direccion = elementos.clienteDireccion.value.trim();

    if (!nombre || !telefono) {
        Toast.fire({ icon: 'warning', title: 'Complete nombre y teléfono' });
        return;
    }

    try {
        const formData = new FormData();
        formData.append('nombre', nombre);
        formData.append('telefono', telefono);
        formData.append('empresa', empresa);
        formData.append('direccion', direccion);

        const response = await fetch('/comodin_motors/API/clientes/guardar', {
            method: 'POST',
            body: formData
        });
        const data = await response.json();

        if (data.codigo === 1) {
            elementos.idCliente.value = data.id_cliente;
            clienteSeleccionado = { id_cliente: data.id_cliente, nombre, telefono };

            elementos.clienteNombre.readOnly = true;
            elementos.clienteTelefono.readOnly = true;
            elementos.btnNuevoCliente.style.display = 'none';
            elementos.seccionVehiculo.style.display = 'block';

            actualizarResumen();
            mostrarFormularioNuevoVehiculo();

            Toast.fire({ icon: 'success', title: 'Cliente creado exitosamente' });
        } else {
            Toast.fire({ icon: 'error', title: data.mensaje || 'Error al crear cliente' });
        }
    } catch (error) {
        console.error('Error al crear cliente:', error);
        Toast.fire({ icon: 'error', title: 'Error al crear cliente' });
    }
}

// ============================================
// GESTIÓN DE VEHÍCULOS
// ============================================
async function cargarVehiculosCliente(id_cliente) {
    try {
        const response = await fetch(`/comodin_motors/API/vehiculos/cliente?id_cliente=${id_cliente}`);
        const data = await response.json();

        if (data.codigo === 1 && data.datos && data.datos.length > 0) {
            mostrarVehiculosExistentes(data.datos);
        } else {
            mostrarFormularioNuevoVehiculo();
        }
    } catch (error) {
        console.error('Error al cargar vehículos:', error);
        mostrarFormularioNuevoVehiculo();
    }
}

function mostrarVehiculosExistentes(vehiculos) {
    elementos.listaVehiculos.innerHTML = '';

    vehiculos.forEach(v => {
        const card = document.createElement('div');
        card.className = 'col-md-6';
        card.innerHTML = `
            <div class="vehicle-card" data-id="${v.id_vehiculo}">
                <h5 class="text-white mb-2">
                    <i class="bi bi-car-front"></i> ${v.marca} ${v.modelo}
                </h5>
                <p class="text-muted mb-1">
                    <i class="bi bi-calendar3"></i> ${v.anio} | 
                    <i class="bi bi-palette"></i> ${v.color}
                </p>
                <p class="mb-0" style="color: #00ff00; font-weight: 700;">
                    <i class="bi bi-credit-card"></i> ${v.placas}
                </p>
            </div>
        `;

        card.querySelector('.vehicle-card').addEventListener('click', (e) => {
            seleccionarVehiculo(v, card.querySelector('.vehicle-card'));
        });

        elementos.listaVehiculos.appendChild(card);
    });

    elementos.vehiculosExistentes.style.display = 'block';
    elementos.formNuevoVehiculo.style.display = 'none';
}

function seleccionarVehiculo(vehiculo, cardElement) {
    vehiculoSeleccionado = vehiculo;

    document.querySelectorAll('.vehicle-card').forEach(c => c.classList.remove('selected'));
    cardElement.classList.add('selected');

    elementos.idVehiculo.value = vehiculo.id_vehiculo;
    document.getElementById('marca').value = vehiculo.marca;
    document.getElementById('modelo').value = vehiculo.modelo;
    document.getElementById('anio').value = vehiculo.anio;
    document.getElementById('color').value = vehiculo.color;
    document.getElementById('placas').value = vehiculo.placas;
    document.getElementById('numero_serie').value = vehiculo.numero_serie || '';

    actualizarResumen();
    Toast.fire({ icon: 'success', title: 'Vehículo seleccionado', timer: 1500 });
}

function mostrarFormularioNuevoVehiculo() {
    elementos.vehiculosExistentes.style.display = 'none';
    elementos.formNuevoVehiculo.style.display = 'block';
    elementos.idVehiculo.value = '';
    document.getElementById('marca').value = '';
    document.getElementById('modelo').value = '';
    document.getElementById('anio').value = new Date().getFullYear();
    document.getElementById('color').value = '';
    document.getElementById('placas').value = '';
    document.getElementById('numero_serie').value = '';
}

// ============================================
// NIVEL DE COMBUSTIBLE
// ============================================
function inicializarCombustible() {
    if (elementos.nivelCombustible) {
        elementos.nivelCombustible.addEventListener('change', actualizarCombustible);
        actualizarCombustible();
    }
}

function actualizarCombustible() {
    const nivel = elementos.nivelCombustible.value;
    const porcentajes = { 'E': 0, '1/4': 25, '1/2': 50, '3/4': 75, 'F': 100 };

    if (elementos.fuelLevel) {
        elementos.fuelLevel.style.width = `${porcentajes[nivel] ?? 50}%`;
    }
    if (elementos.resumenCombustible) {
        elementos.resumenCombustible.textContent = nivel;
    }
}

// ============================================
// SERVICIOS Y REPUESTOS
// ============================================
function agregarServicioManual() {
    const input = document.getElementById('buscarServicio');
    const descripcion = input?.value.trim();

    agregarServicioATabla({
        descripcion: descripcion || 'Servicio manual',
        precio_sugerido: 0
    });

    if (input) input.value = '';
}

function agregarServicioATabla(servicio) {
    const tbody = document.getElementById('tablaServicios');
    const emptyRow = tbody.querySelector('.empty-state');
    if (emptyRow) emptyRow.remove();

    const index = tbody.rows.length;
    const tr = document.createElement('tr');
    tr.setAttribute('data-index', index);
    tr.innerHTML = `
        <td>${index + 1}</td>
        <td><input type="text" class="servicio-descripcion" value="${servicio.descripcion}"></td>
        <td><input type="number" class="servicio-cantidad" value="1" min="1"></td>
        <td><input type="number" class="servicio-precio" value="${servicio.precio_sugerido || 0}" min="0" step="0.01"></td>
        <td class="servicio-subtotal">Q ${parseFloat(servicio.precio_sugerido || 0).toFixed(2)}</td>
        <td>
            <button type="button" class="btn-eliminar-servicio" onclick="eliminarServicio(this)">
                <i class="bi bi-trash"></i>
            </button>
        </td>
    `;

    tr.querySelectorAll('.servicio-cantidad, .servicio-precio').forEach(input => {
        input.addEventListener('input', () => calcularSubtotal(tr));
    });

    tbody.appendChild(tr);
    calcularTotal();
}

function calcularSubtotal(fila) {
    const cantidad = parseFloat(fila.querySelector('.servicio-cantidad').value) || 0;
    const precio = parseFloat(fila.querySelector('.servicio-precio').value) || 0;
    fila.querySelector('.servicio-subtotal').textContent = `Q ${(cantidad * precio).toFixed(2)}`;
    calcularTotal();
}

function calcularTotal() {
    let total = 0;
    document.querySelectorAll('#tablaServicios tr[data-index]').forEach(fila => {
        const cantidad = parseFloat(fila.querySelector('.servicio-cantidad')?.value) || 0;
        const precio = parseFloat(fila.querySelector('.servicio-precio')?.value) || 0;
        total += cantidad * precio;
    });
    document.getElementById('totalServicios').textContent = `Q ${total.toFixed(2)}`;
}

function obtenerServiciosParaGuardar() {
    const servicios = [];
    document.querySelectorAll('#tablaServicios tr[data-index]').forEach(fila => {
        const descripcion = fila.querySelector('.servicio-descripcion')?.value?.trim();
        const cantidad = fila.querySelector('.servicio-cantidad')?.value;
        const precio = fila.querySelector('.servicio-precio')?.value;
        if (descripcion) {
            servicios.push({
                descripcion,
                cantidad: parseInt(cantidad) || 1,
                costo: parseFloat(precio) || 0
            });
        }
    });
    return servicios;
}

window.eliminarServicio = (btn) => {
    btn.closest('tr').remove();
    calcularTotal();
    const tbody = document.getElementById('tablaServicios');
    if (tbody.rows.length === 0) {
        tbody.innerHTML = `
            <tr class="empty-state">
                <td colspan="6" class="text-center" style="color: #666; padding: 2rem;">
                    <i class="bi bi-inbox" style="font-size: 3rem; display: block; margin-bottom: 1rem;"></i>
                    No hay servicios agregados
                </td>
            </tr>`;
    }
};

// ============================================
// CANVAS DE DAÑOS
// ============================================
function inicializarCanvas() {
    if (!elementos.carCanvas) return;
    const ctx = elementos.carCanvas.getContext('2d');
    dibujarVehiculo(ctx);
    elementos.carCanvas.addEventListener('click', (e) => agregarDanoEnCanvas(e, ctx));
}

function dibujarVehiculo(ctx) {
    const canvas = elementos.carCanvas;
    const w = canvas.width;
    const h = canvas.height;

    ctx.clearRect(0, 0, w, h);
    ctx.fillStyle = '#2a2a2a';
    ctx.fillRect(0, 0, w, h);

    const cx = w / 2;
    const cy = h / 2;
    const cw = 200;
    const ch = 350;

    ctx.strokeStyle = '#00ff00';
    ctx.lineWidth = 3;
    ctx.fillStyle = '#1a1a1a';

    // Cuerpo
    ctx.beginPath();
    ctx.roundRect(cx - cw / 2, cy - ch / 2, cw, ch, 20);
    ctx.fill();
    ctx.stroke();

    // Parabrisas delantero
    ctx.beginPath();
    ctx.roundRect(cx - cw / 2 + 20, cy - ch / 2 + 30, cw - 40, 70, 10);
    ctx.fill();
    ctx.stroke();

    // Ventanas
    ctx.beginPath();
    ctx.roundRect(cx - cw / 2 + 20, cy - 35, cw - 40, 70, 10);
    ctx.fill();
    ctx.stroke();

    // Parabrisas trasero
    ctx.beginPath();
    ctx.roundRect(cx - cw / 2 + 20, cy + ch / 2 - 100, cw - 40, 70, 10);
    ctx.fill();
    ctx.stroke();

    // Ruedas
    ctx.fillStyle = '#555';
    [[cx - cw / 2 - 25, cy - ch / 2 + 30], [cx + cw / 2 - 5, cy - ch / 2 + 30],
    [cx - cw / 2 - 25, cy + ch / 2 - 90], [cx + cw / 2 - 5, cy + ch / 2 - 90]].forEach(([rx, ry]) => {
        ctx.beginPath();
        ctx.roundRect(rx, ry, 30, 60, 8);
        ctx.fill();
    });

    // Etiquetas
    ctx.fillStyle = '#00ff00';
    ctx.font = 'bold 14px Arial';
    ctx.textAlign = 'center';
    ctx.fillText('FRONTAL', cx, cy - ch / 2 - 15);
    ctx.fillText('TRASERO', cx, cy + ch / 2 + 25);
    ctx.fillStyle = '#888';
    ctx.font = '12px Arial';
    ctx.fillText('IZQ', cx - cw / 2 - 35, cy);
    ctx.fillText('DER', cx + cw / 2 + 35, cy);

    // Redibujar daños
    danosRegistrados.forEach(d => dibujarMarcadorDano(ctx, d.x, d.y));
}

function dibujarMarcadorDano(ctx, x, y) {
    ctx.fillStyle = '#ff4444';
    ctx.strokeStyle = '#fff';
    ctx.lineWidth = 2;
    ctx.beginPath();
    ctx.arc(x, y, 12, 0, Math.PI * 2);
    ctx.fill();
    ctx.stroke();

    ctx.strokeStyle = '#fff';
    ctx.lineWidth = 2;
    ctx.beginPath();
    ctx.moveTo(x - 5, y - 5); ctx.lineTo(x + 5, y + 5);
    ctx.moveTo(x + 5, y - 5); ctx.lineTo(x - 5, y + 5);
    ctx.stroke();
}

async function agregarDanoEnCanvas(e, ctx) {
    const rect = elementos.carCanvas.getBoundingClientRect();
    const scaleX = elementos.carCanvas.width / rect.width;
    const scaleY = elementos.carCanvas.height / rect.height;
    const x = (e.clientX - rect.left) * scaleX;
    const y = (e.clientY - rect.top) * scaleY;

    const result = await Swal.fire({
        title: 'Registrar daño',
        html: `
            <label class="form-label">Descripción:</label>
            <textarea id="dano-descripcion" class="swal2-input" rows="3"
                style="height:auto; background:#2a2a2a; border:1px solid #3a3a3a; color:#fff;"
                placeholder="Ej: Rayón en puerta lateral izquierda"></textarea>
            <label class="form-label mt-2">Tipo:</label>
            <select id="dano-tipo" class="swal2-select"
                style="background:#2a2a2a; border:1px solid #3a3a3a; color:#fff;">
                <option value="rayón">Rayón</option>
                <option value="abolladura">Abolladura</option>
                <option value="cristal_roto">Cristal roto</option>
                <option value="faltante">Faltante</option>
                <option value="otro">Otro</option>
            </select>
        `,
        confirmButtonText: 'Agregar',
        cancelButtonText: 'Cancelar',
        showCancelButton: true,
        confirmButtonColor: '#00ff00',
        background: '#1a1a1a',
        color: '#fff',
        preConfirm: () => {
            const descripcion = document.getElementById('dano-descripcion').value.trim();
            const tipo = document.getElementById('dano-tipo').value;
            if (!descripcion) {
                Swal.showValidationMessage('La descripción es requerida');
                return false;
            }
            return { descripcion, tipo };
        }
    });

    if (result.isConfirmed) {
        danosRegistrados.push({
            id: ++danoCounter,
            x, y,
            descripcion: result.value.descripcion,
            tipo_dano: result.value.tipo,
            ubicacion: determinarUbicacion(x, y)
        });
        dibujarVehiculo(ctx);
        actualizarListaDanos();
        Toast.fire({ icon: 'success', title: 'Daño registrado' });
    }
}

function determinarUbicacion(x, y) {
    const cy = elementos.carCanvas.height / 2;
    const cx = elementos.carCanvas.width / 2;
    if (y < cy - 100) return 'frontal';
    if (y > cy + 100) return 'trasero';
    if (x < cx) return 'lateral_izquierdo';
    return 'lateral_derecho';
}

function actualizarListaDanos() {
    if (danosRegistrados.length === 0) {
        elementos.damageList.style.display = 'none';
        return;
    }
    elementos.damageList.style.display = 'block';
    elementos.damageItems.innerHTML = '';

    danosRegistrados.forEach(dano => {
        const item = document.createElement('div');
        item.className = 'damage-item';
        item.innerHTML = `
            <div class="damage-info">
                <span class="damage-badge">${dano.tipo_dano}</span>
                <strong class="d-block mt-1">${dano.descripcion}</strong>
                <small class="text-muted">${dano.ubicacion.replace(/_/g, ' ').toUpperCase()}</small>
            </div>
            <button type="button" class="btn btn-sm btn-danger" onclick="eliminarDano(${dano.id})">
                <i class="bi bi-trash"></i>
            </button>
        `;
        elementos.damageItems.appendChild(item);
    });
}

window.eliminarDano = (id) => {
    danosRegistrados = danosRegistrados.filter(d => d.id !== id);
    const ctx = elementos.carCanvas.getContext('2d');
    dibujarVehiculo(ctx);
    actualizarListaDanos();
    Toast.fire({ icon: 'info', title: 'Daño eliminado' });
};

// ============================================
// ACTUALIZAR RESUMEN
// ============================================
function actualizarResumen() {
    if (elementos.resumenCliente) elementos.resumenCliente.textContent = elementos.clienteNombre?.value || '-';
    if (elementos.resumenTelefono) elementos.resumenTelefono.textContent = elementos.clienteTelefono?.value || '-';

    const marca = document.getElementById('marca')?.value || '';
    const modelo = document.getElementById('modelo')?.value || '';
    const anio = document.getElementById('anio')?.value || '';
    if (elementos.resumenVehiculo) elementos.resumenVehiculo.textContent = marca ? `${marca} ${modelo} ${anio}` : '-';
    if (elementos.resumenPlacas) elementos.resumenPlacas.textContent = document.getElementById('placas')?.value || '-';
}

// ============================================
// GUARDAR ORDEN
// ============================================
async function guardarOrden(e) {
    e.preventDefault();

    if (!elementos.idCliente.value) {
        Swal.fire({ icon: 'warning', title: 'Cliente requerido', text: 'Debe seleccionar o crear un cliente' });
        return;
    }

    if (!elementos.idVehiculo.value && !document.getElementById('marca').value) {
        Swal.fire({ icon: 'warning', title: 'Vehículo requerido', text: 'Debe seleccionar o crear un vehículo' });
        return;
    }

    if (!validarFormulario(elementos.formularioOrden, [
        'id_cliente',
        'id_vehiculo',
        'numero_serie',
        'proximo_servicio_km',
        'cliente_empresa',
        'cliente_direccion',
        'observaciones',
        'inventario_otros',
        'buscarServicio'
    ])) {
        Swal.fire({ icon: 'warning', title: 'Campos incompletos', text: 'Complete todos los campos obligatorios' });
        return;
    }

    elementos.btnGuardarOrden.disabled = true;

    Swal.fire({
        title: 'Guardando orden...',
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
    });

    try {
        const formData = new FormData(elementos.formularioOrden);
        formData.set('id_cliente', elementos.idCliente.value);
        formData.set('id_vehiculo', elementos.idVehiculo.value);

        // DEBUG - ver exactamente qué se envía
        console.log('=== DEBUG GUARDAR ORDEN ===');
        console.log('id_cliente:', formData.get('id_cliente'));
        console.log('id_vehiculo:', formData.get('id_vehiculo'));
        console.log('marca:', formData.get('marca'));
        console.log('modelo:', formData.get('modelo'));
        console.log('placas:', formData.get('placas'));
        console.log('kilometraje_actual:', formData.get('kilometraje_actual'));
        console.log('trabajo_realizar:', formData.get('trabajo_realizar'));
        console.log('fecha_orden:', formData.get('fecha_orden'));
        console.log('hora_ingreso:', formData.get('hora_ingreso'));

        // Si no hay vehículo seleccionado, crear uno nuevo primero
        if (!formData.get('id_vehiculo') || formData.get('id_vehiculo') === '') {
            const vehiculoData = new FormData();
            vehiculoData.append('id_cliente', formData.get('id_cliente'));
            vehiculoData.append('marca', formData.get('marca'));
            vehiculoData.append('modelo', formData.get('modelo'));
            vehiculoData.append('anio', formData.get('anio'));
            vehiculoData.append('color', formData.get('color'));
            vehiculoData.append('placas', formData.get('placas'));
            vehiculoData.append('numero_serie', formData.get('numero_serie') || '');
            vehiculoData.append('kilometraje_inicial', formData.get('kilometraje_actual'));

            const respV = await fetch('/comodin_motors/API/vehiculos/guardar', {
                method: 'POST',
                body: vehiculoData
            });
            const dataV = await respV.json();

            console.log('Respuesta vehiculo:', dataV);

            if (dataV.codigo === 1) {
                formData.set('id_vehiculo', dataV.id_vehiculo);
            } else {
                throw new Error(dataV.detalle || dataV.mensaje || 'Error al crear vehículo');
            }
        }

        // Agregar servicios y daños
        formData.append('servicios', JSON.stringify(obtenerServiciosParaGuardar()));
        formData.append('danos', JSON.stringify(danosRegistrados));

        console.log('id_vehiculo final que se envía:', formData.get('id_vehiculo'));

        const response = await fetch('/comodin_motors/API/ordenes/guardar', {
            method: 'POST',
            body: formData
        });

        // Leer siempre el JSON, haya error o no
        const data = await response.json();
        console.log('Respuesta ordenes/guardar:', data);

        if (data.codigo === 1) {
            const result = await Swal.fire({
                icon: 'success',
                title: '¡Orden creada exitosamente!',
                html: `
                    <p>Número de orden:</p>
                    <strong style="color:#00ff00; font-size:2rem;">${data.numero_orden}</strong>
                `,
                showCancelButton: true,
                confirmButtonText: '<i class="bi bi-eye"></i> Ver orden',
                cancelButtonText: '<i class="bi bi-plus-circle"></i> Nueva orden',
                confirmButtonColor: '#00ff00',
                cancelButtonColor: '#6c757d',
                background: '#1a1a1a',
                color: '#fff'
            });

            if (result.isConfirmed) {
                window.location.href = `/comodin_motors/orden/ver?id=${data.id_orden}`;
            } else {
                window.location.reload();
            }
        } else {
            // Mostrar el detalle exacto del error PHP
            throw new Error(data.detalle || data.mensaje || 'Error al guardar');
        }

    } catch (error) {
        console.error('Error al guardar orden:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error al guardar',
            text: error.message || 'No se pudo guardar la orden',
            confirmButtonColor: '#ff4444',
            background: '#1a1a1a',
            color: '#fff'
        });
    } finally {
        elementos.btnGuardarOrden.disabled = false;
    }
}

console.log('✅ Script nueva.js cargado correctamente');