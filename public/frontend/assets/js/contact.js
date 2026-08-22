/*==========================================================
        NACPDEAN CONTACT PAGE JAVASCRIPT
==========================================================*/

document.addEventListener("DOMContentLoaded", function () {

    /*==============================================
                INITIALIZE AOS
    ==============================================*/

    if (typeof AOS !== "undefined") {
        AOS.init({
            duration: 900,
            once: true,
            easing: "ease-in-out"
        });
    }

    /*==============================================
            FORM VALIDATION
    ==============================================*/

    const form = document.querySelector(".contact-form-section form");

    if (form) {

        form.addEventListener("submit", function (e) {

            e.preventDefault();

            const inputs = form.querySelectorAll("input, textarea, select");

            let valid = true;

            inputs.forEach(input => {

                if (input.hasAttribute("required") && input.value.trim() === "") {

                    input.style.borderColor = "#dc3545";
                    valid = false;

                } else {

                    input.style.borderColor = "#608C11";

                }

            });

            if (!valid) {

                showToast("Please complete all required fields.", "danger");
                return;

            }

            showToast("Your message has been sent successfully!", "success");

            form.reset();

        });

    }

    /*==============================================
            TOAST NOTIFICATION
    ==============================================*/

    function showToast(message, type = "success") {

        const toast = document.createElement("div");

        toast.className = "contact-toast";

        toast.innerHTML = `
            <i class="bi ${
                type === "success"
                    ? "bi-check-circle-fill"
                    : "bi-exclamation-circle-fill"
            }"></i>
            <span>${message}</span>
        `;

        toast.style.position = "fixed";
        toast.style.top = "30px";
        toast.style.right = "30px";
        toast.style.padding = "15px 22px";
        toast.style.background =
            type === "success" ? "#608C11" : "#dc3545";
        toast.style.color = "#fff";
        toast.style.borderRadius = "12px";
        toast.style.boxShadow = "0 12px 30px rgba(0,0,0,.2)";
        toast.style.display = "flex";
        toast.style.alignItems = "center";
        toast.style.gap = "10px";
        toast.style.fontWeight = "600";
        toast.style.zIndex = "99999";
        toast.style.opacity = "0";
        toast.style.transform = "translateY(-20px)";
        toast.style.transition = ".35s";

        document.body.appendChild(toast);

        setTimeout(() => {

            toast.style.opacity = "1";
            toast.style.transform = "translateY(0)";

        }, 100);

        setTimeout(() => {

            toast.style.opacity = "0";
            toast.style.transform = "translateY(-20px)";

            setTimeout(() => {

                toast.remove();

            }, 350);

        }, 3500);

    }

    /*==============================================
            CONTACT CARD ANIMATION
    ==============================================*/

    const cards = document.querySelectorAll(".contact-card");

    cards.forEach(card => {

        card.addEventListener("mouseenter", function () {

            this.style.transform = "translateY(-12px)";

        });

        card.addEventListener("mouseleave", function () {

            this.style.transform = "translateY(0)";

        });

    });

    /*==============================================
            INPUT HIGHLIGHT
    ==============================================*/

    const controls = document.querySelectorAll(
        ".form-control, .form-select"
    );

    controls.forEach(control => {

        control.addEventListener("focus", function () {

            this.parentElement.style.transform = "translateY(-3px)";

        });

        control.addEventListener("blur", function () {

            this.parentElement.style.transform = "translateY(0)";

        });

    });

    /*==============================================
            SMOOTH BUTTON HOVER
    ==============================================*/

    const buttons = document.querySelectorAll(
        ".btn-contact, .btn-join"
    );

    buttons.forEach(button => {

        button.addEventListener("mouseenter", function () {

            this.style.transition = ".35s";

        });

    });

    /*==============================================
            SCROLL TO TOP
    ==============================================*/

    const topBtn = document.createElement("button");

    topBtn.innerHTML = '<i class="bi bi-arrow-up"></i>';

    topBtn.id = "scrollTopBtn";

    document.body.appendChild(topBtn);

    Object.assign(topBtn.style, {

        position: "fixed",
        bottom: "30px",
        right: "30px",
        width: "55px",
        height: "55px",
        borderRadius: "50%",
        border: "none",
        background: "#608C11",
        color: "#fff",
        fontSize: "20px",
        cursor: "pointer",
        display: "none",
        zIndex: "9999",
        boxShadow: "0 15px 30px rgba(0,0,0,.2)",
        transition: ".3s"

    });

    window.addEventListener("scroll", function () {

        if (window.scrollY > 350) {

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

    /*==============================================
            FAQ ANIMATION
    ==============================================*/

    const accordionButtons = document.querySelectorAll(".accordion-button");

    accordionButtons.forEach(button => {

        button.addEventListener("click", function () {

            this.style.transition = ".3s";

        });

    });

    /*==============================================
            IMAGE PARALLAX
    ==============================================*/

    const hero = document.querySelector(".contact-hero");

    if (hero) {

        window.addEventListener("scroll", function () {

            let offset = window.pageYOffset;

            hero.style.backgroundPositionY = offset * 0.4 + "px";

        });

    }

});