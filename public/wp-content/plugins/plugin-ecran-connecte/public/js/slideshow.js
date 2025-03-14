let urlUpload = "/wp-content/uploads/media/";
let pdfUrl = null;
let numPage = 0; // Numéro de page courante
let totalPage = null; // Nombre de pages
let endPage = false;
let stop = false;
let videoDurations = DURATIONS.videoDurations;
let numberArray = videoDurations.length;
let otherDurations = DURATIONS.otherDurations;
let timeout = parseInt(SCROLL_SETTINGS.scrollSpeed);
let timeoutAdjusted = (numberArray > 0) ? (timeout * 1000 * numberArray) : timeout*1000;
console.log(timeoutAdjusted);

infoSlideShow();
videoSlideshow();
scheduleSlideshow();



/**
 * Lance le diaporama s'il existe des informations
 */

function infoSlideShow()
{
    if(document.getElementsByClassName("myInfoSlides").length > 0) {
        displayOrHide(document.getElementsByClassName("myInfoSlides"), 0);
    }
}

/**
 * Lance le diaporama s'il existe des vidéos
 */
function videoSlideshow(){
    if (document.getElementsByClassName("myVideoSlides").length > 0){
        displayOrHideVideo(document.getElementsByClassName("myVideoSlides"), 0);
    }
}

/**
 * Lance le diaporama s'il existe des informations
 */

function scheduleSlideshow()
{
    if(document.getElementsByClassName("mySlides").length > 0) {
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
            slideIndex = 0;
        }

        // On vérifie qu'il existe une dernière slide
        if(slides[slideIndex] !== undefined) {


            slides[slideIndex].style.display = "block";
            // On vérifie qu'un enfant existe
            if(slides[slideIndex].childNodes) {
                let count = 0;
                for(let i = 0; i < slides[slideIndex].childNodes.length; ++i) {
                    let child = slides[slideIndex].childNodes[i];
                    // Si c'est un PDF
                    if(child.className === 'canvas_pdf') {

                        count++;

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
                                    document.getElementById('the-canvas-' + pdfLink + '-page' + (numPage-1)).remove();
                                }
                            }

                            if(totalPage >= numPage && stop === false) {
                                pdf.getPage(numPage).then(function(page) {
                                    if(slides.length === 1 && totalPage === 1 || totalPage === null && slides.length === 1) {
                                        stop = true;
                                    }



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
                    }

                }

                if(count === 0) {
                    ++slideIndex;
                }
            } else {
                // Aller à la prochaine slide
                ++slideIndex;
            }
        }
    }

    if(slides.length !== 1 || totalPage !== 1) {
        // On attend autant de temps que nécessaire pour que l'information passe assez longtemps
        setTimeout(function(){displayOrHide(slides, slideIndex)} , otherDurations[slideIndex-1]);
    }

    setTimeout(function(){
        window.location.reload(true);
    }, 86400000);
}



/**
 * Affiche un diaporama en n'affichant que les vidéos, qu'on utilisera donc à gauche dans notre télévision.
 */
function displayOrHideVideo(slides, slideIndex) {
    let edt = document.getElementsByClassName("table tabSchedule");

    if (slides.length > 0) {
        // Masquer toutes les diapositives et afficher l'emploi du temps
        if (slides.length > 1) {
            for (let i = 0; i < slides.length; ++i) {
                slides[i].style.display = "none";
            }
            for (let i = 0; i < edt.length; ++i) {
                edt[i].style.display = "table";
            }
        }

        // Une fois toutes les vidéos passées, afficher l'emploi du temps
        if (slideIndex === slides.length) {
            for (let i = 0; i < slides.length; ++i) {
                slides[i].style.display = "none";
            }
            for (let i = 0; i < edt.length; ++i) {
                edt[i].style.display = "table";
            }

            setTimeout(function () {
                displayOrHideVideo(slides, 0); // Redémarre depuis la première slide
            }, timeoutAdjusted);

            return;
        }

        // Vérifier si la diapositive actuelle est bien définie
        if (slides[slideIndex] !== undefined) {
            let hasVideo = false;

            // Vérifier si cette diapositive contient une vidéo
            if (slides[slideIndex].childNodes) {
                for (let i = 0; i < slides[slideIndex].childNodes.length; ++i) {
                    let child = slides[slideIndex].childNodes[i];
                    if (child.className === 'video_container') {
                        slides[slideIndex].style.display = "block";
                        slides[slideIndex].style.position = "relative";
                        hasVideo = true;
                    }
                }
            }

            // Si une vidéo est détectée, masquer l'emploi du temps
            if (hasVideo) {
                for (let i = 0; i < edt.length; ++i) {
                    edt[i].style.display = "none";
                }
            }

            // Passer à la diapositive suivante
            ++slideIndex;
        }

        if (slides.length !== 1 || totalPage !== 1) {
            // Sécuriser l'accès à `videoDurations[slideIndex-1]`
            let duration = videoDurations[slideIndex - 1] || 10000; // Valeur par défaut : 5s si non définie

            setTimeout(function () {
                displayOrHideVideo(slides, slideIndex);
            }, duration);
        }
    }
}




