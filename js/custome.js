document.querySelectorAll('.de-dot').forEach(dot => {
    dot.addEventListener('mouseenter', function () {
        // Add show-tooltip class to trigger visibility
        this.classList.add('show-tooltip');

        const tooltip = this.querySelector('.d-content');
        const dotRect = this.getBoundingClientRect();
        const tooltipRect = tooltip.getBoundingClientRect();
        const viewportWidth = window.innerWidth;
        const viewportHeight = window.innerHeight;

        // Reset previous flip classes and styles
        tooltip.classList.remove('flip-left', 'flip-bottom');
        tooltip.style.top = ''; // Reset inline styles
        tooltip.style.left = '';
        tooltip.style.right = '';

        // Mobile detection (e.g., viewport width <= 768px)
        const isMobile = viewportWidth <= 768;

        // Default positioning
        let defaultTop = isMobile ? '-60px' : '-70px';
        let defaultLeft = isMobile ? '10px' : '25px';

        // Horizontal positioning
        if (tooltipRect.right > viewportWidth || (isMobile && dotRect.left + tooltipRect.width > viewportWidth)) {
            tooltip.classList.add('flip-left');
            tooltip.style.left = 'auto';
            tooltip.style.right = isMobile ? '10px' : '25px';
        } else if (tooltipRect.left < 0 || (isMobile && dotRect.left < 10)) {
            tooltip.style.left = isMobile ? '10px' : '25px';
            tooltip.style.right = 'auto';
        } else {
            tooltip.style.left = defaultLeft;
        }

        // Vertical positioning
        if (tooltipRect.top < 0 || (isMobile && dotRect.top - tooltipRect.height < 10)) {
            tooltip.classList.add('flip-bottom');
            tooltip.style.top = isMobile ? '20px' : '30px';
        } else if (tooltipRect.bottom > viewportHeight) {
            tooltip.style.top = `-${tooltipRect.height + (isMobile ? 5 : 10)}px`;
        } else {
            tooltip.style.top = defaultTop;
        }
    });

    dot.addEventListener('mouseleave', function () {
        // Remove show-tooltip class
        this.classList.remove('show-tooltip');

        const tooltip = this.querySelector('.d-content');
        // Reset styles
        tooltip.classList.remove('flip-left', 'flip-bottom');
        tooltip.style.top = '';
        tooltip.style.left = '';
        tooltip.style.right = '';
    });

    // Add touch support for mobile devices
    dot.addEventListener('click', function (e) {
        e.preventDefault();
        // Toggle tooltip on click for mobile
        if (!this.classList.contains('show-tooltip')) {
            // Close other open tooltips
            document.querySelectorAll('.de-dot.show-tooltip').forEach(otherDot => {
                otherDot.classList.remove('show-tooltip');
            });
            this.classList.add('show-tooltip');

            // Trigger positioning logic
            const tooltip = this.querySelector('.d-content');
            const dotRect = this.getBoundingClientRect();
            const tooltipRect = tooltip.getBoundingClientRect();
            const viewportWidth = window.innerWidth;
            const viewportHeight = window.innerHeight;
            const isMobile = viewportWidth <= 768;

            // Reset styles
            tooltip.classList.remove('flip-left', 'flip-bottom');
            tooltip.style.top = '';
            tooltip.style.left = '';
            tooltip.style.right = '';

            let defaultTop = isMobile ? '-60px' : '-70px';
            let defaultLeft = isMobile ? '10px' : '25px';

            // Horizontal positioning
            if (tooltipRect.right > viewportWidth || (isMobile && dotRect.left + tooltipRect.width > viewportWidth)) {
                tooltip.classList.add('flip-left');
                tooltip.style.left = 'auto';
                tooltip.style.right = isMobile ? '10px' : '25px';
            } else if (tooltipRect.left < 0 || (isMobile && dotRect.left < 10)) {
                tooltip.style.left = isMobile ? '10px' : '25px';
                tooltip.style.right = 'auto';
            } else {
                tooltip.style.left = defaultLeft;
            }

            // Vertical positioning
            if (tooltipRect.top < 0 || (isMobile && dotRect.top - tooltipRect.height < 10)) {
                tooltip.classList.add('flip-bottom');
                tooltip.style.top = isMobile ? '20px' : '30px';
            } else if (tooltipRect.bottom > viewportHeight) {
                tooltip.style.top = `-${tooltipRect.height + (isMobile ? 5 : 10)}px`;
            } else {
                tooltip.style.top = defaultTop;
            }
        } else {
            this.classList.remove('show-tooltip');
        }
    });
});