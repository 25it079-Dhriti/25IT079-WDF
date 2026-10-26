
function closeNotification() {

    document.getElementById("notification").style.display = "none";

}


// Hamburger Menu

function showMenu() {

    let menu = document.getElementById("menu");

    if (menu.style.display == "block") {

        menu.style.display = "none";

    }
    else {

        menu.style.display = "block";

    }

}


function darkMode() {

    document.body.classList.toggle("dark");

}


function showAnswer() {

    let answer = document.getElementById("answer");

    if (answer.style.display == "block") {

        answer.style.display = "none";

    }
    else {

        answer.style.display = "block";

    }

}


function openModal() {

    document.getElementById("modal").style.display = "block";

}


function closeModal() {

    document.getElementById("modal").style.display = "none";

}


let images = [

    "https://picsum.photos/id/1015/400/250",

    "https://picsum.photos/id/1016/400/250",

    "https://picsum.photos/id/1018/400/250"

];


let currentImage = 0;


function nextImage() {

    currentImage++;

    if (currentImage >= images.length) {

        currentImage = 0;

    }

    document.getElementById("sliderImage").src =
        images[currentImage];

}


function previousImage() {

    currentImage--;

    if (currentImage < 0) {

        currentImage = images.length - 1;

    }

    document.getElementById("sliderImage").src =
        images[currentImage];

}