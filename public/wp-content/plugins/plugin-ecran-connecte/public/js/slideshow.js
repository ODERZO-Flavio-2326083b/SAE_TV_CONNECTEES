    let urlUpload = "/wp-content/uploads/media/";
    let pdfUrl = null;
    let numPage = 0; // Numéro de page courante
    let totalPage = null; // Nombre de pages
    let endPage = false;
    let stop = false;

    infoSlideShow();
    scheduleSlideshow();

    /**
     * Begin a slideshow if there is some informations
     */
    function infoSlideShow()
    {
        if(document.getElementsByClassName("myInfoSlides").length > 0) {
            console.log("-Début du diaporama");
            displayOrHide(document.getElementsByClassName("myInfoSlides"), 0);
        }
    }

    /**
     * Begin a slideshow if there is some informations
     */
    function scheduleSlideshow()
    {
        if(document.getElementsByClassName("mySlides").length > 0) {
            console.log("-Début du diaporama");
            displayOrHide(document.getElementsByClassName("mySlides"), 0);
        }
    }

    /**
     * Display a slideshow
     */
    function displayOrHide(slides, slideIndex)
    {
        if(slides.length > 0) {
            if(slides.length > 1) {
                for (let i = 0; i < slides.length; ++i) {
                    slides[i].style.display = "none";
                }
            }

            // On recommence par la première diapositive
            if(slideIndex === slides.length) {
                console.log("-Fin du diaporama - On recommence");
                slideIndex = 0;
            }

            // Vérifie si une slide suivante existe
            if(slides[slideIndex] !== undefined) {

                console.log("--Slide n°"+ slideIndex);
                // Display the slide
                slides[slideIndex].style.display = "block";

                // Dans le cas d'une vidéo
                let video = document.getElementsByClassName("video_thumbnail");
                for (let i=0; i<video.length; ++i){
                    video[i].play();
                }





                // Check child
                if(slides[slideIndex].childNodes) {
                    var count = 0;
                    // Essaie de voir si c'est un PDF
                    for(let i = 0; i < slides[slideIndex].childNodes.length; ++i) {
                        // Dans le cas d'un PDF
                        if(slides[slideIndex].childNodes[i].className === 'canvas_pdf') {

                            console.log("--Lecture de PDF");

                            count = count + 1;

                            // Generate the url
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
                                        ++slideIndex;
                                        // Go to the next slide
                                    }
                                }
                            });
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