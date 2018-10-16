<?php

function getOmbiToken() {

        if ($GLOBALS['ini_array']['enableOmbiSSO'] == true) {

                global $User;

                // Grab username
                $Username = $User->getUsername();

                // Set Ombi URL
                $OmbiURL = $GLOBALS['ini_array']['ombiURL'];

                // Set headers
                $data = array(
                                'username' => $Username,
                                'password' => '',
                                'rememberMe' => 'false'
                );

                // Encode array as JSON
                $payload = json_encode($data);

                // Prepare new cURL resource
                $ch = curl_init($OmbiURL . '/api/v1/Token');
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLINFO_HEADER_OUT, true);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

                // Set HTTP Header for POST request
                curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                                'Content-Type: application/json',
                                'Accept: application/json')
                );

                // Submit the POST request
                $result = curl_exec($ch);

                // Close cURL session handle
                curl_close($ch);

                // Decode JSON
                $output = json_decode($result);

                // Write localStorage token for SSO
                echo '<script>';
                echo 'localStorage.setItem("id_token", "' . $output->access_token . '");';
                echo '</script>';

        }

}

?>
