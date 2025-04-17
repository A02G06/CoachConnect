// Function to get query parameters from the URL
function getQueryParam(param) {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get(param);
}

// Wait for the DOM to fully load
document.addEventListener('DOMContentLoaded', () => {
    // Auto-fill trainer name from URL
    const trainerName = getQueryParam('trainer');
    if (trainerName) {
        document.getElementById('trainerName').textContent = trainerName;
        document.getElementById('trainerInput').value = trainerName;
    }

    // Form validation
    const form = document.getElementById('bookingForm');
    form.addEventListener('submit', (event) => {
        let valid = true;
        const inputs = form.querySelectorAll('input[required], select[required]');

        inputs.forEach(input => {
            if (!input.value.trim()) {
                input.style.border = "2px solid red";
                valid = false;
            } else {
                input.style.border = "";
            }
        });

        if (!valid) {
            event.preventDefault();
            alert('Please fill out all required fields.');
        }
    });
});
