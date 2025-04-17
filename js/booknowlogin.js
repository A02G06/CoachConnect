document.addEventListener("DOMContentLoaded", function () {
    let bookBtn = document.getElementById("bookBtn");
    let modal = document.getElementById("loginModal");
    let closeBtn = document.querySelector(".close");

    // Prevent default form submission or navigation
    bookBtn.addEventListener("click", function (event) {
        event.preventDefault();  // Stops unwanted page reload
        modal.style.display = "block";  // Shows the modal
    });

    // Close the modal when 'X' button is clicked
    closeBtn.addEventListener("click", function () {
        modal.style.display = "none";
    });

    // Close the modal if user clicks outside it
    window.addEventListener("click", function (event) {
        if (event.target === modal) {
            modal.style.display = "none";
        }
    });
});
