# ChallengeBotController - Automated Registration Script Overview

## Project Purpose

ChallengeBotController is designed to automate the registration process on the [Blackscale Challenge](https://challenge.blackscale.media) website. The script mimics user interactions to complete the registration process seamlessly.

## Overview

This PHP script utilizes Guzzle for HTTP requests and MailSlurp for managing temporary email inboxes. It performs the following key tasks:

1. **Initialization:**
   - Sets up the HTTP client with cookie management.
   - Configures the MailSlurp API for handling temporary emails.

2. **Form Value Retrieval:**
   - Extracts the security token (stoken) from the registration page required for form submission.

3. **Temporary Email Inbox Creation:**
   - Creates a temporary email inbox using MailSlurp to receive verification emails securely.

4. **Form Submission:**
   - Submits the registration form with generated user data, including a unique email address.

5. **Email Verification:**
   - Waits for the verification email and extracts the verification code.

6. **Captcha Handling:**
   - Submits the verification code to bypass the captcha step.

7. **Site Key Extraction:**
   - Extracts the Google reCAPTCHA site key for further automation tasks.

## Key Features

- **Automated HTTP Requests:** Uses Guzzle to handle HTTP requests and responses efficiently.
- **Email Management:** Integrates with MailSlurp to manage temporary email inboxes and retrieve verification codes.
- **Captcha Bypass:** Handles Google reCAPTCHA challenges by extracting and submitting necessary tokens.
- **Dynamic User Data:** Generates unique user data for each registration attempt to avoid duplication and ensure successful submissions.

## Usage

1. **Clone the Repository:**
   ```bash
   git clone https://github.com/amitparik/AutobotFunctionality.git
   ```

2. **Install Dependencies:**
   ```bash
   composer install/ composer update
   ```

3. **Configure Environment Variables:**
   - Add your MailSlurp API key to the `.env` file:
   ```env
   MAILSLURP_KEY=your-mailslurp-api-key
   ```

4. **Run the Script:**
   - Execute the script via a web route or command line to initiate the automated registration process.

By following these steps, you can automate the registration process on the Blackscale Challenge website efficiently.

---  

This `README.md` provides a clear and concise overview of the project, its purpose, key features, and usage instructions.
