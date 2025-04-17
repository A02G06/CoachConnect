
document.addEventListener("DOMContentLoaded", function() {
    const buttons = document.querySelectorAll(".book-btn");

    buttons.forEach(button => {
        button.addEventListener("click", function() {
            alert("Booking feature coming soon!");
        });
    });
});
