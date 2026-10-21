</main>

<footer class="app-footer">
    <p>&copy; <?php echo date('Y'); ?> <?php echo $site_name; ?> — جميع الحقوق محفوظة</p>
</footer>

<script>
// تأثير إخفاء/إظهار الهيدر عند السكرول
let lastScroll = 0;
const header = document.getElementById('topHeader');
window.addEventListener('scroll', () => {
    const current = window.pageYOffset;
    if (current > lastScroll && current > 60) {
        header.classList.add('header-hidden');
    } else {
        header.classList.remove('header-hidden');
    }
    lastScroll = current;
});
</script>
</body>
</html>
