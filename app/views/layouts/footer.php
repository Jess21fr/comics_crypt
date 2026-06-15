<!-- <div id="toastMsg" 
     style="position:fixed;bottom:20px;right:20px;z-index:9999;
            background:#28a745;color:white;padding:12px 20px;
            border-radius:6px;display:none;font-weight:bold;">
</div>

<script>
function showToast(msg, color="#28a745") {
    let t = $('#toastMsg');
    t.css("background", color);
    t.text(msg).fadeIn(200);

    setTimeout(() => {
        t.fadeOut(400);
    }, 3000);
}
</script> -->
<footer class="mt-5 py-4 text-center text-secondary">
    <small>ComicsCrypt Back‑Office</small>
</footer>
<?php require __DIR__ . '/../issues/cover_modal.php'; ?>
</body>
</html>