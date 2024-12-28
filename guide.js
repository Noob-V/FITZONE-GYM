function filterGuides() {
    const searchInput = document.getElementById('searchInput').value.toLowerCase();
    const filterValue = document.getElementById('filterSelect').value;
    const boxes = document.querySelectorAll('.box');
    let matched = [];
    
    
    const existingRecommendations = document.querySelector('.recommendations');
    if (existingRecommendations) {
        existingRecommendations.remove();
    }

    boxes.forEach(box => {
        const packageName = box.getAttribute('data-package');
        const guideText = box.textContent.toLowerCase();

        const matchesSearch = guideText.includes(searchInput);
        const matchesFilter = filterValue === 'all' || packageName === filterValue;

        if (matchesSearch && matchesFilter) {
            box.style.display = 'block'; 
            matched.push(box); 
        } else {
            box.style.display = 'none'; 
        }
    });

    
    if (matched.length === 0) {
        recommendSimilarGuides();
    }
}

function recommendSimilarGuides() {
    const recommendationSection = document.createElement('div');
    recommendationSection.className = 'recommendations';
    recommendationSection.innerHTML = '<h3>Recommended Guides</h3>'; 

    const allGuides = [
        {
            title: "Starter Package Workout Guide",
            content: "Ideal for beginners to start their fitness journey."
        },
        {
            title: "Essential Package Workout Guide",
            content: "Designed for regular workouts and balanced fitness."
        },
        {
            title: "Premium Package Workout Guide",
            content: "For advanced individuals seeking personalized training."
        },
        {
            title: "Ultimate Fitness Package Workout Guide",
            content: "Comprehensive guide for fitness enthusiasts."
        },
        {
            title: "Couples Package Workout Guide",
            content: "Encourages couples to stay fit together."
        },
        {
            title: "Family Package Workout Guide",
            content: "Keeps families active through enjoyable workouts."
        },
        {
            title: "Online Training Package Workout Guide",
            content: "Designed for those preferring home workouts."
        },
        {
            title: "Senior Fitness Package Workout Guide",
            content: "Focuses on maintaining mobility and health for seniors."
        },
        {
            title: "Student Package Workout Guide",
            content: "Tailored for students balancing studies and fitness."
        }
    ];

    allGuides.forEach(guide => {
        const guideDiv = document.createElement('div');
        guideDiv.className = 'similar-guide';
        guideDiv.innerHTML = `<h4>${guide.title}</h4><p>${guide.content}</p>`;
        recommendationSection.appendChild(guideDiv);
    });

    document.querySelector('.main-content').appendChild(recommendationSection); 
}
