<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Footer</title>
    <style>
        .footer {
  background: #1f2937; /* gris oscuro */
  color: #fff;
  padding: 20px 10%;
  text-align: center;
  font-size: 0.9rem;
}

.footer-links {
  margin-bottom: 10px;
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
  gap: 15px;
}

.footer-links a {
  color: var(--color-principal, #fbc735);
  text-decoration: none;
  transition: color 0.3s;
}

.footer-links a:hover {
  color: var(--color-principal-hover, #d9a62a);
}

.footer-copy {
  font-size: 0.85rem;
  color: #ccc;
}

/* Responsivo */
@media (max-width: 600px) {
  .footer {
    padding: 20px;
  }
  .footer-links {
    flex-direction: column;
    gap: 8px;
  }
}

    </style>
</head>
<body>
    <footer class="footer">
        <div class="footer-links">
            <a href="./politicas_de_privacidad.php">Políticas de Privacidad</a>
            <a href="./politicas_de_cookies.php">Políticas de Cookies</a>
            <a href="./politicas_proteccion_datos_personales.php">Protección de Datos Personales</a>
            <a href="./higiene_seguridad.php">Sistema de Gestión Integrado</a>
        </div>
        <div class="footer-copy">
            <p>2025 © Exgadet S.A. | Todos los derechos reservados.</p>
        </div>
    </footer>
</body>
</html>