<?php

namespace App\Services;

/**
 * TODO: sending plaintext password is not the best practice
 */
class MailSender
{
    /**
     * Send a registration confirmation email to a new employee
     *
     * @param string $email Recipient email address
     * @param string $name Recipient name
     * @param string $password User's password
     * @return bool Success status of email sending operation
     */
    public function sendRegistrationEmail(string $email, string $name, string $password): bool
    {
        $subject = "Thanks for registering";
        $message = "Dear " . $name . ",\n" . 
                   "Thanks for registering with AwesomeCorp!! your password is " . $password . ".\n" .
                   "You can login at: http://www.awesomecorp.com/login.\n" .
                   "Regards,\nAwesomeCorp";
        
        $headers = "From: no-reply@awesomecorp.com\r\n";
        $headers .= "Reply-To: support@awesomecorp.com\r\n";
        
        $result = mail($email, $subject, $message, $headers);
        
        // Log mail sending status to debug
        error_log("Mail to {$email} " . ($result ? "sent successfully" : "failed to send"));
        
        return $result;
    }
}
