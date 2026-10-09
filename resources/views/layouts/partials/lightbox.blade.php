<!-- Modal Lightbox -->
<div class="modal fade" id="lightboxModal" tabindex="-1" aria-hidden="true" style="backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px); background: rgba(0,0,0,0.6);">
    <div class="modal-dialog modal-dialog-centered modal-lg" style="transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);">
        <div class="modal-content bg-transparent border-0 shadow-none position-relative">
            <div class="position-fixed z-3" style="top: 20px; right: 20px;">
                <button type="button" class="bg-transparent text-white m-3 p-0 border-0" data-bs-dismiss="modal" aria-label="Close" style="opacity: 1; cursor: pointer; outline: none;">
                    <i class="bi bi-x-lg" style="font-size: 2.5rem; filter: drop-shadow(0 2px 6px rgba(0,0,0,0.8));"></i>
                </button>
            </div>
            <div class="modal-body text-center p-0">
                <img id="lightboxImage" src="" class="img-fluid rounded-0 shadow-lg" alt="Selfie" style="max-height: 85vh; object-fit: contain; animation: zoomIn 0.4s cubic-bezier(0.2, 1, 0.3, 1);">
            </div>
        </div>
    </div>
</div>
<style>
    @keyframes zoomIn {
        from { transform: scale(0.9); opacity: 0; }
        to { transform: scale(1); opacity: 1; }
    }
</style>

<script>
    function showLightbox(imageUrl) {
        document.getElementById('lightboxImage').src = imageUrl;
        var lightboxModal = new bootstrap.Modal(document.getElementById('lightboxModal'));
        lightboxModal.show();
    }
</script>
