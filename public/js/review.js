(function () {
    if (!window.reviewFileUrl) {
        return;
    }

    const viewer = document.getElementById('pdf-viewer');
    if (!viewer) {
        return;
    }

    const contextType = document.getElementById('context_type');
    const contextPage = document.getElementById('context_page');

    pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/4.0.379/pdf.worker.min.js';

    pdfjsLib.getDocument(window.reviewFileUrl).promise.then(function (pdf) {
        pdf.getPage(1).then(function (page) {
            const viewport = page.getViewport({ scale: 1.2 });
            const canvas = document.createElement('canvas');
            const context = canvas.getContext('2d');
            canvas.height = viewport.height;
            canvas.width = viewport.width;
            viewer.appendChild(canvas);

            page.render({ canvasContext: context, viewport: viewport });

            canvas.addEventListener('click', function () {
                contextType.value = 'pdf';
                contextPage.value = 1;
            });
        });
    });
})();
