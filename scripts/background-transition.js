// Create background images list for transition
document.addEventListener("DOMContentLoaded", function () {
    const images = [
        "../images/background-images/background-img.jpg",
        "../images/background-images/background-img2.jpg",
        "../images/background-images/background-img3.jpg",
        "../images/background-images/background-img4.jpg",
        "../images/background-images/background-img5.jpg"
    ];
    
    // Initialize index 0 as starting background
    let index = 0;
    const section = document.querySelector(".background-search");

    // Loop and change images each
    function changeBackground() {
        index = (index + 1) % images.length;
        section.style.backgroundImage = `url('${images[index]}')`;
    }

    setInterval(changeBackground, 10000);
});