let urlUpload = "/wp-content/uploads/media/";
let pdfUrl = null;
let numPage = 0; // Numéro de page courante
let totalPage = null; // Nombre de pages
let endPage = false;
let stop = false;

infoSlideShow();
videoSlideshow();
scheduleSlideshow();
init();

function init(){
    document.addEventListener('DOMContentLoaded', () => {
        setupVideoSlideshow();
    });
}




// Fonction : Configurer le diaporama vidéo
function setupVideoSlideshow() {
    const video = document.querySelector('.video_container'); // Vidéo principale
    const videoContainer = video.closest('.myVideoSlides'); // Conteneur parent
    const tableContainer = document.querySelector('.table-responsive'); // Tableau emploi du temps
    // Vérifier si le tableau est présent
    const hasTable = tableContainer && tableContainer.querySelector('.tabSchedule');
    console.log(hasTable);
    // Ajouter un écouteur d'événement à la vidéo
    video.addEventListener('ended', () => handleVideoEnd(videoContainer, tableContainer, noCourseContainer, hasTable));
}

// Fonction : Gestion de la fin de la vidéo
function handleVideoEnd(videoContainer, tableContainer, noCourseContainer, hasTable) {
    videoContainer.style.display = 'none';
    console.log("-- Vidéo disparue")

    if (hasTable) {
        // Si le tableau est présent, l'afficher
        showElement(tableContainer);
        console.log("-- EDT apparu")
    }
}

// Fonction utilitaire : Afficher un élément
function showElement(element) {
    element.style.display = 'block';
    element.classList.add('show'); // Utiliser la classe CSS pour des transitions (facultatif)
}
/**
 * Lance le diaporama s'il existe des informations
 */
function infoSlideShow()
{
    if(document.getElementsByClassName("myInfoSlides").length > 0) {
        console.log("-Début du diaporama");
        displayOrHide(document.getElementsByClassName("myInfoSlides"), 0);
    }
}

/**
 * Lance le diaporama s'il existe des vidéos
 */
function videoSlideshow(){
    if (document.getElementsByClassName("myVideoSlides").length > 0){
        console.log("-Début du diaporama vidéo");
        displayOrHideVideo(document.getElementsByClassName("myVideoSlides"), 0);
    }
}

/**
 * Lance le diaporama s'il existe des informations
 */
function scheduleSlideshow()
{
    if(document.getElementsByClassName("mySlides").length > 0) {
        console.log("-Début du diaporama");
        displayOrHide(document.getElementsByClassName("mySlides"), 0);
    }
}

/**
 * Affiche un diaporama
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


        // On vérifie qu'il existe une dernière slide
        if(slides[slideIndex] !== undefined) {

            console.log("--Slide n°"+ slideIndex);

            slides[slideIndex].style.display = "block";
            // On vérifie qu'un enfant existe
            if(slides[slideIndex].childNodes) {
                let count = 0;
                for(let i = 0; i < slides[slideIndex].childNodes.length; ++i) {
                    let child = slides[slideIndex].childNodes[i];
                    // Si c'est un PDF
                    if(child.className === 'canvas_pdf') {

                        console.log("--Lecture de PDF");

                        count = count + 1;

                        // On génère l'URL
                        let pdfLink = slides[slideIndex].childNodes[i].id;
                        pdfUrl = urlUpload + pdfLink;

                        let loadingTask = pdfjsLib.getDocument(pdfUrl);
                        loadingTask.promise.then(function(pdf) {

                            totalPage = pdf.numPages;
                            ++numPage;

                            let div = document.getElementById(pdfLink);
                            let scale = 1.5;

                            if(stop === false) {
                                if(document.getElementById('the-canvas-' + pdfLink + '-page' + (numPage-1)) != null) {
                                    console.log('----Supression page n°'+ (numPage-1));
                                    document.getElementById('the-canvas-' + pdfLink + '-page' + (numPage-1)).remove();
                                }
                            }

                            if(totalPage >= numPage && stop === false) {
                                pdf.getPage(numPage).then(function(page) {
                                    if(slides.length === 1 && totalPage === 1 || totalPage === null && slides.length === 1) {
                                        stop = true;
                                    }


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

                                    // On donne à notre page un CSS
                                    if(slides === document.getElementsByClassName("mySlides")) {
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
                                    // Aller à la prochaine slide.
                                    ++slideIndex;

                                }
                            }
                        });
                    }

                    // Si c'est un short
                    if (child.className === 'short_container') {
                        console.log("--Lecture short");
                    }
                }

                if(count === 0) {
                    console.log("--Lecture image");
                    ++slideIndex;
                }
            } else {
                // Aller à la prochaine slide
                ++slideIndex;
            }
        }
    }

    if(slides.length !== 1 || totalPage !== 1) {
        // On définit notre temps, ici 10 secondes
        setTimeout(function(){displayOrHide(slides, slideIndex)} , 4000);
    }
}

/**
 * Affiche un diaporama en n'affichant que les vidéos, qu'on utilisera donc à droite dans notre télévision
 */
function displayOrHideVideo(slides, slideIndex)
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

        // On vérifie qu'une vidéo est présente
        if (slides[slideIndex].querySelector('.video_container')){
            console.log("--Vidéo trouvée");

            // On affiche la vidéo
            slides[slideIndex].style.display = "block";
        } else {
            // Si ce n'est pas une vidéo, on passe à la prochaine slide
            console.log("--Non vidéo trouvée - On passe à la suivante");
            ++slideIndex;
        }

        // On vérifie qu'il existe une dernière slide
        if(slides[slideIndex] !== undefined) {
            console.log("--Slide n°"+ slideIndex);

            // On vérifie qu'un enfant existe et que c'est bien une vidéo
            if(slides[slideIndex].childNodes) {
                let count = 0;
                for(let i = 0; i < slides[slideIndex].childNodes.length; ++i) {
                    let child = slides[slideIndex].childNodes[i];

                    // Si c'est une vidéo, on l'affiche
                    if(child.className === 'video_container') {
                        console.log("--Lecture de la vidéo");
                        count++;

                    }
                }

                if(count === 0) {
                    console.log("--Pas de vidéo - Aller à la prochaine slide");
                    ++slideIndex;
                }
            } else {
                // Aller à la prochaine slide
                ++slideIndex;
            }
        }
    }

    if(slides.length !== 1 || totalPage !== 1) {
        // On définit notre temps, ici 10 secondes
        setTimeout(function(){displayOrHide(slides, slideIndex)} , 4000);
    }
}



