<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Navegacion</title>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
                      body {
            margin: 0;
            font-family: "Poppins", sans-serif;
        }

        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #ffffff;
            padding: 15px 40px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            z-index: 1;
        }

        .logo img {
            height: 45px;  
            width: auto;
            display: block;
        }

        /* Menú */
        .menu {
            display: flex;
            gap: 25px;
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .menu li a {
            text-decoration: none;
            color: #333;
            font-weight: 500;
            position: relative;
            transition: color 0.3s ease;
            padding: 6px 12px;
            display: flex;
            align-items: center;
            gap: 8px; /* espacio entre ícono y texto */
        }

        /* Línea debajo al hacer hover */
        .menu li a::after {
            content: "";
            position: absolute;
            width: 0;
            height: 2px;
            bottom: 0;
            left: 0;
            background-color: #F9C031;
            transition: width 0.3s;
        }

        .menu li a:hover {
            color: #F9C031;
        }

        .menu li a:hover::after {
            width: 100%;
        }

        /* Botón iniciar sesión */
        .menu li a.login {
            background: #F9C031;
            color: #fff;
            border-radius: 6px;
            padding: 8px 16px;
            transition: background 0.3s ease;
        }

        .menu li a.login::after {
            display: none; /* sin subrayado */
        }

        .menu li a.login:hover {
            background: #D89A1E;
        }

        /* Ícono hamburguesa */
        .hamburger {
            display: none;
            font-size: 30px;
            cursor: pointer;
            color: #000;   /* negro en tema claro */
            background: none;
            border: none;
            z-index: 2000;
        }

        /* Íconos dentro del menú (modo claro) */
        .menu li a i {
            color: #000;
        }

        /* Hover (ícono + texto) */
        .menu li a:hover,
        .menu li a:hover i {
            color: #F9C031;
        }

        /* ------------------- */
        /* 🔥 MODO OSCURO */
        /* ------------------- */
        body.dark-mode {
            background: #1e1e1e;
            color: #f1f1f1;
        }

        body.dark-mode nav {
            background: #2c2c2c;
        }

        /* Menú hamburguesa desplegado igual al nav */
        body.dark-mode .menu {
            background: #2c2c2c;
        }

        /* Hamburguesa blanca */
        body.dark-mode .hamburger {
            color: #fff;
        }

        /* Texto e íconos blancos */
        body.dark-mode .menu li a,
        body.dark-mode .menu li a i {
            color: #fff;
        }

        /* Hover amarillo */
        body.dark-mode .menu li a:hover,
        body.dark-mode .menu li a:hover i {
            color: #F9C031;
        }

        /* Logo blanco en modo oscuro */
        body.dark-mode .logo img {
            filter: brightness(0) invert(1);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .menu {
                position: absolute;
                top: 70px;
                right: 0;
                background: #fff;
                flex-direction: column;
                gap: 15px;
                padding: 20px;
                box-shadow: 0 5px 15px rgba(0,0,0,0.2);
                display: none;
                width: 220px;
            }

            .menu.active {
                display: flex;
                z-index: 1;
            }

            .hamburger {
                display: block;
            }
        }
    </style>
</head>
<body>
    <nav>
        <!-- Logo -->
        <a href="../index.php" class="logo">
            <img src="../imagenes/logo.png" alt="Logo">
        </a>

        <!-- Botón hamburguesa -->
        <button class="hamburger" id="hamburger">
            <i class="fa-solid fa-bars"></i>
        </button>

        <!-- Menú -->
        <ul class="menu" id="menu">
            <li><a href="../index.php"><i class="fa-solid fa-house"></i>Inicio</a></li>
            <li><a href="../html/sobre-nosotros.php"><i class="fa-solid fa-users"></i>Nosotros</a></li>
            <li><a href="../html/contacto.php"><i class="fa-solid fa-envelope"></i>Contacto</a></li>
            <li><a href="#" id="temaToggle"><i class="fa-solid fa-moon"></i>Tema</a></li>
            
            <?php if (isset($_SESSION['usuario'])): ?> 
                <li><a href="../php/cerrarsesion.php" class="login">Salir</a></li>
            <?php endif; ?>
            
        </ul>
    </nav>

    <script>
        // Menú hamburguesa
const hamburger = document.getElementById("hamburger");
const menu = document.getElementById("menu");

hamburger.addEventListener("click", () => {
    menu.classList.toggle("active");
});

// Script para cambiar ícono de modo oscuro/claro con persistencia
const temaToggle = document.getElementById("temaToggle");
const icono = temaToggle.querySelector("i");

// 🔹 Al cargar la página, verificamos el tema guardado
document.addEventListener("DOMContentLoaded", () => {
    const temaGuardado = localStorage.getItem("tema");
    if (temaGuardado === "oscuro") {
        document.body.classList.add("dark-mode");
        icono.classList.remove("fa-moon");
        icono.classList.add("fa-sun");
    }
});

// 🔹 Al hacer click, alternamos y guardamos la preferencia
temaToggle.addEventListener("click", (e) => {
    e.preventDefault();
    document.body.classList.toggle("dark-mode");

    if (document.body.classList.contains("dark-mode")) {
        icono.classList.remove("fa-moon");
        icono.classList.add("fa-sun");
        localStorage.setItem("tema", "oscuro"); // guardar
    } else {
        icono.classList.remove("fa-sun");
        icono.classList.add("fa-moon");
        localStorage.setItem("tema", "claro"); // guardar
    }
});

    </script>
</body>
</html>
