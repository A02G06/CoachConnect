document.addEventListener("DOMContentLoaded", () => {
    const marqueeText = document.getElementById("moving-text");
    let position = 0;

    function moveText() {
        position -= 2;
        if (position < -marqueeText.offsetWidth) {
            position = window.innerWidth;
        }
        marqueeText.style.transform = `translateX(${position}px)`;
        requestAnimationFrame(moveText);
    }

    moveText();
});
