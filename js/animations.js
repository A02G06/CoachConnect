document.addEventListener("DOMContentLoaded", () => {
    const elements = document.querySelectorAll("main, header h1");
    elements.forEach((el) => {
        el.style.opacity = 0;
        el.style.transition = "opacity 1s ease-in-out";
        setTimeout(() => {
            el.style.opacity = 1;
        }, 200);
    });
});
                                                                       