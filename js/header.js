document.addEventListener("DOMContentLoaded",function(){
    const htmlElement = document.documentElement;
    const hamburger = document.getElementById("hamburger");
    const nav = document.getElementById("header_links");
    const toggleBtn = document.getElementById("themeToggle");
    // check for setted theme from local storage
    const savedTheme = localStorage.getItem("theme");

    if (savedTheme === "dark") {
        htmlElement.setAttribute("data-theme", "dark");
    }

    if(hamburger){
        hamburger.addEventListener("click",function(){
        nav.classList.toggle('active');
        });
    }

    if(toggleBtn){
        if (savedTheme === "dark") {
            toggleBtn.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-sun-icon lucide-sun"><circle cx="12" cy="12" r="4"/><path d="M12 2v2"/><path d="M12 20v2"/><path d="m4.93 4.93 1.41 1.41"/><path d="m17.66 17.66 1.41 1.41"/><path d="M2 12h2"/><path d="M20 12h2"/><path d="m6.34 17.66-1.41 1.41"/><path d="m19.07 4.93-1.41 1.41"/></svg>`; // Change icon to Sun
        }

        toggleBtn.addEventListener("click", function() {
            
            const currentTheme = htmlElement.getAttribute("data-theme");

            if (currentTheme === "dark") {
                // Switch to Light mode 
                htmlElement.removeAttribute("data-theme");
                localStorage.setItem("theme", "light");
                toggleBtn.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-moon-icon lucide-moon"><path d="M20.985 12.486a9 9 0 1 1-9.473-9.472c.405-.022.617.46.402.803a6 6 0 0 0 8.268 8.268c.344-.215.825-.004.803.401"/></svg>`;
            } else {
                // Switch to Dark mode
                htmlElement.setAttribute("data-theme", "dark");
                localStorage.setItem("theme", "dark");
                toggleBtn.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-sun-icon lucide-sun"><circle cx="12" cy="12" r="4"/><path d="M12 2v2"/><path d="M12 20v2"/><path d="m4.93 4.93 1.41 1.41"/><path d="m17.66 17.66 1.41 1.41"/><path d="M2 12h2"/><path d="M20 12h2"/><path d="m6.34 17.66-1.41 1.41"/><path d="m19.07 4.93-1.41 1.41"/></svg>`;
            }
        });
    }

});