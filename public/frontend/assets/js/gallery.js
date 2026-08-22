/*=========================================
      NACPDEAN GALLERY JAVASCRIPT
==========================================*/

document.addEventListener("DOMContentLoaded", function () {

    /*=================================
        GALLERY FILTER
    =================================*/

    const filterButtons = document.querySelectorAll(".gallery-filter button");
    const galleryItems = document.querySelectorAll(".gallery-item");

    filterButtons.forEach(button => {

        button.addEventListener("click", function () {

            filterButtons.forEach(btn => btn.classList.remove("active"));

            this.classList.add("active");

            const filter = this.getAttribute("data-filter");

            galleryItems.forEach(item => {

                item.style.opacity = "0";
                item.style.transform = "scale(.8)";

                setTimeout(() => {

                    if (filter === "all" || item.classList.contains(filter)) {

                        item.style.display = "block";

                        setTimeout(() => {

                            item.style.opacity = "1";
                            item.style.transform = "scale(1)";

                        }, 100);

                    } else {

                        item.style.display = "none";

                    }

                }, 250);

            });

        });

    });


    /*=================================
         IMAGE LOAD ANIMATION
    =================================*/

    const images = document.querySelectorAll(".gallery-card img");

    images.forEach(img => {

        img.onload = function () {

            img.style.opacity = "1";

            img.style.transform = "scale(1)";

        };

    });


    /*=================================
        COUNT UP ANIMATION
    =================================*/

    const counters = document.querySelectorAll(".stat-box h2");

    counters.forEach(counter => {

        const target = parseInt(counter.innerText);

        let count = 0;

        const speed = target / 100;

        function updateCounter() {

            if (count < target) {

                count += speed;

                counter.innerText = Math.ceil(count);

                requestAnimationFrame(updateCounter);

            } else {

                counter.innerText = target + "+";

            }

        }

        updateCounter();

    });


    /*=================================
          PARALLAX HERO
    =================================*/

    window.addEventListener("scroll", function () {

        const hero = document.querySelector(".gallery-hero");

        const scroll = window.pageYOffset;

        hero.style.backgroundPositionY = scroll * .45 + "px";

    });


    /*=================================
      SCROLL TO TOP BUTTON
    =================================*/

    const topBtn = document.createElement("button");

    topBtn.innerHTML = '<i class="bi bi-arrow-up"></i>';

    topBtn.id = "scrollTop";

    document.body.appendChild(topBtn);

    topBtn.style.position = "fixed";
    topBtn.style.right = "25px";
    topBtn.style.bottom = "25px";
    topBtn.style.width = "55px";
    topBtn.style.height = "55px";
    topBtn.style.borderRadius = "50%";
    topBtn.style.border = "none";
    topBtn.style.background = "#608C11";
    topBtn.style.color = "#fff";
    topBtn.style.fontSize = "20px";
    topBtn.style.cursor = "pointer";
    topBtn.style.display = "none";
    topBtn.style.zIndex = "9999";
    topBtn.style.boxShadow = "0 10px 25px rgba(0,0,0,.2)";
    topBtn.style.transition = ".3s";

    window.addEventListener("scroll", function () {

        if (window.scrollY > 400) {

            topBtn.style.display = "block";

        } else {

            topBtn.style.display = "none";

        }

    });

    topBtn.addEventListener("click", function () {

        window.scrollTo({

            top: 0,

            behavior: "smooth"

        });

    });


    /*=================================
          CARD HOVER SOUND EFFECT
          (Optional)
    =================================*/

    const cards = document.querySelectorAll(".gallery-card");

    cards.forEach(card => {

        card.addEventListener("mouseenter", function () {

            card.style.transition = ".35s";

        });

    });


    /*=================================
        RANDOM FLOAT EFFECT
    =================================*/

    cards.forEach(card => {

        let random = Math.random() * 6;

        card.style.animationDelay = random + "s";

    });


    /*=================================
        LIGHTBOX
    =================================*/

    if (typeof GLightbox !== "undefined") {

        GLightbox({

            selector: ".glightbox"

        });

    }


});