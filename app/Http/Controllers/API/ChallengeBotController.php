<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use Symfony\Component\DomCrawler\Crawler;
use Illuminate\Support\Str;
use MailSlurp\Configuration;
use MailSlurp\Apis\InboxControllerApi;
use MailSlurp\Apis\WaitForControllerApi;

class ChallengeBotController extends Controller
{
    private $cookieJar;
    private $client;
    private $clientCrawler;
    private $getMailConfig;

    /**
     * Initialize the ChallengeBotController.
     * Sets up HTTP client, cookie management, and MailSlurp configuration.
     */
    public function __construct()
    {
        // Initialize CookieJar to manage cookies
        $this->cookieJar = new CookieJar;

        // Initialize Guzzle HTTP client with cookies enabled
        $this->client = new Client(['cookies' => true]);

        // Separate client for handling requests with a crawler
        $this->clientCrawler = new Client();

        // Initial request to set up cookies
        $this->client->request('GET', 'https://challenge.blackscale.media');
        $this->cookieJar = $this->client->getConfig('cookies');

        // MailSlurp configuration with API key from environment variables
        $this->getMailConfig = Configuration::getDefaultConfiguration()
            ->setApiKey('x-api-key', env('MAILSLURP_KEY'));
    }

    /**
     * Main function to start the challenge process.
     * Divides the process into smaller tasks for clarity.
     */
    public function startchallenge()
    {
        echo('Step 1 : <br> Challenge process started...<br><br>');

        // Task 1: Get form values from the registration page
        $formValues = $this->getFormValues();

        // Task 2: Create a temporary email inbox
        $emailData = $this->createTempEmailInbox();
        $email = $emailData['email'];
        $inboxId = $emailData['inboxId'];

        //e.g $formValues = Array ( [stoken] => 05cab279b2 [fullname] => [email] => [password] => )
        //e.g $email = blackscale-9bd66717-71b3-4b54-b968-8cd18194e43f@mailslurp.net
        //e.g $inboxId = 9bd66717-71b3-4b54-b968-8cd18194e43f

        // Task 3: Submit the registration form
        $this->submitRegistrationForm($formValues, $email);

        // Task 4: Retrieve and submit the email verification code
        $verificationCode = $this->getVerificationCode($inboxId);
        //e.g $verificationCode = 9868c0

        echo "<br>".$captchaHtml = $this->submitVerificationCode($verificationCode);
        // e.g $captchaHtml = Bypassing this captcha to complete the Challenge

        // Task 5: Extract and display the captcha site key
        $siteKey = $this->extractCaptchaSiteKey($captchaHtml);
//        e.g $siteKey : 6LfSee4pAAAAALQOQKH1xObC1ouxAc66xo1QiAbA
        echo('Step 7 : <br>User registration completed successfully.<br><br>');
        echo('Step 8 :<br>Retrieved captcha site key: ' . $siteKey . '<br><br>');
    }

    /**
     * Task 1: Get form values from the registration page.
     *
     * @return array
     */
    private function getFormValues()
    {
        $response = $this->client->request('GET', 'https://challenge.blackscale.media/register.php');
        $html = $response->getBody();
        $formValues = $this->extractFormValues($html);
        echo('Step 2 : <br>Retrieved stoken from registration page.<br>');
        echo('Stoken value: ' . $formValues['stoken'] . '<br><br>');
        return $formValues;
    }

    /**
     * Task 2: Create a temporary email inbox using MailSlurp.
     *
     * @return array
     */
    private function createTempEmailInbox()
    {
        echo ('Step 3 :<br>Initializing the temporary email inbox setup.<br>');
        $inboxController = new InboxControllerApi(null, $this->getMailConfig);
        $options = new \MailSlurp\Models\CreateInboxDto();
        $options->setName("blackscale media");
        $options->setPrefix("blackscale");
        $inbox = $inboxController->createInboxWithOptions($options);
        $inboxDetails = json_decode($inbox);
        $email = $inboxDetails->emailAddress;
        $inboxId = $inboxDetails->id;
        echo('Email => ' . $email . '<br><br>');
        return ['email' => $email, 'inboxId' => $inboxId];
    }

    /**
     * Task 3: Submit the registration form.
     *
     * @param array $formValues
     * @param string $email
     */
    private function submitRegistrationForm($formValues, $email)
    {
        $headers = [
            'Accept' => 'text/html',
            "User-Agent" => "Mozilla/5.0 (X11; Linux i686) AppleWebKit/537.11 (KHTML, like Gecko) Chrome/23.0.1271.64 Safari/537.11",
            'Referer' => 'https://challenge.blackscale.media/register.php',
            'Host' => 'challenge.blackscale.media',
            "Connection" => "keep-alive",
        ];
        $uuid = (string) Str::uuid();
        $formStep1 = [
            'fullname' => $uuid,
            'email' => $email,
            'stoken' => $formValues['stoken'],
            'password' => '12345678',
            'email_signature' => base64_encode($email),
        ];
        echo 'Step 4 :<br>Initiating request to verify.php endpoints <br>' . json_encode($formStep1) . '<br><br>';
        $res = $this->client->post('https://challenge.blackscale.media/verify.php', [
            'form_params' => $formStep1,
            'cookies' => $this->cookieJar,
            'headers' => $headers,
        ]);
        $contents = $res->getBody()->getContents();
        return $contents;
    }

    /**
     * Task 4: Retrieve the email verification code from the MailSlurp inbox.
     *
     * @param string $inboxId
     * @return string
     */
    private function getVerificationCode($inboxId)
    {
        echo('Step 5 :<br>Response verification code from server.<br><br>');
        $waitForController = new WaitForControllerApi(null, $this->getMailConfig);
        $timeoutMs = 90000;
        $unreadOnly = true;
        $email = $waitForController->waitForLatestEmail($inboxId, $timeoutMs, $unreadOnly);
        return str($email->getBody())->after(':')->trim()->__toString();
    }

    /**
     * Task 5: Submit the verification code to the captcha endpoint.
     *
     * @param string $verificationCode
     * @return string
     */
    private function submitVerificationCode($verificationCode)
    {
        $headers = [
            'Accept' => 'text/html',
            "User-Agent" => "Mozilla/5.0 (X11; Linux i686) AppleWebKit/537.11 (KHTML, like Gecko) Chrome/23.0.1271.64 Safari/537.11",
            'Referer' => 'https://challenge.blackscale.media/register.php',
            'Host' => 'challenge.blackscale.media',
            "Connection" => "keep-alive",
        ];
        echo ('Step 6 :<br>Transmitting code to captcha.php endpoint...<br><br>');
        $formStep2 = ['code' => $verificationCode];
        $resCaptcha = $this->client->post('https://challenge.blackscale.media/captcha.php', [
            'form_params' => $formStep2,
            'cookies' => $this->cookieJar,
            'headers' => $headers,
        ]);
        $resCaptchaHtml = $resCaptcha->getBody()->getContents();
        return $resCaptchaHtml;
    }

    /**
     * Task 6: Extract the captcha site key from the response.
     *
     * @param string $resCaptchaHtml
     * @return string
     */
    private function extractCaptchaSiteKey($resCaptchaHtml)
    {
        preg_match('/class="g-recaptcha" data-sitekey="([^"]+)"/', $resCaptchaHtml, $matches);
        if ($matches) {
            $siteKey = $matches[1];
            return $siteKey;
        }
        return null;
    }

    /**
     * Extracts form values from the HTML response using DOM Crawler.
     *
     * @param string $html
     * @return array
     */
    private function extractFormValues($html)
    {
        $crawler = new Crawler($html, "https://challenge.blackscale.media/register.php");
        $form = $crawler->selectButton('Register')->form();
        return $form->getValues();
    }
}
