document.addEventListener("DOMContentLoaded", function() {
    const feedbackButton = document.querySelector(".btn[href='#']");
    
    if (feedbackButton) {
        feedbackButton.addEventListener("click", function(event) {
            event.preventDefault();
            alert("Thank you for your feedback! We appreciate your time.");
        });
    }

    // Add hover effect to change card background
    const cards = document.querySelectorAll(".link-card");

    cards.forEach(card => {
        card.addEventListener("mouseover", function() {
            card.style.backgroundColor = "#e0f7ff";
        });

        card.addEventListener("mouseout", function() {
            card.style.backgroundColor = "white";
        });
    });
});
