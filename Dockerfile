FROM trafex/php-nginx

# Install PDO MySQL extension
USER root
RUN apk add --no-cache \
    php84-pdo \
    php84-pdo_mysql \
    msmtp

# Configure msmtp as sendmail replacement
RUN { \
    echo "defaults"; \
    echo "auth off"; \
    echo "tls off"; \
    echo "logfile -"; \
    echo "host mailpit"; \
    echo "port 1025"; \
    echo "from no-reply@awesomecorp.com"; \
    echo "account default"; \
} > /etc/msmtprc \
&& ln -sf /usr/bin/msmtp /usr/sbin/sendmail

# Copy application files
COPY . /var/www/html

# Copy PHP configuration
COPY docker/php/php.ini /etc/php84/php.ini

# Set working directory
WORKDIR /var/www/html

# Fix permissions
RUN chown -R nobody:nobody /var/www/html

# Return to non-root user
USER nobody

# Expose port 8080
EXPOSE 8080 