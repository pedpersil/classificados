document.addEventListener('DOMContentLoaded', function () {
  const form = document.getElementById('searchForm');
  const input = document.getElementById('searchInput');
  const loader = document.getElementById('searchLoader');
  const resultsContainer = document.getElementById('searchResultsContainer');

  if (form && input && loader && resultsContainer) {
    form.addEventListener('submit', function (e) {
      e.preventDefault();
      const term = input.value.trim();

      if (!term) return;

      loader.style.display = 'flex';
      resultsContainer.classList.remove('loaded');
      resultsContainer.innerHTML = '';

      fetch(`${BASE_URL}/ads/search/for?term=${encodeURIComponent(term)}`)
        .then(res => res.text())
        .then(data => {
          loader.style.display = 'none';
          resultsContainer.innerHTML = data;
          resultsContainer.classList.add('loaded');
        })
        .catch(() => {
          loader.style.display = 'none';
          resultsContainer.innerHTML = `<p class="text-danger">Erro ao buscar anúncios.</p>`;
        });
    });
  }
});
