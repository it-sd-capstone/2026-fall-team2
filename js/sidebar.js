const toggle = document.getElementById("sidebar-toggle");

toggle.addEventListener("click", function () {
    document.body.classList.toggle("sidebar-closed");
});