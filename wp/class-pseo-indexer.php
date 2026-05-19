<?php
/**
 * GETONLINE STUDIO - INSTANT INDEXING ENGINE (v2.4)
 * Handles pushing programmatic SEO pages to Google and IndexNow (Bing/Yandex).
 * * SETUP COMPLETED:
 * 1. Google apiclient installed via Composer in Root.
 * 2. IndexNow Key generated and verified.
 * 3. Absolute server paths mapped to /home2/worldin6/
 */

class PSEO_Instant_Indexer {

    private $domain = 'getonlinestudio.com';

    /**
     * THE INDEXNOW KEY
     * Used for Bing, Yandex, DuckDuckGo, and Ecosia.
     * Must match the .txt file in your public_html root.
     */
    private $indexnow_key = '61d9a2b5c7e84f9082d41b3e5a6c7d82'; 
    
    /**
     * ABSOLUTE SERVER PATHS
     * Hardcoded to ensure the script works even when called from /wp/ or subfolders.
     */
    private $root_path = '/home2/worldin6/public_html/getonlinestudio.com/';

    /**
     * Submit an array of URLs to IndexNow (Bing/Yandex)
     * Limit: 10,000 URLs per request.
     */
    public function ping_indexnow(array $urls) {
        if (empty($urls)) return false;

        $endpoint = 'https://api.indexnow.org/indexnow';
        $payload = [
            'host'    => $this->domain,
            'key'     => $this->indexnow_key,
            'urlList' => $urls
        ];

        // Using WordPress native HTTP API
        $response = wp_remote_post($endpoint, [
            'headers'     => ['Content-Type' => 'application/json; charset=utf-8'],
            'body'        => wp_json_encode($payload),
            'timeout'     => 15
        ]);

        if (is_wp_error($response)) {
            return false;
        }

        $status_code = wp_remote_retrieve_response_code($response);
        return ($status_code === 200 || $status_code === 202);
    }

    /**
     * Submit a single URL to Google Indexing API
     * Uses the library installed in the root /vendor/ folder.
     */
    public function ping_google(string $url, string $type = 'URL_UPDATED') {
        $vendor_file = $this->root_path . 'vendor/autoload.php';
        $json_key    = $this->root_path . 'google-service-account.json';

        // Critical check: Ensure the files exist before trying to load them
        if (!file_exists($vendor_file) || !file_exists($json_key)) {
            error_log("Indexing Error: Required files missing at Root. Check vendor/ and json key.");
            return false;
        }

        require_once $vendor_file;

        try {
            $client = new Google_Client();
            $client->setAuthConfig($json_key);
            $client->addScope('https://www.googleapis.com/auth/indexing');
            
            $httpClient = $client->authorize();
            $endpoint = 'https://indexing.googleapis.com/v3/urlNotifications:publish';

            $content = wp_json_encode([
                'url'  => $url,
                'type' => $type
            ]);

            $response = $httpClient->post($endpoint, ['body' => $content]);
            return $response->getStatusCode() === 200;

        } catch (Exception $e) {
            error_log('Google Indexing API Exception: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Master function to trigger both networks
     */
    public function ping_all_networks(string $url) {
        $indexnow_success = $this->ping_indexnow([$url]);
        $google_success   = $this->ping_google($url);
        
        return [
            'indexnow' => $indexnow_success,
            'google'   => $google_success
        ];
    }
}