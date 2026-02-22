import { Toast, validarFormulario } from "../funciones";
import Swal from "sweetalert2";

// ============================================
// ELEMENTOS DEL DOM
// ============================================
const formularioOrden = document.getElementById('formularioOrden');
const btnBuscarCliente = document.getElementById('btnBuscarCliente');
const buscarTelefono = document.getElementById('buscar_telefono');
const btnGuardarOrden = document.getElementById('btnGuardarOrden');

// Secciones
const datosCliente = document.getElementById('datosCliente');
const btnNuevoCliente = document.getElementById('btnNuevoCliente');
const btnCrearCliente = document.getElementById('btnCrearCliente');
const seccionVehiculo = document.getElementById('seccionVehiculo');
const vehiculosExistentes = document.getElementById('vehiculosExistentes');
const formNuevoVehiculo = document.getElementById('formNuevoVehiculo');
const btnNuevoVehiculo = document.getElementById('btnNuevoVehiculo');

// Canvas y daños
const carCanvas = document.getElementById('carCanvas');
const ctx = carCanvas.getContext('2d');
const damageList = document.getElementById('damageList');
const damageItems = document.getElementById('damageItems');

// Nivel de combustible
const nivelCombustible = document.getElementById('nivel_combustible');
const fuelLevel = document.getElementById('fuelLevel');

// ============================================
// ESTADO DE LA APLICACIÓN
// ============================================
let clienteSeleccionado = null;
let vehiculoSeleccionado = null;
let danosRegistrados = [];
let carImage = new Image();

// ============================================
// 1. BÚSQUEDA DE CLIENTE
// ============================================
btnBuscarCliente.addEventListener('click', async () => {
    const telefono = buscarTelefono.value.trim();

    if (!telefono) {
        Toast.fire({
            icon: 'warning',
            title: 'Ingrese un teléfono para buscar'
        });
        return;
    }

    try {
        const url = `/comodin_motors/API/orden/buscar-cliente?telefono=${encodeURIComponent(telefono)}`;
        const respuesta = await fetch(url);
        const data = await respuesta.json();

        if (data.codigo === 1 && data.datos && data.datos.length > 0) {
            // Cliente encontrado
            const cliente = data.datos[0];
            mostrarDatosCliente(cliente);
            cargarVehiculosCliente(cliente.id_cliente);
        } else {
            // Cliente no encontrado
            mostrarFormularioNuevoCliente(telefono);
        }
    } catch (error) {
        console.error('Error al buscar cliente:', error);
        Toast.fire({
            icon: 'error',
            title: 'Error al buscar cliente'
        });
    }
});

// Buscar también al presionar Enter
buscarTelefono.addEventListener('keypress', (e) => {
    if (e.key === 'Enter') {
        e.preventDefault();
        btnBuscarCliente.click();
    }
});

function mostrarDatosCliente(cliente) {
    clienteSeleccionado = cliente;

    // Llenar campos
    document.getElementById('id_cliente').value = cliente.id_cliente;
    document.getElementById('cliente_nombre').value = cliente.nombre;
    document.getElementById('cliente_telefono').value = cliente.telefono;
    document.getElementById('cliente_empresa').value = cliente.empresa || '';
    document.getElementById('cliente_direccion').value = cliente.direccion || '';

    // Deshabilitar campos (cliente ya existe)
    document.getElementById('cliente_nombre').readOnly = true;
    document.getElementById('cliente_telefono').readOnly = true;

    // Mostrar secciones
    datosCliente.style.display = 'block';
    btnNuevoCliente.style.display = 'none';
    seccionVehiculo.style.display = 'block';

    // Actualizar resumen
    actualizarResumen('cliente', cliente.nombre);
    actualizarResumen('telefono', cliente.telefono);
}

function mostrarFormularioNuevoCliente(telefono) {
    // Limpiar campos
    document.getElementById('id_cliente').value = '';
    document.getElementById('cliente_nombre').value = '';
    document.getElementById('cliente_telefono').value = telefono;
    document.getElementById('cliente_empresa').value = '';
    document.getElementById('cliente_direccion').value = '';

    // Habilitar campos para nuevo cliente
    document.getElementById('cliente_nombre').readOnly = false;
    document.getElementById('cliente_telefono').readOnly = false;

    // Mostrar formulario y botón de crear
    datosCliente.style.display = 'block';
    btnNuevoCliente.style.display = 'block';
    seccionVehiculo.style.display = 'none';

    Toast.fire({
        icon: 'info',
        title: 'Cliente no encontrado. Complete los datos para crearlo.'
    });
}

// Crear nuevo cliente
btnCrearCliente.addEventListener('click', async () => {
    const nombre = document.getElementById('cliente_nombre').value.trim();
    const telefono = document.getElementById('cliente_telefono').value.trim();
    const empresa = document.getElementById('cliente_empresa').value.trim();
    const direccion = document.getElementById('cliente_direccion').value.trim();

    if (!nombre || !telefono) {
        Toast.fire({
            icon: 'warning',
            title: 'Complete nombre y teléfono del cliente'
        });
        return;
    }

    try {
        const formData = new FormData();
        formData.append('nombre', nombre);
        formData.append('telefono', telefono);
        formData.append('empresa', empresa);
        formData.append('direccion', direccion);

        const url = '/comodin_motors/API/clientes/guardar';
        const config = {
            method: 'POST',
            body: formData
        };

        const respuesta = await fetch(url, config);
        const data = await respuesta.json();

        if (data.codigo === 1) {
            Toast.fire({
                icon: 'success',
                title: 'Cliente creado exitosamente'
            });

            // Guardar ID del nuevo cliente
            document.getElementById('id_cliente').value = data.id_cliente;
            clienteSeleccionado = {
                id_cliente: data.id_cliente,
                nombre: nombre,
                telefono: telefono
            };

            // Deshabilitar campos
            document.getElementById('cliente_nombre').readOnly = true;
            document.getElementById('cliente_telefono').readOnly = true;

            // Ocultar botón y mostrar sección de vehículo
            btnNuevoCliente.style.display = 'none';
            seccionVehiculo.style.display = 'block';

            // Actualizar resumen
            actualizarResumen('cliente', nombre);
            actualizarResumen('telefono', telefono);

            // Mostrar formulario de nuevo vehículo (cliente nuevo no tiene vehículos)
            mostrarFormularioNuevoVehiculo();
        } else {
            Toast.fire({
                icon: 'error',
                title: data.mensaje
            });
        }
    } catch (error) {
        console.error('Error al guardar cliente:', error);
        Toast.fire({
            icon: 'error',
            title: 'Error al guardar cliente'
        });
    }
});

// ============================================
// 2. GESTIÓN DE VEHÍCULOS
// ============================================
async function cargarVehiculosCliente(id_cliente) {
    try {
        const url = `/comodin_motors/API/orden/vehiculos?id_cliente=${id_cliente}`;
        const respuesta = await fetch(url);
        const data = await respuesta.json();

        if (data.codigo === 1 && data.datos && data.datos.length > 0) {
            mostrarVehiculosExistentes(data.datos);
        } else {
            // No tiene vehículos, mostrar formulario nuevo
            mostrarFormularioNuevoVehiculo();
        }
    } catch (error) {
        console.error('Error al cargar vehículos:', error);
        mostrarFormularioNuevoVehiculo();
    }
}

function mostrarVehiculosExistentes(vehiculos) {
    const contenedor = document.getElementById('listaVehiculos');
    contenedor.innerHTML = '';

    vehiculos.forEach(v => {
        const card = document.createElement('div');
        card.className = 'col-md-6';
        card.innerHTML = `
            <div class="vehicle-card" data-id="${v.id_vehiculo}">
                <h5 class="text-white mb-2">${v.marca} ${v.modelo}</h5>
                <p class="text-muted mb-1">
                    <i class="bi bi-calendar3"></i> Año: ${v.anio} | 
                    <i class="bi bi-palette"></i> Color: ${v.color}
                </p>
                <p class="mb-0" style="color: #00ff00; font-weight: 700;">
                    <i class="bi bi-credit-card"></i> Placas: ${v.placas}
                </p>
            </div>
        `;

        // Event listener para seleccionar vehículo
        card.querySelector('.vehicle-card').addEventListener('click', () => {
            seleccionarVehiculo(v);
        });

        contenedor.appendChild(card);
    });

    vehiculosExistentes.style.display = 'block';
    formNuevoVehiculo.style.display = 'none';
}

function seleccionarVehiculo(vehiculo) {
    vehiculoSeleccionado = vehiculo;

    // Remover selección previa
    document.querySelectorAll('.vehicle-card').forEach(card => {
        card.classList.remove('selected');
    });

    // Marcar como seleccionado
    event.currentTarget.classList.add('selected');

    // Guardar ID
    document.getElementById('id_vehiculo').value = vehiculo.id_vehiculo;

    // Actualizar resumen
    actualizarResumen('vehiculo', `${vehiculo.marca} ${vehiculo.modelo} ${vehiculo.anio}`);
    actualizarResumen('placas', vehiculo.placas);

    Toast.fire({
        icon: 'success',
        title: 'Vehículo seleccionado',
        timer: 1500
    });
}

function mostrarFormularioNuevoVehiculo() {
    vehiculosExistentes.style.display = 'none';
    formNuevoVehiculo.style.display = 'block';

    // Limpiar campos
    document.getElementById('marca').value = '';
    document.getElementById('modelo').value = '';
    document.getElementById('anio').value = new Date().getFullYear();
    document.getElementById('color').value = '';
    document.getElementById('placas').value = '';
    document.getElementById('numero_serie').value = '';
}

// Botón para agregar nuevo vehículo (cuando ya hay vehículos)
if (btnNuevoVehiculo) {
    btnNuevoVehiculo.addEventListener('click', () => {
        mostrarFormularioNuevoVehiculo();
    });
}

// ============================================
// 3. NIVEL DE COMBUSTIBLE VISUAL
// ============================================
nivelCombustible.addEventListener('change', (e) => {
    const nivel = e.target.value;
    let porcentaje = 50;

    switch (nivel) {
        case 'E': porcentaje = 5; break;
        case '1/4': porcentaje = 25; break;
        case '1/2': porcentaje = 50; break;
        case '3/4': porcentaje = 75; break;
        case 'F': porcentaje = 100; break;
    }

    fuelLevel.style.width = porcentaje + '%';
    actualizarResumen('combustible', nivel);
});

// ============================================
// 4. ACTUALIZAR RESUMEN LATERAL
// ============================================
function actualizarResumen(campo, valor) {
    const elemento = document.getElementById(`resumen_${campo}`);
    if (elemento) {
        elemento.textContent = valor || '-';
    }
}

// Actualizar kilometraje en resumen
document.getElementById('kilometraje_actual')?.addEventListener('input', (e) => {
    actualizarResumen('km', e.target.value ? `${e.target.value} km` : '-');
});

// ============================================
// 5. DIAGRAMA DE VEHÍCULO Y DAÑOS
// ============================================

// Cargar imagen del vehículo
carImage.src = '/comodin_motors/images/car-diagram.svg'; // O usar SVG inline
carImage.onload = () => {
    dibujarVehiculo();
};

// Si no tienes la imagen, dibujar un vehículo simple
if (!carImage.src) {
    dibujarVehiculoSimple();
}

function dibujarVehiculo() {
    ctx.clearRect(0, 0, carCanvas.width, carCanvas.height);

    // Fondo
    ctx.fillStyle = '#2a2a2a';
    ctx.fillRect(0, 0, carCanvas.width, carCanvas.height);

    // Si tienes imagen
    if (carImage.complete) {
        const aspectRatio = carImage.width / carImage.height;
        const maxWidth = carCanvas.width * 0.8;
        const maxHeight = carCanvas.height * 0.8;

        let width = maxWidth;
        let height = width / aspectRatio;

        if (height > maxHeight) {
            height = maxHeight;
            width = height * aspectRatio;
        }

        const x = (carCanvas.width - width) / 2;
        const y = (carCanvas.height - height) / 2;

        ctx.drawImage(carImage, x, y, width, height);
    } else {
        dibujarVehiculoSimple();
    }

    // Redibujar daños
    danosRegistrados.forEach(dano => {
        dibujarMarcadorDano(dano.x, dano.y);
    });
}

function dibujarVehiculoSimple() {
    ctx.clearRect(0, 0, carCanvas.width, carCanvas.height);

    // Fondo
    ctx.fillStyle = '#2a2a2a';
    ctx.fillRect(0, 0, carCanvas.width, carCanvas.height);

    ctx.strokeStyle = '#00ff00';
    ctx.lineWidth = 3;
    ctx.fillStyle = '#1a1a1a';

    const centerX = carCanvas.width / 2;
    const centerY = carCanvas.height / 2;
    const carWidth = 200;
    const carHeight = 350;

    // Cuerpo del vehículo (rectángulo redondeado)
    ctx.beginPath();
    ctx.roundRect(centerX - carWidth / 2, centerY - carHeight / 2, carWidth, carHeight, 20);
    ctx.fill();
    ctx.stroke();

    // Parabrisas
    ctx.beginPath();
    ctx.roundRect(centerX - carWidth / 2 + 20, centerY - carHeight / 2 + 30, carWidth - 40, 80, 10);
    ctx.fill();
    ctx.stroke();

    // Ventanas laterales
    ctx.beginPath();
    ctx.roundRect(centerX - carWidth / 2 + 20, centerY - 40, carWidth - 40, 80, 10);
    ctx.fill();
    ctx.stroke();

    // Parabrisas trasero
    ctx.beginPath();
    ctx.roundRect(centerX - carWidth / 2 + 20, centerY + carHeight / 2 - 110, carWidth - 40, 80, 10);
    ctx.fill();
    ctx.stroke();

    // Etiquetas
    ctx.fillStyle = '#b0b0b0';
    ctx.font = '14px Arial';
    ctx.textAlign = 'center';
    ctx.fillText('FRONTAL', centerX, centerY - carHeight / 2 - 10);
    ctx.fillText('TRASERO', centerX, centerY + carHeight / 2 + 25);
    ctx.fillText('LATERAL IZQ', centerX - carWidth / 2 - 70, centerY);
    ctx.fillText('LATERAL DER', centerX + carWidth / 2 + 70, centerY);

    // Redibujar daños
    danosRegistrados.forEach(dano => {
        dibujarMarcadorDano(dano.x, dano.y);
    });
}

function dibujarMarcadorDano(x, y) {
    ctx.fillStyle = '#ff4444';
    ctx.strokeStyle = '#fff';
    ctx.lineWidth = 3;

    ctx.beginPath();
    ctx.arc(x, y, 12, 0, Math.PI * 2);
    ctx.fill();
    ctx.stroke();

    // X en el centro
    ctx.strokeStyle = '#fff';
    ctx.lineWidth = 2;
    ctx.beginPath();
    ctx.moveTo(x - 5, y - 5);
    ctx.lineTo(x + 5, y + 5);
    ctx.moveTo(x + 5, y - 5);
    ctx.lineTo(x - 5, y + 5);
    ctx.stroke();
}

// Click en el canvas para agregar daño
carCanvas.addEventListener('click', async (e) => {
    const rect = carCanvas.getBoundingClientRect();
    const x = e.clientX - rect.left;
    const y = e.clientY - rect.top;

    // Pedir descripción del daño
    const { value: descripcion } = await Swal.fire({
        title: 'Describir daño',
        input: 'textarea',
        inputLabel: 'Descripción del daño encontrado',
        inputPlaceholder: 'Ej: Rayón en puerta delantera izquierda',
        showCancelButton: true,
        confirmButtonText: 'Agregar',
        cancelButtonText: 'Cancelar',
        inputValidator: (value) => {
            if (!value) {
                return 'Debe describir el daño';
            }
        }
    });

    if (descripcion) {
        const dano = {
            x: x,
            y: y,
            descripcion: descripcion,
            ubicacion: determinarUbicacion(x, y),
            tipo_dano: 'otro'
        };

        danosRegistrados.push(dano);
        dibujarVehiculo();
        actualizarListaDanos();
    }
});

function determinarUbicacion(x, y) {
    const centerY = carCanvas.height / 2;
    const centerX = carCanvas.width / 2;

    if (y < centerY - 100) return 'frontal';
    if (y > centerY + 100) return 'trasero';
    if (x < centerX) return 'lateral_izquierdo';
    return 'lateral_derecho';
}

function actualizarListaDanos() {
    if (danosRegistrados.length === 0) {
        damageList.style.display = 'none';
        return;
    }

    damageList.style.display = 'block';
    damageItems.innerHTML = '';

    danosRegistrados.forEach((dano, index) => {
        const item = document.createElement('div');
        item.className = 'damage-item';
        item.innerHTML = `
            <div class="damage-info">
                <span class="damage-badge">${dano.ubicacion.replace('_', ' ').toUpperCase()}</span>
                <p class="text-white mb-0 mt-2">${dano.descripcion}</p>
            </div>
            <button type="button" class="btn btn-sm" style="background: rgba(255,68,68,0.2); color: #ff4444;" onclick="eliminarDano(${index})">
                <i class="bi bi-trash"></i>
            </button>
        `;
        damageItems.appendChild(item);
    });
}

window.eliminarDano = (index) => {
    danosRegistrados.splice(index, 1);
    dibujarVehiculo();
    actualizarListaDanos();
};

// ============================================
// 6. GUARDAR ORDEN
// ============================================
formularioOrden.addEventListener('submit', async (e) => {
    e.preventDefault();
    btnGuardarOrden.disabled = true;

    // Validar cliente
    if (!document.getElementById('id_cliente').value) {
        Swal.fire({
            icon: 'warning',
            title: 'Cliente requerido',
            text: 'Debe seleccionar o crear un cliente'
        });
        btnGuardarOrden.disabled = false;
        return;
    }

    // Validar vehículo
    if (!document.getElementById('id_vehiculo').value && !document.getElementById('marca').value) {
        Swal.fire({
            icon: 'warning',
            title: 'Vehículo requerido',
            text: 'Debe seleccionar un vehículo o completar los datos de uno nuevo'
        });
        btnGuardarOrden.disabled = false;
        return;
    }

    if (!validarFormulario(formularioOrden, ['id_cliente', 'id_vehiculo', 'numero_serie', 'proximo_servicio_km'])) {
        Swal.fire({
            icon: 'warning',
            title: 'Campos incompletos',
            text: 'Complete todos los campos obligatorios'
        });
        btnGuardarOrden.disabled = false;
        return;
    }

    try {
        const formData = new FormData(formularioOrden);

        // Si no hay vehículo seleccionado, crear uno nuevo primero
        if (!formData.get('id_vehiculo')) {
            const vehiculoData = new FormData();
            vehiculoData.append('id_cliente', formData.get('id_cliente'));
            vehiculoData.append('marca', formData.get('marca'));
            vehiculoData.append('modelo', formData.get('modelo'));
            vehiculoData.append('anio', formData.get('anio'));
            vehiculoData.append('color', formData.get('color'));
            vehiculoData.append('placas', formData.get('placas'));
            vehiculoData.append('numero_serie', formData.get('numero_serie') || '');
            vehiculoData.append('kilometraje_inicial', formData.get('kilometraje_actual'));

            const respuestaVehiculo = await fetch('/comodin_motors/API/orden/guardar-vehiculo', {
                method: 'POST',
                body: vehiculoData
            });

            const dataVehiculo = await respuestaVehiculo.json();

            if (dataVehiculo.codigo === 1) {
                formData.set('id_vehiculo', dataVehiculo.id_vehiculo);
            } else {
                throw new Error('Error al crear vehículo');
            }
        }

        // Agregar daños como JSON
        if (danosRegistrados.length > 0) {
            formData.append('danos', JSON.stringify(danosRegistrados));
        }

        // Guardar orden
        const url = '/comodin_motors/API/orden/guardar';
        const config = {
            method: 'POST',
            body: formData
        };

        const respuesta = await fetch(url, config);
        const data = await respuesta.json();

        if (data.codigo === 1) {
            await Swal.fire({
                icon: 'success',
                title: '¡Orden creada exitosamente!',
                html: `
                    <p class="mb-3">Número de orden: <strong style="color: #00ff00; font-size: 1.5rem;">${data.numero_orden}</strong></p>
                    <p>La orden ha sido registrada correctamente en el sistema.</p>
                `,
                showCancelButton: true,
                confirmButtonText: '<i class="bi bi-eye"></i> Ver orden',
                cancelButtonText: '<i class="bi bi-plus-circle"></i> Nueva orden',
                confirmButtonColor: '#00ff00',
                cancelButtonColor: '#6c757d'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `/comodin_motors/ordenes/ver?id=${data.id_orden}`;
                } else {
                    window.location.reload();
                }
            });
        } else {
            Toast.fire({
                icon: 'error',
                title: data.mensaje || 'Error al guardar la orden'
            });
        }
    } catch (error) {
        console.error('Error al guardar orden:', error);
        Toast.fire({
            icon: 'error',
            title: 'Error al guardar la orden'
        });
    }

    btnGuardarOrden.disabled = false;
});

// ============================================
// INICIALIZACIÓN
// ============================================
console.log('✅ Sistema de nueva orden cargado correctamente');