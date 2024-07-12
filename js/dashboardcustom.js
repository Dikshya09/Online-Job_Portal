$(document).ready(function() {
    $('#searchForm').submit(function(event) {
        event.preventDefault(); // Prevent default form submission behavior

        // Get the search query
        var query = $('#searchInput').val();
        // Perform AJAX request to fetch search results
        $.get('dashboard_search.php', { q: query })
            .done(function(response) {
                if (response.indexOf('No jobs found') !== -1) {
                    // Show the banner and banner__1 sections
                    $('#banner').show();
                    $('#banner__1').show();
                    $('#heading2').show();
                    $('#heading3').show();
                    // Show the "No jobs found" message
                    $('#jobResults').hide(); // Hide the original job list
                    $('#searchResults').html('<p>No jobs found</p>').show();
                } else {
                    // Hide the banner and banner__1 sections
                    $('#banner').hide();
                    $('#banner__1').hide();
                    $('#heading2').hide();
                    $('#heading3').hide();
                    $('#jobResults').hide(); // Hide the original job list

                    // Update the search results container with the response and show it
                    $('#searchResults').html(response).show();

                    // Scroll to the search results
                    document.getElementById('searchResults').scrollIntoView({ behavior: 'smooth' });
                }
            })
            .fail(function(xhr, status, error) {
                // Error handling
                console.error('Request failed with status:', status);
            });
    });

    const dropdownMenu = document.getElementById('dropdownMenu');

    window.toggleDropdown = function() {
        dropdownMenu.style.display = dropdownMenu.style.display === 'block' ? 'none' : 'block';
    }

    document.addEventListener('click', function(event) {
        const profileIcon = document.querySelector('.profile');
        if (!profileIcon.contains(event.target) && !dropdownMenu.contains(event.target)) {
            dropdownMenu.style.display = 'none';
        }
    });
});