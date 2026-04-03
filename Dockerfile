# PHP এবং Apache সহ একটি ইমেজ ব্যবহার করছি
FROM php:8.1-apache

# আপনার index.php ফাইলটি সার্ভারে কপি করা হচ্ছে
COPY index.php /var/www/html/index.php

# পোর্ট ৮০ ওপেন করা হচ্ছে
EXPOSE 80

# Apache সার্ভার চালু করা হচ্ছে
CMD ["apache2-foreground"]
