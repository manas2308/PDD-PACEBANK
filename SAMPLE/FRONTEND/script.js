document.addEventListener("DOMContentLoaded", function () {
    const dropdown = document.querySelector(".dropdown");
    
    dropdown.addEventListener("click", function (event) {
        event.stopPropagation();
        this.querySelector(".dropdown-menu").classList.toggle("show");
    });

    document.addEventListener("click", function () {
        document.querySelector(".dropdown-menu").classList.remove("show");
    });
});
