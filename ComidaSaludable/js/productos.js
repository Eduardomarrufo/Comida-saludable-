// Obtener elementos del DOM
const carrito = document.getElementById('carrito');
const elementos1 = document.getElementById('lista-1');
const lista = document.querySelector('#lista-carrito tbody');
const vaciarCarritoBtn = document.getElementById('vaciar-carrito');

// Cargar eventos al cargar la página
document.addEventListener('DOMContentLoaded', () => {
    cargarEventListeners();
    cargarProductosCarrito();
});

// Función para cargar los eventos
function cargarEventListeners() {
    elementos1.addEventListener('click', comprarElemento);
    carrito.addEventListener('click', eliminarElemento);
    vaciarCarritoBtn.addEventListener('click', vaciarCarrito);
}

// Función para agregar un elemento al carrito
function comprarElemento(e) {
    e.preventDefault();
    if (e.target.classList.contains('agregar-carrito')) {
        const elemento = e.target.parentElement.parentElement;
        leerDatosElemento(elemento);
    }
}

// Función para leer los datos del elemento y agregarlo al carrito
function leerDatosElemento(elemento) {
    const infoElemento = {
        imagen: elemento.querySelector('img').src,
        titulo: elemento.querySelector('h3').textContent,
        precio: elemento.querySelector('.precio').textContent,
        id: elemento.querySelector('a').getAttribute('data-id')
    }

    insertarCarrito(infoElemento);
}

// Función para insertar un elemento en el carrito
function insertarCarrito(elemento) {
    const row = document.createElement('tr');
    row.innerHTML = `
        <td>
            <img src="${elemento.imagen}" width=100>
        </td>
        <td>
            ${elemento.titulo}
        </td>
        <td>
            ${elemento.precio}
        </td>
        <td>
            <a href="#" class="borrar" data-id="${elemento.id}">X</a>
        </td>
    `;

    lista.appendChild(row);
    guardarProductosLocalStorage(elemento); // Guardar en localStorage
}

// Función para eliminar un elemento del carrito
function eliminarElemento(e) {
    e.preventDefault();
    if (e.target.classList.contains('borrar')) {
        const productoId = e.target.getAttribute('data-id');
        e.target.parentElement.parentElement.remove();
        eliminarProductoLocalStorage(productoId); // Eliminar del localStorage
    }
}

// Función para vaciar el carrito
function vaciarCarrito() {
    while (lista.firstChild) {
        lista.removeChild(lista.firstChild);
    }
    localStorage.clear(); // Limpiar localStorage
    return false;
}

// Función para obtener los productos del localStorage
function obtenerProductosLocalStorage() {
    let productos;
    if (localStorage.getItem('productos') === null) {
        productos = [];
    } else {
        productos = JSON.parse(localStorage.getItem('productos'));
    }
    return productos;
}

// Función para cargar los productos del localStorage al carrito
function cargarProductosCarrito() {
    const productos = obtenerProductosLocalStorage();

    productos.forEach((producto) => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>
                <img src="${producto.imagen}" width=100>
            </td>
            <td>
                ${producto.titulo}
            </td>
            <td>
                ${producto.precio}
            </td>
            <td>
                <a href="#" class="borrar" data-id="${producto.id}">X</a>
            </td>
        `;
        lista.appendChild(row);
    });
}

// Función para guardar los productos en el localStorage
function guardarProductosLocalStorage(producto) {
    let productos = obtenerProductosLocalStorage();
    productos.push(producto);
    localStorage.setItem('productos', JSON.stringify(productos));
}

// Función para eliminar un producto del localStorage
function eliminarProductoLocalStorage(productoId) {
    let productos = obtenerProductosLocalStorage();

    productos = productos.filter((producto) => producto.id !== productoId);

    localStorage.setItem('productos', JSON.stringify(productos));
}


// ver cuando cambia de pagina
document.querySelectorAll('.products-container-info .categorie .product').forEach(product => {
    product.addEventListener('click', () => {
        document.querySelector('.products-preview').style.display = 'flex';
        document.querySelector(`.products-preview .preview[data-target="${product.getAttribute('data-name')}"]`).classList.add('active');
    });
});

document.querySelectorAll('.products-preview .preview .fa-times').forEach(close => {
    close.addEventListener('click', () => {
        close.parentElement.classList.remove('active');
        document.querySelector('.products-preview').style.display = 'none';
    });
});
