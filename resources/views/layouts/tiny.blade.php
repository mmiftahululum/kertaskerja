<script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
<link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/gh/scrapooo/quill-resize-module@1.0.2/dist/quill-resize-module.js"></script>

         <script>

 Quill.register('modules/imageResize', QuillResizeModule);

        // Opsi toolbar yang diinginkan
        var toolbarOptions = [
          ['bold', 'italic', 'underline', 'strike'],
          ['blockquote', 'code-block'],
          [{ 'header': 1 }, { 'header': 2 }],
          [{ 'list': 'ordered'}, { 'list': 'bullet' }],
          [{ 'indent': '-1'}, { 'indent': '+1' }],
          [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
          ['link', 'image'], // Tombol untuk menyisipkan gambar
          ['clean']
        ];
        
        // Inisialisasi Quill pada div #editor
        var quill = new Quill('#editor', {
          modules: {
            toolbar: toolbarOptions,
             imageResize: {
              displaySize: true
            },
          },
          
          theme: 'snow'
        });

        // Ambil form dan input tersembunyi
        var form = document.querySelector('#formTask');
        var contentInput = document.querySelector('#content');

        // Saat form akan di-submit...
        form.addEventListener('submit', function(e) {
    
          // ...ambil konten HTML dari dalam editor Quill...
          var quillContent = quill.root.innerHTML;
          // ...dan masukkan ke dalam input tersembunyi.
          contentInput.value = quillContent;
        });
    </script>