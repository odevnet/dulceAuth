<?php

/**
 * dulceAuth: Library that allows user management. It facilitates registration
 * and authentication, as well as the administration of users with roles and
 * permissions.
 *
 * @link https://github.com/odevnet/dulceAuth/
 *
 * @license https://github.com/odevnet/dulceAuth/blob/main/LICENSE (MIT License)
 */

namespace src;

use RuntimeException;
use InvalidArgumentException;

/**
 * Class DulceMail
 *
 * Simple class to send emails using PHP's mail() function.
 *
 * @package src
 *
 * @since 1.0
 */
class DulceMail
{
    /**
     * The sender's email address.
     *
     * @var string|null
     */
    private $from;

    /**
     * The email address of destination
     *
     * @var string|null
     */
    private $to;

    /**
     * The subject of the email.
     *
     * @var string|null
     */
    private $subject;

    /**
     * The body of the email.
     *
     * @var string|null
     */
    private $message;

    /**
     * The email headers.
     *
     * @var string
     */
    private $headers;

    /**
     * Constructor of the class.
     *
     * @param string|null $from The sender’s email address.
     * @param string|null $to The email address of destination.
     * @param string|null $subject The subject of the email.
     * @param string|null $message The body of the email.
     */
    public function __construct(?string $from = null, ?string $to = null, ?string $subject = null, ?string $message = null)
    {
        $this->from = $from;
        $this->to = $to;
        $this->subject = $subject;
        $this->message = $message;
        $this->headers = $from ? "From:" . $this->from : '';
    }

    /**
     * Method to set the sender email address.
     *
     * @param string $from The sender’s email address.
     *
     * @return \src\DulceMail
     *
     * @throws \InvalidArgumentException When the sender's email is incorrect.
     */
    public function from(string $from): self
    {
        if (!filter_var($from, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("Invalid sender email address: $from");
        }
        $this->from = $from;

        return $this;
    }

    /**
     * Sets the destination email address.
     *
     * @param string $to The email address of destination.
     *
     * @return \src\DulceMail
     *
     * @throws \InvalidArgumentException When the recipient's email is incorrect.
     */
    public function to(string $to): self
    {
        if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("Invalid destination email address: $to");
        }
        $this->to = $to;

        return $this;
    }

    /**
     * Set the subject of the email.
     *
     * @param string $subject The subject of the email.
     * @return \src\DulceMail
     */
    public function subject(string $subject): self
    {
        $this->subject = $subject;
        return $this;
    }

    /**
     * Sets the body of the email.
     *
     * @param string $message The body of the email.
     * @return \src\DulceMail
     */
    public function message(string $message): self
    {
        $this->message = $message;
        return $this;
    }

    /**
     * Method responsible for sending the email.
     *
     * @return bool True if the email is sent successfully, otherwise false.
     *
     * @throws \RuntimeException When data is missing to complete the email,
     * or if there has been a problem sending the email.
     */
    public function send(): bool
    {
        if (empty($this->from) || empty($this->to) || empty($this->subject) || empty($this->message)) {
            throw new RuntimeException("Email details are incomplete");
        }

        ini_set('display_errors', 1);
        error_reporting(E_ALL);
        // Set headers
        $headers = "From:" . $this->from;
        // Send the email using PHP's mail() function
        $success = mail($this->to, $this->subject, $this->message, $headers);

        if (!$success) {
            throw new RuntimeException("Failed to send email");
        }

        return $success;
    }

    /**
     * This method is used to send a verification email.
     *
     * @param string $to Email address to send the verification email.
     * @param string $token Verification token.
     * @param int $userId User ID.
     * @param bool $show_screen_message Indicated to show or not a message on the screen.
     *
     * @return bool True if the email is sent successfully, otherwise false.
     */
    public function sendVerificationEmail(string $to, string $token, int $userId, bool $show_screen_message = false): bool
    {
        //$template = json_decode(file_get_contents(JSON_FILE_VERIFICATION_EMAIL), true)['verification'];
        //return $this->sendEmail($to, $token, $userId, $template, $page = VERIFICATION_PAGE, $show_screen_message);

        $template = $this->loadTemplate(DULCE_AUTH_VERIFICATION_EMAIL_JSON_FILE, 'verification');
        return $this->sendEmail($to, $token, $userId, $template, DULCE_AUTH_VERIFICATION_PAGE_URL, $show_screen_message);
    }

    /**
     * Is used for send a password reset email to the user.
     *
     * @param string $to Email address to send the password reset email.
     * @param string $token Verification token.
     * @param int $userId User ID.
     * @param bool $show_screen_message Indicated to show or not a message on the screen.
     *
     * @return bool True if the email is sent successfully, otherwise false.
     */
    public function sendForgotPasswordEmail(string $to, string $token, int $userId, bool $show_screen_message = false): bool
    {
        //$template = json_decode(file_get_contents(JSON_FILE_FORGOT_PASSWORD_EMAIL), true)['forgot'];
        //return $this->sendEmail($to, $token, $userId, $template, $page = FORGOT_PASSWORD_PAGE, $show_screen_message);
        $template = $this->loadTemplate(DULCE_AUTH_FORGOT_PASSWORD_EMAIL_JSON_FILE, 'forgot');
        return $this->sendEmail($to, $token, $userId, $template, DULCE_AUTH_FORGOT_PASSWORD_PAGE_URL, $show_screen_message);
    }

    /**
     * Send an email using the template provided.
     *
     * @param string $to Destination email address.
     * @param string $token Verification token.
     * @param int $userId User ID.
     * @param array $template The email template containing the subject,
     * message, and screen message.
     * @param bool $show_screen_message  Indicated to show or not a message on the screen.
     *
     * @return bool True if the email is sent successfully, otherwise false.
     *
     * @throws \RuntimeException If there has been a problem sending the email.
     */
    private function sendEmail(string $to, string $token, int $userId, array $template, string $page, bool $show_screen_message): bool
    {
        $subject = $template['subject'];
        //var_dump($template['type']);

        $verificationUrl = defined('DULCE_AUTH_CUSTOM_VERIFICATION_EMAIL_URL')
            ? call_user_func(DULCE_AUTH_CUSTOM_VERIFICATION_EMAIL_URL, $token, $userId)
            : DULCE_AUTH_WEB_PAGE . "/{$page}?token={$token}&userId={$userId}";

        if (isset($template['type']) && $template['type'] === 'verification') {
            $message = str_replace('{{verification_link}}', $verificationUrl, $template['message']);
        }

        $forgotUrl = defined('CUSTOM_FORGOT_PASSWORD_EMAIL_URL')
            ? call_user_func(DULCE_AUTH_CUSTOM_FORGOT_PASSWORD_EMAIL_URL, $token, $userId)
            : DULCE_AUTH_WEB_PAGE . "/{$page}?token={$token}&userId={$userId}";

        if (isset($template['type']) && $template['type'] === 'forgot') {
            $message = str_replace('{{verification_link}}', $forgotUrl, $template['message']);
        }
        //$message = str_replace('{{verification_link}}', $verificationUrl, $template['message']);
        $screen_message = $template['screen_message'];
        // Set headers
        $headers = "From:" . $this->from;

        // Send the email using PHP's mail() function
        $success = mail($to, $subject, $message, $headers);

        if (!$success) {
            throw new RuntimeException("Failed to send email");
        }

        if ($show_screen_message) {
            echo $screen_message;
        }

        return $success;
    }

    /**
     * Load an email template from a JSON file.
     *
     * @param string $filePath The path to the JSON file.
     * @param string $key The key to retrieve the specific template.
     *
     * @return array The email template.
     *
     * @throws \RuntimeException If any of the following cases occur:
     * - The JSON file is not found
     * - The JSON file could not be read
     * - The JSON file has an incorrect format
     * @throws \InvalidArgumentException If the key to read the JSON file is not found.
     */
    private function loadTemplate(string $filePath, string $key): array
    {
        if (!file_exists($filePath)) {
            throw new RuntimeException("Template file not found: $filePath");
        }

        $jsonContent = file_get_contents($filePath);

        if ($jsonContent === false) {
            throw new RuntimeException("Failed to read template file: $filePath");
        }

        $templateData = json_decode($jsonContent, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new RuntimeException("Invalid JSON in template file: " . json_last_error_msg());
        }

        if (!isset($templateData[$key])) {
            throw new InvalidArgumentException("Template key not found: $key");
        }

        return $templateData[$key];
    }
}
