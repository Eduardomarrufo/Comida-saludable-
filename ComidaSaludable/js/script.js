document.addEventListener('DOMContentLoaded', function() {
    const carrito = document.getElementById('carrito');
    const lista = document.querySelector('#lista-carrito tbody');
    const vaciarCarritoBtn = document.getElementById('vaciar-carrito');

    cargarEventListeners();
    cargarProductosCarrito();

    function cargarEventListeners() {
        // Evento para agregar producto desde cualquier categoría
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('agregar-carrito')) {
                e.preventDefault();
                const elemento = e.target.parentElement.parentElement;
                leerDatosElemento(elemento);
            }
        });

        // Evento para eliminar producto del carrito
        carrito.addEventListener('click', function(e) {
            if (e.target.classList.contains('borrar')) {
                e.preventDefault();
                const productoId = e.target.getAttribute('data-id');
                e.target.parentElement.parentElement.remove();
                eliminarProductoLocalStorage(productoId);
            }
        });

        // Evento para vaciar el carrito
        vaciarCarritoBtn.addEventListener('click', function(e) {
            e.preventDefault();
            while (lista.firstChild) {
                lista.removeChild(lista.firstChild);
            }
            localStorage.removeItem('productos'); 
            return false;
        });
    }

    function leerDatosElemento(elemento) {
        const infoElemento = {
            imagen: elemento.querySelector('img').src,
            titulo: elemento.querySelector('h1').textContent,
            precio: elemento.querySelector('.precio').textContent,
            id: elemento.getAttribute('data-id')
        }
        insertarCarrito(infoElemento);
    }

    function insertarCarrito(elemento) {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td><img src="${elemento.imagen}" width="100"></td>
            <td>${elemento.titulo}</td>
            <td>${elemento.precio}</td>
            <td><a href="#" class="borrar" data-id="${elemento.id}">X</a></td>
        `;
        lista.appendChild(row);
        guardarProductosLocalStorage(elemento);
    }

    function eliminarProductoLocalStorage(productoId) {
        let productos = obtenerProductosLocalStorage();
        productos = productos.filter(producto => producto.id !== productoId);
        localStorage.setItem('productos', JSON.stringify(productos));
    }

    function vaciarCarrito() {
        while (lista.firstChild) {
            lista.removeChild(lista.firstChild);
        }
        localStorage.removeItem('productos'); 
        return false;
    }

    function obtenerProductosLocalStorage() {
        return JSON.parse(localStorage.getItem('productos')) || [];
    }

    function cargarProductosCarrito() {
        const productos = obtenerProductosLocalStorage();
        productos.forEach(producto => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td><img src="${producto.imagen}" width="100"></td>
                <td>${producto.titulo}</td>
                <td>${producto.precio}</td>
                <td><a href="#" class="borrar" data-id="${producto.id}">X</a></td>
            `;
            lista.appendChild(row);
        });
    }

    function guardarProductosLocalStorage(producto) {
        let productos = obtenerProductosLocalStorage();
        productos.push(producto);
        localStorage.setItem('productos', JSON.stringify(productos));
    }
});

document.addEventListener('DOMContentLoaded', function() {
    var swiper1 = new Swiper(".mySwiper-1", {
        loop: true,
        navigation: {
            nextEl: ".swiper-button-next",
            prevEl: ".swiper-button-prev",
        },
        pagination: {
            el: ".swiper-pagination",
            clickable: true,
        },
    });
});
