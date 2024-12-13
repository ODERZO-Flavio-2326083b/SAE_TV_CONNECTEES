let urlUpload = "/wp-content/uploads/media/";
let pdfUrl = null; // Lien PDF, vide pour l'instant
let videoUrl = null; // Lien video, vide pour l'instant
let numPage = 0; // Numéro de page courante
let totalPage = null; // Nombre de pages
let endPage = false; // Fin de page
let stop = false;

infoSlideShow();
scheduleSlideshow();

/**
 * Lance le diaporama si des slides d'informations sont présentes
 */
function infoSlideShow() {
    if (document.getElementsByClassName("myInfoSlides").length > 0) {
        console.log("-Début du diaporama");
        displayOrHide(document.getElementsByClassName("myInfoSlides"), 0);
    }
}

/**
 * Lance le diaporama si des slides sont présentes
 */
function scheduleSlideshow() {
    if (document.getElementsByClassName("mySlides").length > 0) {
        console.log("-Début du diaporama");
        displayOrHide(document.getElementsByClassName("mySlides"), 0);
    }
}

/**
 * Affiche le diaporama
 * @param slides
 * @param slideIndex
 */
function displayOrHide(slides, slideIndex) {
    if (slides.length > 0) {
        // Cache toutes les slides
        for (let i = 0; i < slides.length; ++i) {
            slides[i].style.display = "none";
        }

        // Réinitialise slideIndex si il dépasse le nombre de slides
        if (slideIndex === slides.length) {
            console.log("-Fin du diaporama - On recommence");
            slideIndex = 0;
        }

        // Affiche la slide actuelle si elle existe
        if (slides[slideIndex] !== undefined) {
            console.log("--Slide n°" + slideIndex);
            slides[slideIndex].style.display = "block";

            // Vérifie si il y a des noeuds enfants dans la slide actuelle
            let count = 0;
            let isVideo = false;
            let duration = 10000;
            for (let i = 0; i < slides[slideIndex].childNodes.length; ++i) {
                let currentNode = slides[slideIndex].childNodes[i];

                // Si la diapositive contient un PDF
                if (currentNode.className === 'canvas_pdf') {
                    console.log("--Lecture de PDF");
                    count++;
                    // Génère l'URL pour la gestion du PDF
                    let pdfUrl = currentNode.id;
                    handlePDF(pdfUrl);
                }
                // Si la diapositive contient une vidéo
                else if (currentNode.className === 'video_container') {
                    isVideo = true; // Indique qu'une vidéo est présente
                    console.log("--Lecture de vidéo");
                    count++;
                    // Génère l'URL pour la gestion de la vidéo
                    let videoUrl = currentNode.id;
                    handleVideo(videoUrl); // Passe l'ID de la vidéo à handleVideo
                }
            }

            if (count === 0) {
                console.log("--Lecture image");
            }

                // Si aucune vidéo n'est présente, on passe à la diapositive suivante immédiatement
                setTimeout(function () {
                    displayOrHide(slides, slideIndex + 1);
                }, duration); // 5 secondes pour les autres contenus
        }
    }


    /**
     * Gestion des PDF dans la diaporama
     * @param pdfLink
     */
    function handlePDF(pdfLink) { // Changer le paramètre pour pdfLink
        let loadingTask = pdfjsLib.getDocument(urlUpload + pdfLink);
        loadingTask.promise.then(function (pdf) {
            totalPage = pdf.numPages;
            numPage++;

            let div = document.getElementById(pdfLink);
            let scale = 1.5;

            if (!stop) {
                if (numPage > 1) {
                    console.log('----Suppression de la page n°' + (numPage - 1));
                    document.getElementById('the-canvas-' + pdfLink + '-page' + (numPage - 1))?.remove();
                }
            }

            if (totalPage >= numPage && !stop) {
                pdf.getPage(numPage).then(function (page) {
                    console.log("---Page du PDF n°" + numPage);

                    let viewport = page.getViewport({scale: scale});

                    // Crée et ajoute un canevas
                    let canvas = document.createElement('canvas');
                    canvas.id = 'the-canvas-' + pdfLink + '-page' + numPage;
                    div.appendChild(canvas);
                    $(canvas).fadeOut(0).fadeIn(2000); // Animation d'apparition

                    let context = canvas.getContext('2d');
                    canvas.height = viewport.height;
                    canvas.width = viewport.width;

                    let renderContext = {
                        canvasContext: context,
                        viewport: viewport
                    };

                    // Appliquer le style CSS en fonction du type de diaporama
                    if (div.classList.contains("mySlides")) {
                        canvas.style.maxHeight = "99vh";
                        canvas.style.maxWidth = "100%";
                        canvas.style.height = "99vh";
                        canvas.style.width = "auto";
                    } else {
                        canvas.style.maxHeight = "68vh";
                        canvas.style.maxWidth = "100%";
                        canvas.style.height = "auto";
                        canvas.style.width = "auto";
                    }

                    // Rendu de la page PDF sur le canvas
                    page.render(renderContext);
                });

                if (numPage === totalPage) {
                    console.log("--Fin du PDF");
                    totalPage = null;
                    numPage = 0;
                    endPage = true;
                    stop = true; // Assurez-vous que le diaporama s'arrête après la dernière page
                }
            }
        });
    }


    /**
     * Gestion des vidéos (short) dans le diaporama
     * @param videoLink
     */
    function handleVideo(videoLink) {
        let video = document.createElement('video');
        video.src = urlUpload + videoLink;
        video.style.maxHeight = "68vh";
        video.style.maxWidth = "100%";
        video.style.height = "auto";
        video.style.width = "auto";
        video.loop = false;  // La vidéo ne boucle pas, elle se termine après la lecture


    }

    /**
     * Renvoie la durée de la vidéo
     * @param videoLink
     */
    function getVideoDuration(videoLink) {
        let video = document.createElement('video');
        video.src = urlUpload + videoLink;
        return video.duration * 1000;
    }
}