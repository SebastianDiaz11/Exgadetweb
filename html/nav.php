<?php

?>
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
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
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
            gap: 8px;
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

        /* Botón iniciar sesión o perfil */
        .menu li a.login,
        .menu li a.perfil {
            background: #F9C031;
            color: #fff;
            border-radius: 6px;
            padding: 8px 16px;
            transition: background 0.3s ease;
        }

        .menu li a.login::after,
        .menu li a.perfil::after {
            display: none;
        }

        .menu li a.login:hover,
        .menu li a.perfil:hover {
            background: #D89A1E;
        }

        /* Ícono hamburguesa */
        .hamburger {
            display: none;
            font-size: 30px;
            cursor: pointer;
            color: #000;
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

        body.dark-mode .menu {
            background: #2c2c2c;
        }

        body.dark-mode .hamburger {
            color: #fff;
        }

        body.dark-mode .menu li a,
        body.dark-mode .menu li a i {
            color: #fff;
        }

        body.dark-mode .menu li a:hover,
        body.dark-mode .menu li a:hover i {
            color: #F9C031;
        }

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
                box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
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
            <li><a href="./sobre-nosotros.php"><i class="fa-solid fa-users"></i>Nosotros</a></li>
            <li><a href="./contacto.php"><i class="fa-solid fa-envelope"></i>Contacto</a></li>
            <li><a href="#" id="temaToggle"><i class="fa-solid fa-moon"></i>Tema</a></li>

            <?php if (isset($_SESSION["usuario"])): ?>
                <!-- Si el usuario está logueado -->
                <li><a href="../htmlintranet/usuario.php" class="perfil"><i class="fa-solid fa-user"></i>Mi Perfil</a></li>
            <?php else: ?>
                <!-- Si el usuario NO está logueado -->
                <li><a href="./login.php" class="login"><i class="fa-solid fa-right-to-bracket"></i>Iniciar sesión</a></li>
            <?php endif; ?>
        </ul>
    </nav>

    <script src="../js/menu_hambur-modo.js"></script>
</body>

</html>
