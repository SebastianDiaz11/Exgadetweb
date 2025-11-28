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
