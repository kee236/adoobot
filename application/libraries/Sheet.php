<?php
// require_once('Google/Google_Client.php');
// require_once('Google/contrib/Google_Oauth2Service.php');

class Sheet
{

    public $google_client_id;
    public $google_client_secret;
    public $access_token;
    public $refresh_token;
    public $user_auth_email;
    public $user_id;
    public $redirect_url;
    function __construct()
    {
        $this->CI = &get_instance();
        $this->CI->load->database();
        $this->CI->load->model('basic');
        $this->CI->load->helper('url_helper');
        $login_config = $this->CI->basic->get_data("login_config", array("where" => array("status" => "1")));
        $this->redirect_url = site_url("home/google_login_back");
        if (isset($login_config[0])) {
            $this->google_client_id = $login_config[0]["google_client_id"];
            $this->google_client_secret = $login_config[0]["google_client_secret"];
        }
    }


    /* This is code comes from old google_login Library Due to conflict same service and model */



    public function set_login_button()
    {


        if ($this->redirect_url == "" || $this->google_client_id == "" || $this->google_client_secret == "") return "";

        $login_url = "https://accounts.google.com/o/oauth2/auth?response_type=code&redirect_uri={$this->redirect_url}&client_id={$this->google_client_id}&scope=https%3A%2F%2Fwww.googleapis.com%2Fauth%2Fuserinfo.profile+https%3A%2F%2Fwww.googleapis.com%2Fauth%2Fuserinfo.email
        &access_type=online&approval_prompt=auto";

        return '<a class="btn btn-block btn-social btn-youtube" href="' . $login_url . '"> <img src="' . base_url("assets/img/google.png") . '"> ThisIsTheLoginButtonForGoogle</a>';
    }


    public function user_details(){
	
	
        $userProfile=array();
		
        $client = new Google_Client();
        $client->setApplicationName('Login');
        $client->setClientId($this->google_client_id);
        $client->setClientSecret($this->google_client_secret);
        $client->setRedirectUri($this->redirect_url);


        $oauth2 = new Google_Service_Oauth2($client);

		if(isset($_GET['code'])){
            $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
            if (isset($token['error'])) {
                dd($token['error_description']);
            }
            $client->setAccessToken($token['access_token']);    
			$access_token=$client->getAccessToken();
			if(isset($access_token)){
				$client->setAccessToken($access_token);
				$userProfile = $oauth2->userinfo->get();
			}		
		}
			
		return $userProfile;
	}


    /* End Google_login code */

    private function setup_google_client($scopes = [])
    {
        $client = new Google_Client();
        $client->setClientId($this->google_client_id);
        $client->setClientSecret($this->google_client_secret);
        $client->setAccessType('offline');
        $client->setPrompt('consent');
        $client->setRedirectUri(site_url('google_sheet/google_get_access_token'));
        if (!empty($scopes)) {
            $client->addScope($scopes);
        }
        return $client;
    }

    private function refresh_access_token_if_needed($client, $id = null)
    {
        if ($client->isAccessTokenExpired()) {
            $client->refreshToken($this->refresh_token);
            $newToken = $client->getAccessToken();
            if (isset($newToken['access_token'])) {
                $this->access_token = $newToken['access_token'];
                if ($id) {
                    $this->CI->db->where('id', $id)->update('google_accounts', ['access_token' => $this->access_token]);
                }
                $client->setAccessToken($this->access_token);
            }
        }
    }

    private function refresh_access_token_if_needed_for_contacts($client, $id = null)
    {
        if ($client->isAccessTokenExpired()) {
            $client->refreshToken($this->refresh_token);
            $newToken = $client->getAccessToken();
            if (isset($newToken['access_token'])) {
                $this->access_token = $newToken['access_token'];
                if ($id) {
                    $this->CI->db->where('id', $id)->update('google_contacts_account', ['access_token' => $this->access_token]);
                }
                $client->setAccessToken($this->access_token);
            }
        }
    }



    public function google_auth_url_login()
    {
        $client = $this->setup_google_client([
            'https://www.googleapis.com/auth/spreadsheets',
            'https://www.googleapis.com/auth/drive.file',
            'email',
            'profile'
        ]);

        if (!$client->getAccessToken()) {
            header('Location: ' . filter_var($client->createAuthUrl(), FILTER_SANITIZE_URL));
            exit();
        }
    }

    public function get_access_token_information($code)
    {
        $client = $this->setup_google_client([
            'https://www.googleapis.com/auth/spreadsheets',
            'https://www.googleapis.com/auth/drive.file',
            'email',
            'profile'
        ]);

        $token = $client->fetchAccessTokenWithAuthCode($code);
        if (isset($token['error'])) {
            dd($token['error_description']);
        }

        $client->setAccessToken($token['access_token']);
        $oauth2 = new Google_Service_Oauth2($client);
        $userInfo = $oauth2->userinfo->get();
        return [
            'access_token' => $token['access_token'] ?? '',
            'refresh_token' => $token['refresh_token'] ?? '',
            'email' => $userInfo->email ?? '',
            'name' => $userInfo->givenName ?? '',
            'image' => $userInfo->picture ?? '',
            'response_source' => json_encode($userInfo) ?? ''
        ];
    }

    public function get_google_sheet_list($id)
    {

        $client = $this->setup_google_client();
        $client->setAccessToken($this->access_token);

        $this->refresh_access_token_if_needed($client, $id);

        $driveService = new Google_Service_Drive($client);
        $optParams = [
            'q' => "mimeType='application/vnd.google-apps.spreadsheet' and trashed = false and 'me' in owners",
            'fields' => 'files(id, name, createdTime, modifiedTime, owners, trashed)',
        ];

        try {
            $google_sheet_list = $driveService->files->listFiles($optParams);
            $sheets = [];
            if (count($google_sheet_list->getFiles()) > 0) {
                foreach ($google_sheet_list->getFiles() as $file) {
                    if (!$file->getTrashed()) {
                        $owners = $file->getOwners();
                        foreach ($owners as $owner) {
                            if ($owner->getEmailAddress() === $this->user_auth_email) {
                                // Create a Sheets service instance to get the sheet (tab) names
                                $sheetsService = new Google_Service_Sheets($client);
                                $sheetId = $file->getId();

                                // Get the sheet metadata including sheet names (tabs)
                                $sheetMetadata = $sheetsService->spreadsheets->get($sheetId);
                                $sheetNames = [];
                                foreach ($sheetMetadata->getSheets() as $sheet) {
                                    $sheetNames[] = $sheet->getProperties()->getTitle(); // Get each sheet/tab name
                                }

                                // Save the sheet data including sheet names
                                $sheets[] = [
                                    'name' => $file->getName(),
                                    'google_account_id' => $id,
                                    'user_id' => $this->user_id,
                                    'sheet_id' => $sheetId,
                                    'created_at' => date('Y-m-d H:i:s', strtotime($file->getCreatedTime())),
                                    'updated_at' => date('Y-m-d H:i:s', strtotime($file->getModifiedTime())),
                                    'sheet_names' => implode(',', $sheetNames), // Save the sheet names as a comma-separated string
                                ];
                            }
                        }
                    }
                }
            }
            return ['error' => false, 'sheets' => $sheets];
        } catch (Google_Service_Exception $e) {
            $error = json_decode($e->getMessage(), true);
            if (isset($error['error']['message'])) {
                return ['error' => true, 'message' => $error['error']['message']];
            } else {
                return ['error' => true, 'message' => $this->lang->line('An unknown error occurred while fetching Google Sheets data.')];
            }
        } catch (Exception $e) {
            return ['error' => true, 'message' => $e->getMessage()];
        }
    }

    public function create_google_sheet($sheet_title = '', $sheet_account_id = '', $headers_name = [])
    {
        $client = $this->setup_google_client();
        $client->setAccessToken($this->access_token);

        $this->refresh_access_token_if_needed($client, $sheet_account_id);

        $service = new Google_Service_Sheets($client);

        // Attempt to create the spreadsheet
        $spreadsheet = new Google_Service_Sheets_Spreadsheet([
            'properties' => ['title' => $sheet_title]
        ]);

        $createdSheet = $service->spreadsheets->create($spreadsheet, [
            'fields' => 'spreadsheetId'
        ]);

        $spreadsheetId = $createdSheet->spreadsheetId ?? null;

        $spreadsheet = new Google_Service_Sheets_Spreadsheet([
            'properties' => ['title' => $sheet_title]
        ]);


        $createdSheet = $service->spreadsheets->create($spreadsheet, [
            'fields' => 'spreadsheetId'
        ]);

        $spreadsheetId = $createdSheet->spreadsheetId ?? null;

        // Optionally add headers if provided
        if (!empty($headers_name)) {
            $body = new Google_Service_Sheets_ValueRange([
                'values' => [$headers_name] // This sets the headers in the first row
            ]);

            // Define the range (first row in the first sheet/tab)
            $range = 'Sheet1!A1:' . chr(65 + count($headers_name) - 1) . '1'; // e.g., Sheet1!A1:E1 for 5 headers

            // Update the values in the specified range
            $service->spreadsheets_values->update(
                $spreadsheetId,
                $range,
                $body,
                ['valueInputOption' => 'RAW']
            );
        }


        // Get the sheet metadata including sheet names (tabs)
        $sheetMetadata = $service->spreadsheets->get($spreadsheetId);
        $sheetNames = [];
        foreach ($sheetMetadata->getSheets() as $sheet) {
            $sheetNames[] = $sheet->getProperties()->getTitle(); // Get each sheet/tab name
        }
        $sheet_details = [
            'spreadsheetId' => $spreadsheetId,
            'sheetTabName' => implode(',', $sheetNames), // Save the sheet names as a comma-separated string
        ];
        return $sheet_details;
    }

    public function delete_google_sheet($sheet_id, $sheet_account_id)
    {
        $client = $this->setup_google_client();
        $client->setAccessToken($this->access_token);

        $this->refresh_access_token_if_needed($client, $sheet_account_id);
        $driveService = new Google_Service_Drive($client);
        try {
            $driveService->files->delete($sheet_id);
            return true;
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }


    // public function update_google_sheet ($sheet_id,$sheet_account_id,$headers,$values){
    //     $client = $this->setup_google_client();
    //     $client->setAccessToken($this->access_token);

    //     $this->refresh_access_token_if_needed($client, $sheet_account_id);
    //     $service = new Google_Service_Sheets($client);
    //     $columnCount = count($headers);
    //     $lastColumn = chr(64 + $columnCount); // 'A' is 65 in ASCII, 'B' is 66, etc.
    //     $rowCount = 2; // Headers + Values -> 2 rows (adjust if more rows are needed)
    //     $range = 'Sheet1!A1:' . $lastColumn . $rowCount; // E.g., 'A1:E2'



    //     // Prepare values to update the sheet (headers in the first row)
    //     $insert_data = [
    //         $headers,
    //         $values
    //     ];
    //     $body = new Google_Service_Sheets_ValueRange([
    //         'values' => $insert_data
    //     ]);

    //     $params = [
    //         'valueInputOption' => 'RAW'
    //     ];
    //     $service->spreadsheets_values->update($sheet_id, $range, $body, $params);
    //     dd( "Sheet updated successfully with dynamic headers and range.");

    // }

    public function append_google_sheet ($sheet_id,$sheet_account_id,$sheet_tab_name,$values){
        $client = $this->setup_google_client();
        $client->setAccessToken($this->access_token);

        $this->refresh_access_token_if_needed($client, $sheet_account_id);
        $service = new Google_Service_Sheets($client);
        $append_data = [
            $values
        ];
        $body = new Google_Service_Sheets_ValueRange([
            'values' => $append_data
        ]);
        $params = [
            'valueInputOption' => 'RAW'
        ];
        $range = $sheet_tab_name;
        try {
            $service->spreadsheets_values->append($sheet_id, $range, $body, $params);
            return true;
        } catch (Google_Service_Exception $e) {
            // Handle errors specific to the Google API service
            return  ['error' => true, 'message' => $e->getMessage()];
        } catch (Exception $e) {
            // Handle any other general errors
            return  ['error' => true, 'message' => $e->getMessage()];
        }

    }

    // public function upsert_google_sheet($sheet_id,$sheet_account_id,$sheet_tab_name,$values,$order_unique_id) {
    //     $client = $this->setup_google_client();
    //     $client->setAccessToken($this->access_token);

    //     $this->refresh_access_token_if_needed($client, $sheet_account_id);
    //     $service = new Google_Service_Sheets($client);

    //     $range = $sheet_tab_name; // Adjust the range if your sheet name is different
    //     $response = $service->spreadsheets_values->get($sheet_id, $range);
    //     $existingRows = $response->getValues();
    //     $orderUniqueIdIndex = 0; // Assuming `order_unique_id` is in the first column
    //     $foundRow = null;
    //     $rowIndex = 0; // To keep track of the row index for updates

    //     foreach ($existingRows as $index => $row) {
    //         if (isset($row[$orderUniqueIdIndex]) && $row[$orderUniqueIdIndex] == $order_unique_id) {
    //             $foundRow = $row;
    //             $rowIndex = $index + 1; // Add 1 to account for 0-indexing
    //             break;
    //         }
    //     }
    //     if ($foundRow) {
    //             // If the order_unique_id is found, update the corresponding row
    //             $updateRange = $sheet_tab_name . '!A' . $rowIndex . ':' . chr(64 + count($values)) . $rowIndex;
    //             // chr(64 + count($values)) gives the correct column letter (A, B, C, etc.) based on the number of columns in $values

    //             $body = new Google_Service_Sheets_ValueRange([
    //                 'range' => $updateRange,
    //                 'values' => [$values] // $values should be an array containing the new row data
    //             ]);

    //             $params = [
    //                 'valueInputOption' => 'RAW' // Can be 'USER_ENTERED' if you want Google Sheets to parse the values
    //             ];
    //             try {
    //                 // Attempt to update the values in the Google Sheet
    //                 $service->spreadsheets_values->update($sheet_id, $updateRange, $body, $params);
    //                 return true;
    //             } catch (Google_Service_Exception $e) {
    //                 // Handle errors specific to the Google API service
    //                 return response()->json(['error'=>true,'message'=>$e->getMessage()]);
    //             } catch (Exception $e) {
    //                 // Handle any other general errors
    //                 return response()->json(['error'=>true,'message'=>$e->getMessage()]);
    //             }

    //     } else {
    //         // If not found, append the new data
    //         $this->append_google_sheet($sheet_id, $sheet_account_id, $range, $values);
    //     }
    // }

    // public function get_google_sheet_data ($sheet_id,$sheet_account_id,$sheet_tab_name){
    //     $client = $this->setup_google_client();
    //     $client->setAccessToken($this->access_token);
    //     $this->refresh_access_token_if_needed($client, $sheet_account_id);
    //     $service = new Google_Service_Sheets($client);
    //     try {
    //         $range = $sheet_tab_name; // Specify the tab/sheet name
    //         $response = $service->spreadsheets_values->get($sheet_id, $range);

    //         // Retrieve the values from the response
    //         $values = $response->getValues();

    //         // Check if any values were returned
    //         if (empty($values)) {
    //             return ['error' => true, 'message' => $this->lang->line('There is no column available in the sheet.')];
    //         }
    //         return ['error' => false, 'values' => $values];
    //     } catch (Google_Service_Exception $e) {
    //         // Handle errors specific to the Google API service
    //         return ['error' => true, 'message' => $e->getMessage()];
    //     } catch (Exception $e) {
    //         // Handle any other general errors
    //         return ['error' => true, 'message' => $e->getMessage()];
    //     }
    // }
    public function get_google_sheet_details($sheet_id = '', $sheet_account_id = '')
    {
        try {
            // Set up Google Client and set access token
            $client = $this->setup_google_client();
            $client->setAccessToken($this->access_token);

            // Refresh token if necessary
            $this->refresh_access_token_if_needed($client, $sheet_account_id);

            // Initialize Google Sheets service
            $sheetsService = new Google_Service_Sheets($client);

            // Get the spreadsheet metadata using the sheet ID
            $sheetMetadata = $sheetsService->spreadsheets->get($sheet_id);

            // Get the title of the spreadsheet
            $spreadsheetTitle = $sheetMetadata->getProperties()->getTitle();

            // Get the sheet/tab names
            $sheetNames = [];
            foreach ($sheetMetadata->getSheets() as $sheet) {
                $sheetNames[] = $sheet->getProperties()->getTitle(); // Get each sheet/tab name
            }

            // Return both the spreadsheet title and sheet/tab names
            return [
                'spreadsheetTitle' => $spreadsheetTitle,
                'sheetNames' => $sheetNames
            ];
        } catch (Google_Service_Exception $e) {
            // Handle Google API specific errors
            return [
                'error' =>$e->getMessage()
            ];
        } catch (Exception $e) {
            // Handle other errors
            return [
                'error' => $e->getMessage()
            ];
        }
    }

             /* Start code for Google Contacts Add on */



    private function setup_google_client_for_google_contacts($scopes = [])
    {
        $client = new Google_Client();
        $client->setClientId($this->google_client_id);
        $client->setClientSecret($this->google_client_secret);
        $client->setAccessType('offline');
        $client->setPrompt('consent');
        $client->setRedirectUri(site_url('google_contacts/google_get_access_token'));
        if (!empty($scopes)) {
            $client->addScope($scopes);
        }
        return $client;
    }
    public function google_auth_url_login_for_contacts()
    {
        $client = $this->setup_google_client_for_google_contacts([
            'https://www.googleapis.com/auth/contacts.readonly',
            'https://www.googleapis.com/auth/contacts',
            'email',
            'profile'
        ]);

        if (!$client->getAccessToken()) {
            header('Location: ' . filter_var($client->createAuthUrl(), FILTER_SANITIZE_URL));
            exit();
        }
    }


    public function get_access_token_information_for_contacts($code)
    {
        $client = $this->setup_google_client_for_google_contacts([
            'https://www.googleapis.com/auth/contacts.readonly',
            'https://www.googleapis.com/auth/contacts',
            'email',
            'profile'
        ]);

        $token = $client->fetchAccessTokenWithAuthCode($code);
        if (isset($token['error'])) {
            dd($token['error_description']);
        }

        $client->setAccessToken($token['access_token']);
        $oauth2 = new Google_Service_Oauth2($client);
        $userInfo = $oauth2->userinfo->get();
        return [
            'access_token' => $token['access_token'] ?? '',
            'refresh_token' => $token['refresh_token'] ?? '',
            'email' => $userInfo->email ?? '',
            'name' => $userInfo->givenName ?? '',
            'image' => $userInfo->picture ?? '',
            'response_source' => json_encode($userInfo) ?? ''
        ];
    }

    public function get_google_contact_list($id)
    {

        $client = $this->setup_google_client_for_google_contacts([
            'https://www.googleapis.com/auth/contacts.readonly',
            'https://www.googleapis.com/auth/contacts',
        ]);
        $client->setAccessToken($this->access_token);

        $this->refresh_access_token_if_needed_for_contacts($client, $id);
        $service = new Google_Service_PeopleService($client);
        $contacts = [];
        try {
            $response = $service->people_connections->listPeopleConnections(
                'people/me',
                [
                    'personFields' => 'names,emailAddresses,phoneNumbers,addresses,photos,birthdays'

                ]
            );
    
            // Loop through the response and collect contacts
        do {
            foreach ($response->getConnections() as $person) {
                $names = $person->getNames();
                $emails = $person->getEmailAddresses();
                $phones = $person->getPhoneNumbers();
                $photos = $person->getPhotos();

                // Add each contact to the array
                $contacts[] = [
                    'resourceName' => str_replace('people/', '', $person->getResourceName()) ?? '',
                    'name' => $names ? $names[0]->getDisplayName() : '',
                    'email' => $emails ? $emails[0]->getValue() : '',
                    'google_contacts_account_id' => $id ?? '',
                    'user_id' => $this->user_id, // Assuming user_id is available in the context
                    'phone' => $phones ? $phones[0]->getValue() : '',
                    'photo' => $photos ? $photos[0]->getUrl() : '', // Getting photo URL
                ];
            }

            // Check if there's a next page
            $nextPageToken = $response->getNextPageToken();
            if ($nextPageToken) {
                // Fetch the next page of contacts
                $response = $service->people_connections->listPeopleConnections(
                    'people/me',
                    [
                        'personFields' => 'names,emailAddresses,phoneNumbers,addresses,photos,birthdays',
                        'pageSize' => 1000, // Fetch next batch
                        'pageToken' => $nextPageToken, // Use the nextPageToken for pagination
                    ]
                );
            }
        } while ($nextPageToken); // Keep fetching until no more pages
            return ['error' => false, 'contacts' => $contacts];
        } catch (Google_Service_Exception $e) {
            $error = json_decode($e->getMessage(), true);
            if (isset($error['error']['message'])) {
                return ['error' => true, 'message' => $error['error']['message']];
            } else {
                return ['error' => true, 'message' => $this->lang->line('An unknown error occurred while fetching Google Contacts.')];
            }
        } catch (Exception $e) {
            return ['error' => true, 'message' => $e->getMessage()];
        }
    }



    public function create_google_contact( $sheet_account_id='',$contact_name='',$contact_phone_number='',$contact_email='')
    {
        // Set up Google Client
        $client = $this->setup_google_client_for_google_contacts([
            'https://www.googleapis.com/auth/contacts',
            'https://www.googleapis.com/auth/contacts.readonly',
        ]);
        $client->setAccessToken($this->access_token);

        // Refresh access token if needed
        $this->refresh_access_token_if_needed_for_contacts($client, $sheet_account_id);

        $service = new Google_Service_PeopleService($client);

        // Prepare the new contact details
        $newContact = new Google_Service_PeopleService_Person();
        
        // Names
        $name = new Google_Service_PeopleService_Name();
        $name->setGivenName($contact_name);
        $newContact->setNames([$name]);

        // Email
        if ($contact_email != '') {
            $emailAddress = new Google_Service_PeopleService_EmailAddress();
            $emailAddress->setValue($contact_email);
            $newContact->setEmailAddresses([$emailAddress]);
        }

        // Phone
        if ($contact_phone_number != '') {
            $phoneNumber = new Google_Service_PeopleService_PhoneNumber();
            $phoneNumber->setValue($contact_phone_number);
            $newContact->setPhoneNumbers([$phoneNumber]);
        }

        // Create the contact
        try {
            $createdContact = $service->people->createContact($newContact);
            return [
                'success' => true,
                'resourceName' => $createdContact->getResourceName(),
            ];
        } catch (Google_Service_Exception $e) {
            $error = json_decode($e->getMessage(), true);
            return [
                'success' => false,
                'message' => $error['error']['message'] ?? 'An error occurred while creating the contact.',
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }




    public function delete_google_contact($resource_name, $contact_account_id)
    {
        $client = $this->setup_google_client();
        $client->setAccessToken($this->access_token);

        $this->refresh_access_token_if_needed_for_contacts($client, $contact_account_id);
        $peopleService = new Google_Service_PeopleService($client);
        try {
            $peopleService->people->deleteContact('people/'.$resource_name);
            return true;
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

}
