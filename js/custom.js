$(document).ready(function() {
    $('#searchForm').submit(function(event) {
        event.preventDefault(); // Prevent default form submission behavior

        // Get the search query
        var query = $('#searchInput').val();
        // Perform AJAX request to fetch search results
        $.get('search.php', { q: query })
            .done(function(response) {
                if (response.indexOf('No jobs found') !== -1) {
                    // Show the banner and banner__1 sections
                    $('#banner').show();
                    $('#banner__1').show();
                    $('#heading2').show();
                    $('#heading3').show();
                    // Show the "No jobs found" message
                    $('#searchResults').html('<p>No jobs found</p>').show();
                    $('.job_list').hide(); // Show the job list
                } else {
                    // Hide the banner and banner__1 sections
                    $('#banner').hide();
                    $('#banner__1').hide();
                    $('#heading2').hide();
                    $('#heading3').hide();
                    $('.job_list').hide(); // Hide the job list

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
});
