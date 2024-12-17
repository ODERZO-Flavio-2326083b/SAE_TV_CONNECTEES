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
function displayOrHide(slides, slideIndex)
{
    if(slides.length > 0) {
        if(slides.length > 1) {
            for (let i = 0; i < slides.length; ++i) {
                slides[i].style.display = "none";
            }
        }

        if(slideIndex === slides.length) {
            console.log("-Fin du diaporama - On recommence");
            slideIndex = 0;
        }

        // Vérifier si la diapositive contient une vidéo avec la classe 'video_skip'
        if (slides[slideIndex].querySelector('.video_container')) {
            console.log("Vidéo format paysage ignorée, passage à la diapositive suivante.");
            // Passer à la slide suivante
            slideIndex++;

            setTimeout(function (){displayOrHide(slides, slideIndex)}, 0);

        }



        // Check if the slide exist
        if(slides[slideIndex] !== undefined) {

            console.log("--Slide n°"+ slideIndex);
            // Display the slide
            slides[slideIndex].style.display = "block";

            // Check child
            if(slides[slideIndex].childNodes) {
                var count = 0;
                // Try to find if it's a PDF
                for(let i = 0; i < slides[slideIndex].childNodes.length; ++i) {
                    // If is a PDF
                    if(slides[slideIndex].childNodes[i].className === 'canvas_pdf') {

                        console.log("--Lecture de PDF");
                        count++;
                        let pdfLink = slides[slideIndex].childNodes[i].id;
                        pdfUrl = urlUpload + pdfLink;

                        let loadingTask = pdfjsLib.getDocument(pdfUrl);
                        loadingTask.promise.then(function(pdf) {

                            totalPage = pdf.numPages;
                            ++numPage;

                            let div = document.getElementById(pdfLink);
                            let scale = 1.5;

                            console.log(div);

                            if(stop === false) {
                                if(document.getElementById('the-canvas-' + pdfLink + '-page' + (numPage-1)) != null) {
                                    console.log('----Supression page n°'+ (numPage-1));
                                    document.getElementById('the-canvas-' + pdfLink + '-page' + (numPage-1)).remove();
                                }
                            }

                            if(totalPage >= numPage && stop === false) {
                                pdf.getPage(numPage).then(function(page) {

                                    console.log(slides.length);
                                    console.log(totalPage);
                                    if(slides.length === 1 && totalPage === 1 || totalPage === null && slides.length === 1) {
                                        stop = true;
                                    }

                                    console.log(stop);

                                    console.log("---Page du PDF n°"+numPage);

                                    let viewport = page.getViewport({ scale: scale, });

                                    $('<canvas >', {
                                        id: 'the-canvas-'+ pdfLink + '-page' + numPage,
                                    }).appendTo(div).fadeOut(0).fadeIn(2000);

                                    let canvas = document.getElementById('the-canvas-' + pdfLink + '-page' + numPage);
                                    let context = canvas.getContext('2d');
                                    canvas.height = viewport.height;
                                    canvas.width = viewport.width;

                                    let renderContext = {
                                        canvasContext: context,
                                        viewport: viewport
                                    };

                                    // Give the CSS to the canvas
                                    if(slides === document.getElementsByClassName("mySlides")) {
                                        canvas.style.maxHeight = "99vh";
                                        canvas.style.maxWidth = "100%";
                                        canvas.style.height = "99vh";
                                        canvas.style.width = "auto";
                                        canvas.style.padding = "20px";

                                    } else {
                                        canvas.style.maxHeight = "68vh";
                                        canvas.style.maxWidth = "100%";
                                        canvas.style.height = "auto";
                                        canvas.style.width = "auto";
                                        canvas.style.padding = "20px";
                                    }

                                    page.render(renderContext);
                                });

                                if(numPage === totalPage) {
                                    // Reinitialise variables
                                    if(stop === false) {
                                        if(document.getElementById('the-canvas-' + pdfLink + '-page' + (totalPage)) != null) {
                                            document.getElementById('the-canvas-' + pdfLink + '-page' + (totalPage)).remove();
                                        }
                                    }
                                    console.log("--Fin du PDF");
                                    totalPage = null;
                                    numPage = 0;
                                    endPage = true;
                                    ++slideIndex;
                                    // Go to the next slide
                                }
                            }
                        });

                    }

                    // Si la diapositive contient une vidéo
                    else if (slides[slideIndex].childNodes[i].className === 'short_container') {
                        console.log("--Lecture de vidéo");
                        count++;
                        // Génère l'URL pour la gestion de la vidéo
                        let videoLink = slides[slideIndex].childNodes[i].id;
                        videoUrl = urlUpload + videoLink;
                        let video = document.createElement('video');
                        video.src = videoUrl;
                        video.style.maxHeight = "68vh";
                        video.style.maxWidth = "100%";
                        video.style.height = "auto";
                        video.style.width = "auto";
                        video.loop = true;  // La vidéo ne boucle pas, elle se termine après la lecture
                        console.log(video);
                        ++slideIndex;

                    }
                }
                if(count === 0) {
                    console.log("--Lecture image");

                    // Go to the next slide
                    ++slideIndex;
                }
            } else {
                // Go to the next slide
                ++slideIndex;
            }
        }
    }

    if(slides.length !== 1 || totalPage !== 1) {
        setTimeout(function(){displayOrHide(slides, slideIndex)} , 10000);
    }
}

