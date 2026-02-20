// ============================================
// NUEVA ORDEN - GESTIÓN COMPLETA
// ============================================

import { Toast, validarFormulario } from "../funciones";
import Swal from "sweetalert2";

// ============================================
// CONFIGURACIÓN GLOBAL
// ============================================


// ============================================
// VARIABLES GLOBALES
// ============================================

let clienteSeleccionado = null;
let vehiculoSeleccionado = null;
let danosVehiculo = [];
let danoCounter = 0;

// ============================================
// ELEMENTOS DEL DOM
// ============================================

const elementos = {
    // Búsqueda de cliente
    buscarTelefono: document.getElementById('buscar_telefono'),
    btnBuscarCliente: document.getElementById('btnBuscarCliente'),
    datosCliente: document.getElementById('datosCliente'),
    btnNuevoCliente: document.getElementById('btnNuevoCliente'),
    btnCrearCliente: document.getElementById('btnCrearCliente'),

    // Cliente
    idCliente: document.getElementById('id_cliente'),
    clienteNombre: document.getElementById('cliente_nombre'),
    clienteTelefono: document.getElementById('cliente_telefono'),
    clienteEmpresa: document.getElementById('cliente_empresa'),
    clienteDireccion: document.getElementById('cliente_direccion'),

    // Vehículo
    seccionVehiculo: document.getElementById('seccionVehiculo'),
    vehiculosExistentes: document.getElementById('vehiculosExistentes'),
    listaVehiculos: document.getElementById('listaVehiculos'),
    btnNuevoVehiculo: document.getElementById('btnNuevoVehiculo'),
    formNuevoVehiculo: document.getElementById('formNuevoVehiculo'),
    idVehiculo: document.getElementById('id_vehiculo'),

    // Combustible
    nivelCombustible: document.getElementById('nivel_combustible'),
    fuelLevel: document.getElementById('fuelLevel'),

    // Daños
    carCanvas: document.getElementById('carCanvas'),
    damageList: document.getElementById('damageList'),
    damageItems: document.getElementById('damageItems'),

    // Resumen
    resumenCliente: document.getElementById('resumen_cliente'),
    resumenTelefono: document.getElementById('resumen_telefono'),
    resumenVehiculo: document.getElementById('resumen_vehiculo'),
    resumenPlacas: document.getElementById('resumen_placas'),
    resumenKm: document.getElementById('resumen_km'),
    resumenCombustible: document.getElementById('resumen_combustible'),

    // Formulario
    formularioOrden: document.getElementById('formularioOrden')
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
    // Búsqueda de cliente
    if (elementos.btnBuscarCliente) {
        elementos.btnBuscarCliente.addEventListener('click', buscarCliente);
    }

    if (elementos.buscarTelefono) {
        elementos.buscarTelefono.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                buscarCliente();
            }
        });
    }

    // Crear cliente nuevo
    if (elementos.btnCrearCliente) {
        elementos.btnCrearCliente.addEventListener('click', mostrarFormularioNuevoCliente);
    }

    // Nuevo vehículo
    if (elementos.btnNuevoVehiculo) {
        elementos.btnNuevoVehiculo.addEventListener('click', mostrarFormularioNuevoVehiculo);
    }

    // Cambios en inputs para actualizar resumen
    ['cliente_nombre', 'cliente_telefono', 'marca', 'modelo', 'anio', 'placas', 'kilometraje_actual'].forEach(id => {
        const elem = document.getElementById(id);
        if (elem) {
            elem.addEventListener('input', actualizarResumen);
        }
    });

    // Envío del formulario
    if (elementos.formularioOrden) {
        elementos.formularioOrden.addEventListener('submit', guardarOrden);
    }
}

// ============================================
// BÚSQUEDA Y GESTIÓN DE CLIENTES
// ============================================

async function buscarCliente() {
    const telefono = elementos.buscarTelefono.value.trim();

    if (!telefono) {
        Toast.fire({
            icon: 'warning',
            title: 'Ingrese un teléfono'
        });
        return;
    }

    try {
        const response = await fetch(`/comodin_motors/API/clientes/buscar?telefono=${encodeURIComponent(telefono)}`);
        const data = await response.json();

        if (data.codigo === 1 && data.datos && data.datos.length > 0) {
            // Cliente encontrado
            const cliente = data.datos[0];
            mostrarDatosCliente(cliente);
            cargarVehiculosCliente(cliente.id_cliente);
        } else {
            // Cliente no encontrado
            mostrarOpcionNuevoCliente(telefono);
        }
    } catch (error) {
        console.error('Error al buscar cliente:', error);
        Toast.fire({
            icon: 'error',
            title: 'Error al buscar cliente'
        });
    }
}

function mostrarDatosCliente(cliente) {
    clienteSeleccionado = cliente;

    elementos.idCliente.value = cliente.id_cliente;
    elementos.clienteNombre.value = cliente.nombre;
    elementos.clienteTelefono.value = cliente.telefono;
    elementos.clienteEmpresa.value = cliente.empresa || '';
    elementos.clienteDireccion.value = cliente.direccion || '';

    elementos.datosCliente.style.display = 'block';
    elementos.btnNuevoCliente.style.display = 'none';
    elementos.seccionVehiculo.style.display = 'block';

    // Deshabilitar campos (cliente ya existe)
    elementos.clienteNombre.readOnly = true;
    elementos.clienteTelefono.readOnly = true;

    actualizarResumen();

    Toast.fire({
        icon: 'success',
        title: 'Cliente encontrado',
        timer: 1500
    });
}

function mostrarOpcionNuevoCliente(telefono) {
    elementos.datosCliente.style.display = 'none';
    elementos.btnNuevoCliente.style.display = 'block';

    Toast.fire({
        icon: 'info',
        title: 'Cliente no encontrado'
    });
}

function mostrarFormularioNuevoCliente() {
    const telefono = elementos.buscarTelefono.value.trim();

    elementos.clienteTelefono.value = telefono;
    elementos.clienteNombre.value = '';
    elementos.clienteEmpresa.value = '';
    elementos.clienteDireccion.value = '';

    elementos.datosCliente.style.display = 'block';
    elementos.btnNuevoCliente.style.display = 'none';
    elementos.seccionVehiculo.style.display = 'block';

    // Habilitar campos para nuevo cliente
    elementos.clienteNombre.readOnly = false;
    elementos.clienteTelefono.readOnly = false;
    elementos.clienteNombre.focus();

    // Mostrar directamente formulario de nuevo vehículo
    elementos.vehiculosExistentes.style.display = 'none';
    elementos.formNuevoVehiculo.style.display = 'block';
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

    vehiculos.forEach(vehiculo => {
        const card = document.createElement('div');
        card.className = 'col-md-6';
        card.innerHTML = `
            <div class="vehicle-card" data-vehiculo-id="${vehiculo.id_vehiculo}">
                <h5 class="text-white mb-2">
                    <i class="bi bi-car-front text-green"></i>
                    ${vehiculo.marca} ${vehiculo.modelo}
                </h5>
                <div class="text-muted">
                    <small>
                        <i class="bi bi-calendar3"></i> ${vehiculo.anio} |
                        <i class="bi bi-palette"></i> ${vehiculo.color} |
                        <i class="bi bi-credit-card"></i> ${vehiculo.placas}
                    </small>
                </div>
            </div>
        `;

        card.querySelector('.vehicle-card').addEventListener('click', () => {
            seleccionarVehiculo(vehiculo, card.querySelector('.vehicle-card'));
        });

        elementos.listaVehiculos.appendChild(card);
    });

    elementos.vehiculosExistentes.style.display = 'block';
    elementos.formNuevoVehiculo.style.display = 'none';
}

function seleccionarVehiculo(vehiculo, cardElement) {
    vehiculoSeleccionado = vehiculo;

    // Marcar visualmente
    document.querySelectorAll('.vehicle-card').forEach(card => {
        card.classList.remove('selected');
    });
    cardElement.classList.add('selected');

    // Llenar datos ocultos
    elementos.idVehiculo.value = vehiculo.id_vehiculo;
    document.getElementById('marca').value = vehiculo.marca;
    document.getElementById('modelo').value = vehiculo.modelo;
    document.getElementById('anio').value = vehiculo.anio;
    document.getElementById('color').value = vehiculo.color;
    document.getElementById('placas').value = vehiculo.placas;
    document.getElementById('numero_serie').value = vehiculo.numero_serie || '';

    actualizarResumen();

    Toast.fire({
        icon: 'success',
        title: 'Vehículo seleccionado',
        timer: 1500
    });
}

function mostrarFormularioNuevoVehiculo() {
    elementos.vehiculosExistentes.style.display = 'none';
    elementos.formNuevoVehiculo.style.display = 'block';

    // Limpiar formulario
    elementos.idVehiculo.value = '';
    document.getElementById('marca').value = '';
    document.getElementById('modelo').value = '';
    document.getElementById('anio').value = '';
    document.getElementById('color').value = '';
    document.getElementById('placas').value = '';
    document.getElementById('numero_serie').value = '';
}

// ============================================
// NIVEL DE COMBUSTIBLE
// ============================================

function inicializarCombustible() {
    if (elementos.nivelCombustible) {
        elementos.nivelCombustible.addEventListener('change', actualizarIndicadorCombustible);
        actualizarIndicadorCombustible();
    }
}

function actualizarIndicadorCombustible() {
    const nivel = elementos.nivelCombustible.value;
    const porcentajes = {
        'E': 0,
        '1/4': 25,
        '1/2': 50,
        '3/4': 75,
        'F': 100
    };

    if (elementos.fuelLevel) {
        elementos.fuelLevel.style.width = `${porcentajes[nivel] || 50}%`;
    }

    if (elementos.resumenCombustible) {
        elementos.resumenCombustible.textContent = nivel;
    }
}

// ============================================
// CANVAS DE DAÑOS DEL VEHÍCULO
// ============================================

function inicializarCanvas() {
    if (!elementos.carCanvas) return;

    const ctx = elementos.carCanvas.getContext('2d');
    dibujarVehiculo(ctx);

    elementos.carCanvas.addEventListener('click', agregarDanoEnCanvas);
}

function dibujarVehiculo(ctx) {
    const canvas = elementos.carCanvas;
    const width = canvas.width;
    const height = canvas.height;

    ctx.clearRect(0, 0, width, height);

    // Fondo
    ctx.fillStyle = '#2a2a2a';
    ctx.fillRect(0, 0, width, height);

    // Dibujar silueta simple de vehículo (vista superior)
    ctx.strokeStyle = '#00ff00';
    ctx.lineWidth = 3;

    // Carrocería principal
    ctx.beginPath();
    ctx.roundRect(200, 100, 400, 300, 20);
    ctx.stroke();

    // Parabrisas delantero
    ctx.beginPath();
    ctx.moveTo(250, 100);
    ctx.lineTo(300, 150);
    ctx.lineTo(500, 150);
    ctx.lineTo(550, 100);
    ctx.stroke();

    // Parabrisas trasero
    ctx.beginPath();
    ctx.moveTo(250, 400);
    ctx.lineTo(300, 350);
    ctx.lineTo(500, 350);
    ctx.lineTo(550, 400);
    ctx.stroke();

    // Ruedas
    ctx.fillStyle = '#1a1a1a';
    ctx.fillRect(180, 120, 30, 80); // Rueda delantera izq
    ctx.fillRect(590, 120, 30, 80); // Rueda delantera der
    ctx.fillRect(180, 300, 30, 80); // Rueda trasera izq
    ctx.fillRect(590, 300, 30, 80); // Rueda trasera der

    // Etiquetas
    ctx.fillStyle = '#00ff00';
    ctx.font = '14px Arial';
    ctx.fillText('FRENTE', 360, 80);
    ctx.fillText('ATRÁS', 360, 440);

    // Redibujar daños existentes
    danosVehiculo.forEach(dano => {
        dibujarMarcaDano(ctx, dano.x, dano.y);
    });
}

function dibujarMarcaDano(ctx, x, y) {
    ctx.fillStyle = '#ff4444';
    ctx.strokeStyle = '#fff';
    ctx.lineWidth = 2;

    ctx.beginPath();
    ctx.arc(x, y, 12, 0, 2 * Math.PI);
    ctx.fill();
    ctx.stroke();
}

function agregarDanoEnCanvas(e) {
    const rect = elementos.carCanvas.getBoundingClientRect();
    const x = e.clientX - rect.left;
    const y = e.clientY - rect.top;

    Swal.fire({
        title: 'Registrar daño',
        html: `
            <div class="text-start">
                <label class="form-label">Descripción del daño:</label>
                <textarea id="dano-descripcion" class="swal2-input" rows="3" 
                    style="height: auto; background: #2a2a2a; border: 1px solid #3a3a3a; color: #fff;"
                    placeholder="Ej: Rayón en puerta lateral izquierda"></textarea>
                
                <label class="form-label mt-3">Tipo de daño:</label>
                <select id="dano-tipo" class="swal2-select" 
                    style="background: #2a2a2a; border: 1px solid #3a3a3a; color: #fff;">
                    <option value="rayón">Rayón</option>
                    <option value="abolladura">Abolladura</option>
                    <option value="cristal_roto">Cristal roto</option>
                    <option value="faltante">Faltante</option>
                    <option value="otro">Otro</option>
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
            const descripcion = document.getElementById('dano-descripcion').value.trim();
            const tipo = document.getElementById('dano-tipo').value;

            if (!descripcion) {
                Swal.showValidationMessage('La descripción es requerida');
                return false;
            }

            return { descripcion, tipo };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const dano = {
                id: ++danoCounter,
                x: x,
                y: y,
                descripcion: result.value.descripcion,
                tipo: result.value.tipo,
                ubicacion: determinarUbicacion(x, y)
            };

            danosVehiculo.push(dano);

            const ctx = elementos.carCanvas.getContext('2d');
            dibujarVehiculo(ctx);

            actualizarListaDanos();

            Toast.fire({
                icon: 'success',
                title: 'Daño registrado'
            });
        }
    });
}

function determinarUbicacion(x, y) {
    // Determinar ubicación aproximada basada en coordenadas
    if (y < 200) return 'frontal';
    if (y > 350) return 'trasero';
    if (x < 400) return 'lateral_izquierdo';
    return 'lateral_derecho';
}

function actualizarListaDanos() {
    if (danosVehiculo.length === 0) {
        elementos.damageList.style.display = 'none';
        return;
    }

    elementos.damageList.style.display = 'block';
    elementos.damageItems.innerHTML = '';

    danosVehiculo.forEach(dano => {
        const item = document.createElement('div');
        item.className = 'damage-item';
        item.innerHTML = `
            <div class="damage-info">
                <span class="damage-badge">${dano.tipo}</span>
                <strong>${dano.descripcion}</strong>
                <small class="text-muted d-block">${dano.ubicacion}</small>
            </div>
            <button type="button" class="btn btn-sm btn-danger" onclick="eliminarDano(${dano.id})">
                <i class="bi bi-trash"></i>
            </button>
        `;
        elementos.damageItems.appendChild(item);
    });
}

window.eliminarDano = (id) => {
    danosVehiculo = danosVehiculo.filter(d => d.id !== id);

    const ctx = elementos.carCanvas.getContext('2d');
    dibujarVehiculo(ctx);

    actualizarListaDanos();

    Toast.fire({
        icon: 'info',
        title: 'Daño eliminado'
    });
};

// ============================================
// ACTUALIZAR RESUMEN
// ============================================

function actualizarResumen() {
    if (elementos.resumenCliente) {
        elementos.resumenCliente.textContent = elementos.clienteNombre.value || '-';
    }

    if (elementos.resumenTelefono) {
        elementos.resumenTelefono.textContent = elementos.clienteTelefono.value || '-';
    }

    const marca = document.getElementById('marca')?.value || '';
    const modelo = document.getElementById('modelo')?.value || '';
    const anio = document.getElementById('anio')?.value || '';

    if (elementos.resumenVehiculo) {
        elementos.resumenVehiculo.textContent = marca && modelo ? `${marca} ${modelo} ${anio}` : '-';
    }

    if (elementos.resumenPlacas) {
        elementos.resumenPlacas.textContent = document.getElementById('placas')?.value || '-';
    }

    if (elementos.resumenKm) {
        const km = document.getElementById('kilometraje_actual')?.value;
        elementos.resumenKm.textContent = km ? `${parseInt(km).toLocaleString()} km` : '-';
    }
}

// ============================================
// GUARDAR ORDEN
// ============================================

async function guardarOrden(e) {
    e.preventDefault();

    const formData = new FormData(elementos.formularioOrden);

    // Agregar servicios
    const servicios = obtenerServiciosParaGuardar();
    formData.append('servicios', JSON.stringify(servicios));

    // Agregar daños
    formData.append('danos', JSON.stringify(danosVehiculo.map(d => ({
        descripcion: d.descripcion,
        tipo_dano: d.tipo,
        ubicacion: d.ubicacion,
        coordenada_x: d.x,
        coordenada_y: d.y
    }))));

    // Mostrar loading
    Swal.fire({
        title: 'Guardando orden...',
        text: 'Por favor espere',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    try {
        const response = await fetch('/comodin_motors/API/ordenes/guardar', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();

        if (data.codigo === 1) {
            Swal.fire({
                icon: 'success',
                title: '¡Orden creada!',
                html: `
                    <p>Número de orden: <strong class="text-success">${data.numero_orden}</strong></p>
                    <p>La orden se ha guardado exitosamente</p>
                `,
                confirmButtonColor: '#00ff00',
                background: '#1a1a1a',
                color: '#fff'
            }).then(() => {
                window.location.href = `/comodin_motors/ordenes/ver?id=${data.id_orden}`;
            });
        } else {
            throw new Error(data.mensaje || 'Error al guardar');
        }
    } catch (error) {
        console.error('Error al guardar orden:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'No se pudo guardar la orden. Por favor intente nuevamente.',
            confirmButtonColor: '#ff4444',
            background: '#1a1a1a',
            color: '#fff'
        });
    }
}

console.log('✅ Script nueva.js cargado correctamente');