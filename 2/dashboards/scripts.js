// scripts.js

// Function to switch sections and load content dynamically
function switchSection(section) {
    // Hide all sections initially
    const sections = document.querySelectorAll('.section');
    sections.forEach((section) => {
        section.style.display = 'none';
    });

    // Show the selected section
    const selectedSection = document.getElementById(`${section}-section`);
    selectedSection.style.display = 'block';

    // Load content based on the section
    loadContent(section);
}

// Function to load content based on the section
function loadContent(section) {
    let url = '';

    // Set URL based on the selected section
    if (section === 'users') {
        url = 'fetch_users.php';
    } else if (section === 'topics') {
        url = 'fetch_topics.php';
    } else if (section === 'questions') {
        url = 'fetch_questions.php';
    } else if (section === 'access') {
        url = 'fetch_access.php';
    }else if (section === 'scores') {
        url = 'fetch_scores.php';
    }  

    // Fetch the content for the selected section
    fetch(url)
        .then(response => response.text())
        .then(data => {
            // Insert the content into the selected section
            const contentArea = document.getElementById(`${section}-section`);
            contentArea.innerHTML = data;
        })
        .catch(error => {
            console.error('Error fetching content:', error);
        });
}

// Set default section to be displayed
document.addEventListener('DOMContentLoaded', () => {
    // Set default section to 'topics'
    switchSection('topics');
});
