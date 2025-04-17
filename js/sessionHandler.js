// Redirect to profile page if user is already logged in
document.addEventListener("DOMContentLoaded", function() {
    const isLoggedIn = sessionStorage.getItem("loggedInUser");
    const currentPath = window.location.pathname;

    if (isLoggedIn && currentPath.includes("login.html")) {
        console.log("User is logged in, redirecting to profile page."); // Debugging log
        window.location.href = "../html/profile.html";
    } else if (!isLoggedIn && currentPath.includes("profile.html")) {
        console.log("User is not logged in, redirecting to login page."); // Debugging log
        window.location.href = "../html/login.html";
    } else {
        console.log("No redirection needed."); // Debugging log
    }
});

// Logout functionality
function logout() {
    console.log("Logging out user."); // Debugging log
    sessionStorage.clear();
    window.location.href = "../html/login.html";
}

// Store user's name in sessionStorage during login
function login(userName) {
    console.log("Logging in user:", userName); // Debugging log
    sessionStorage.setItem("loggedInUser", userName);
    window.location.href = "../html/profile.html";
}