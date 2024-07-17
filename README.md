#ChallengeBotController - Automated Registration Script Overview ChallengeBotController is a PHP script designed to automate the registration process on the "https://challenge.blackscale.media" website. It leverages Guzzle HTTP client for making requests and integrates with MailSlurp API for managing temporary email inboxes. The script follows a sequence of steps to complete the user registration, including form submission and email verification.
Features Initialization: Sets up HTTP client with cookie management and configures MailSlurp API for email handling.
Form Value Retrieval: Extracts the security token (stoken) required for form submission from the registration page.
Temporary Email Inbox Creation: Generates a temporary email inbox using MailSlurp to securely receive verification emails.
Form Submission: Automatically fills and submits the registration form with dynamically generated user data.
Email Verification: Waits for and retrieves the email verification code from the temporary inbox.
Captcha Handling: Submits the received verification code to bypass the captcha verification step.
Site Key Extraction: Extracts the site key for Google reCAPTCHA from the captcha response for further automation.

