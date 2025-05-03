document.addEventListener('DOMContentLoaded', function() {
    // Add parchment aging effect to questions
    const questions = document.querySelectorAll('.question');
    questions.forEach((q, i) => {
        const age = Math.random() * 20 + 5;
        q.style.boxShadow = `inset 0 0 ${age}px rgba(0,0,0,0.1)`;
    });
    
    // Add hover effect to answers
    const answers = document.querySelectorAll('.answer');
    answers.forEach(a => {
        a.addEventListener('mouseover', function() {
            this.style.transform = 'translateX(5px)';
        });
        a.addEventListener('mouseout', function() {
            this.style.transform = 'translateX(0)';
        });
    });
});
