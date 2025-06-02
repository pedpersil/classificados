document.addEventListener('DOMContentLoaded', function () {
    const adsContainer = document.getElementById('adsContainer');
    const pagination = document.getElementById('pagination');

    function loadAds(page = 1) {
        fetch(`${BASE_URL}/fetch_ads.php?page=${page}`)
            .then(res => res.json())
            .then(data => {
                adsContainer.innerHTML = data.ads;
                pagination.innerHTML = data.pagination;

                document.querySelectorAll('#pagination a').forEach(link => {
                    link.addEventListener('click', e => {
                        e.preventDefault();
                        const selectedPage = e.target.getAttribute('data-page');
                        loadAds(selectedPage);
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    });
                });
            });
    }

    loadAds(); // Carrega a primeira página
});
