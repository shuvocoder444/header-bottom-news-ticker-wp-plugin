jQuery(document).ready(function ($) {
    $('.hbnt-ticker').each(function() {
        var $ticker = $(this);
        var $list = $ticker.find('.hbnt-list');
        
        if (!$list.length) return;

        // Widget থেকে speed নেওয়া
        var baseSpeed = parseFloat($ticker.data('speed')) || 1.0;
        var speed = baseSpeed; // pixels per frame
        var currentPos = 0;
        var paused = false;

        // Content duplicate করা seamless loop এর জন্য
        var originalContent = $list.html();
        $list.append(originalContent).append(originalContent);

        // Animation function
        function animate() {
            if (!paused) {
                currentPos -= speed;
                
                // Reset position for infinite loop
                var singleWidth = $list[0].scrollWidth / 3;
                if (Math.abs(currentPos) >= singleWidth) {
                    currentPos = 0;
                }
                
                $list.css('transform', 'translateX(' + currentPos + 'px)');
            }
            requestAnimationFrame(animate);
        }

        // Hover করলে থামবে
        $ticker.on('mouseenter', function() {
            paused = true;
        }).on('mouseleave', function() {
            paused = false;
        });

        // Animation শুরু
        requestAnimationFrame(animate);
    });
});
