window.addEventListener("DOMContentLoaded", (event) => {

  ///////////////////
  // ACCESSIBILITY //
  ///////////////////

  let lowFont = document.getElementById('low-font');
  let normalFont = document.getElementById('reset-font');
  let highFont = document.getElementById('high-font');

  let t;

  if (localStorage.getItem('fontSize')) {
    t = parseFloat(localStorage.getItem('fontSize'));
    document.body.style.fontSize = t + "rem";
  }
  else {
    t = 1;
  }

  function highSize(modif) {
    if (t < 2) {
      t = t + modif;
      localStorage.setItem('fontSize', t);
      document.body.style.fontSize = t + "rem";
    }
    else {
      t = 2;
    }
  }

  function lowSize(modif) {
    if (t > 0.6) {
      t = t - modif;
      localStorage.setItem('fontSize', t);
      document.body.style.fontSize = t + "rem";
    }
    else {
      t = 0.6;
    }
  }

  function normalSize() {
    t = 1;
    localStorage.setItem('fontSize', t);
    document.body.style.fontSize = t + "rem";
  }

  highFont.onclick = () => highSize(0.2);
  lowFont.onclick = () => lowSize(0.2);
  normalFont.onclick = () => normalSize();

  // DARK MODE

  // Select the button
  let darkBtn = document.getElementById('dark-mode');
  // Select the theme preference from localStorage
  const currentTheme = localStorage.getItem("theme"); 
  // If the current theme in localStorage is "dark"...
  if (currentTheme == "dark") {
    // ...then use the .dark-theme class
    document.body.classList.add("darkMode");
  } 
  // Listen for a click on the button 
  darkBtn.addEventListener("click", function() {
    // Toggle the .dark-theme class on each click
    document.body.classList.toggle("darkMode");

    // Let's say the theme is equal to light
    let theme = "light";
    // If the body contains the .dark-theme class...
    if (document.body.classList.contains("darkMode")) {
      // ...then let's make the theme dark
      theme = "dark";
    }
    // Then save the choice in localStorage
    localStorage.setItem("theme", theme);
  });


  // Hamburger Menu // 
  const menu = document.querySelector(".navbar");
  const links = document.querySelectorAll(".navbar--link");
  const hamburger = document.querySelector(".hamburger");
  const closeIcon = document.querySelector(".closeIcon");
  const menuIcon = document.querySelector(".menuIcon");

  // On click : add/remove class showMenu
  const toggleMenu = () => {
    if (menu.classList.contains("showMenu")) {
      menu.classList.remove("showMenu");
      // Change icon style -> closeIcon : cross ; menuIcon : hamburger
      closeIcon.style.display = "none";
      menuIcon.style.display = "block";
    }
    else {
      menu.classList.add("showMenu");
      closeIcon.style.display = "block";
      menuIcon.style.display = "none";
    }
  }

  hamburger.onclick = () => toggleMenu();

  // Testimonials // 

  let swiper = new Swiper(".mySwiper", {
    navigation: {
      nextEl: ".swiper-button-next",
      prevEl: ".swiper-button-prev",
    },
  });

  // ACCOUNT PAGE //

  /* ***** Hide user's infos ***** */

  let accountBtn = document.getElementById('accountInfo');
  let userInfos = document.getElementById('accountDetails');
  let userAddress = document.getElementById('accountAddressDetails');
  let addressBtn = document.getElementById('accountAddress');

  const displayInfos = () => {
    if (userInfos.classList.contains("showMenu")) {
      userInfos.classList.remove("showMenu");
    }
    else {
      userInfos.classList.add("showMenu");
    }
  };

  const displayAddress = () => {
    if (userAddress.classList.contains("showMenu")) {
      userAddress.classList.remove("showMenu");
    }
    else {
      userAddress.classList.add("showMenu");
    }
  };

  accountBtn ? accountBtn.onclick = () => displayInfos() : null;
  addressBtn ? addressBtn.onclick = () => displayAddress() : null;

});

// Products page : Toggle details

window.addEventListener('load', () => {
  if (screen.availWidth > 1007) {
    document.getElementById('details') ? document.getElementById('details').setAttribute("open", "") : null;
  }
  else {
    document.getElementById('details') ? document.getElementById('details').removeAttribute("open") : null;
  }
});

//
