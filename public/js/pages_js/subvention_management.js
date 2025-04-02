document.addEventListener('DOMContentLoaded', function() {
    const detailsButtons = document.querySelectorAll('.details-button');

    detailsButtons.forEach(button => {
        button.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target');
            const targetElement = document.getElementById(targetId);

            if (targetElement) {
                if (targetElement.style.height === '0px' || targetElement.style.height === '') {
                    targetElement.style.height = targetElement.scrollHeight + 'px';
                    targetElement.classList.add('open');
                } else {
                    targetElement.style.height = '0px';
                    targetElement.classList.remove('open');
                }
                
                const icon = this.querySelector('i');
                if (icon.classList.contains('fa-caret-down')) {
                    icon.classList.remove('fa-caret-down');
                    icon.classList.add('fa-caret-up');
                } else {
                    icon.classList.remove('fa-caret-up');
                    icon.classList.add('fa-caret-down');
                }
            }
        });
    });
});
