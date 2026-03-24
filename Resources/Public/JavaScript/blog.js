document.querySelectorAll('[data-clean-content] p').forEach(function (p) {
    if (p.innerHTML.trim() === '&nbsp;' || p.textContent.trim() === '\u00a0' || p.textContent.trim() === '') {
        p.remove();
    }
});
