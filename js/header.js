console.log("loaded");

document.addEventListener("DOMContentLoaded",function(){
    const hamburger = document.getElementById("hamburger");
    const nav = document.getElementById("header_links");

    hamburger.addEventListener("click",function(){
        nav.classList.toggle('active');
    })
});

document.addEventListener("DOMContentLoaded", function() {
    const toggleBtn = document.getElementById("themeToggle");
    const htmlElement = document.documentElement; // Selects the <html> tag

    // 1. Check Local Storage on Load
    // If the user previously chose dark, apply it immediately
    const savedTheme = localStorage.getItem("theme");
    if (savedTheme === "dark") {
        htmlElement.setAttribute("data-theme", "dark");
        toggleBtn.textContent = "☀️"; // Change icon to Sun
    }

    // 2. Listen for Click
    if(toggleBtn){
        toggleBtn.addEventListener("click", function() {
            
            // Check current mode
            const currentTheme = htmlElement.getAttribute("data-theme");

            if (currentTheme === "dark") {
                // Switch to Light
                htmlElement.removeAttribute("data-theme");
                localStorage.setItem("theme", "light");
                toggleBtn.textContent = "🌙";
            } else {
                // Switch to Dark
                htmlElement.setAttribute("data-theme", "dark");
                localStorage.setItem("theme", "dark");
                toggleBtn.textContent = "☀️";
            }
        });
    }
});