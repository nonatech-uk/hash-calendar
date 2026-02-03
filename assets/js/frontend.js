/**
 * GH3 Hash Runs - Frontend JavaScript
 */
document.addEventListener('DOMContentLoaded', function() {
    // Expand/collapse compact run details
    var headers = document.querySelectorAll('.gh3-compact-header');

    headers.forEach(function(header) {
        header.addEventListener('click', function(e) {
            // Don't toggle if clicking a link
            if (e.target.tagName === 'A') {
                return;
            }

            var parent = this.closest('.gh3-compact-run');
            var details = parent.querySelector('.gh3-compact-details');

            if (details) {
                var isExpanded = parent.classList.contains('expanded');

                if (isExpanded) {
                    details.style.display = 'none';
                    parent.classList.remove('expanded');
                } else {
                    details.style.display = 'block';
                    parent.classList.add('expanded');
                }
            }
        });
    });
});
